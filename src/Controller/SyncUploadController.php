<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nxcore/
 * @link      https://gitlab.com/nx-core/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

/** @noinspection DuplicatedCode */

namespace App\Controller;

use App\AppConstants;
use App\Entity\Patient;
use App\Entity\PatientCredentials;
use App\Entity\SyncQueue;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\CommsManager;
use App\Transform\Fitbit\Constants;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class SyncUploadController
 *
 * @package App\Controller
 */
class SyncUploadController extends AbstractController
{
    /**
     * @param Request $request
     */
    private function postToMirror(Request $request)
    {
        $ch = curl_init($_ENV['MESH_MIRROR'] . $request->getRequestUri());
        if ($request->getContentType() == "json") {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getContent());
//        curl_exec($ch);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        if (!empty($response)) {
            $response = json_decode($response);
            if ($response->success) {
                AppConstants::writeToLog('mirror.txt',
                    '[' . $_ENV['MESH_MIRROR'] . '] Accepted the post to ' . $request->getRequestUri());
            } else {
                if ($response->status != 200) {
                    AppConstants::writeToLog('mirror.txt',
                        '[' . $_ENV['MESH_MIRROR'] . '] Failed to accept the post to ' . $request->getRequestUri());
                    AppConstants::writeToLog('mirror.txt',
                        '[' . $_ENV['MESH_MIRROR'] . '] responded with ' . json_encode($response));
                } else {
                    AppConstants::writeToLog('mirror.txt',
                        '[' . $_ENV['MESH_MIRROR'] . '] responded with ' . $response->status);
                }
            }
        } else {
            AppConstants::writeToLog('mirror.txt',
                '[' . $_ENV['MESH_MIRROR'] . '] sent an empty responce');
        }

        curl_close($ch);
    }

    /**
     * @Route("/sync/upload/{service}/{data_set}", name="sync_upload_post", methods={"POST"})
     * @param String          $service
     * @param String          $data_set
     *
     * @param LoggerInterface $logger
     *
     * @param AwardManager    $awardManager
     *
     * @param ChallengePve    $challengePve
     * @param CommsManager    $commsManager
     *
     * @return JsonResponse
     */
    public function index_post(
        string $service,
        string $data_set,
        LoggerInterface $logger,
        AwardManager $awardManager,
        ChallengePve $challengePve,
        CommsManager $commsManager
    ) {
        $request = Request::createFromGlobals();

        if ($service == "samasung") {
            $service = "SamsungHealth";
        }

        if ($data_set != "count_daily_steps" &&
            $data_set != "tracking_devices" &&
            $data_set != "intraday_steps" &&
            $data_set != "count_daily_floors" &&
            $data_set != "water_intakes" &&
            $data_set != "caffeine_intakes" &&
            $data_set != "body_weights" &&
            $data_set != "body_fats" &&
            $data_set != "exercises" &&
            $data_set != "count_daily_calories" &&
            $data_set != "count_daily_distances" &&
            $data_set != "food" &&
            $data_set != "food_intake" &&
            $data_set != "food_info") {
            AppConstants::writeToLog($service . '_' . $data_set . '.txt', $request->getContent());
        }

        $transformerClassName = 'App\\Transform\\' . $service . '\\Entry';
        if (!class_exists($transformerClassName)) {
            $logger->error('I could not find a Entry class for ' . $service);
            $savedId = -2;
        } else {
            if (!is_null($this->getUser()) && is_object($this->getUser()) && get_class($this->getUser()) == "App\Entity\Patient") {
                $transformerClass = new $transformerClassName($logger, $this->getUser());
            } else {
                $transformerClass = new $transformerClassName($logger);
            }
            $savedId = $transformerClass->transform($data_set, $request->getContent(), $this->getDoctrine(),
                $awardManager, $challengePve, $commsManager);
        }

        if (array_key_exists("MESH_MIRROR", $_ENV)) {
            $this->postToMirror($request);
        }

        if (is_array($savedId)) {
            return $this->json([
                'success' => true,
                'status' => 200,
                'message' => "Saved multiple '$service/$data_set' entitles",
                'entity_id' => $savedId,
            ]);
        } else {
            if ($savedId > 0) {
                return $this->json([
                    'success' => true,
                    'status' => 200,
                    'message' => "Saved '$service/$data_set' as " . $savedId,
                    'entity_id' => $savedId,
                ]);
            } else {
                if ($savedId == -1) {
//                    AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__);
                    return $this->json([
                        'success' => false,
                        'status' => 500,
                        'message' => "Unable to save entity",
                    ]);
                } else {
                    if ($savedId == -2) {
//                        AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__);
                        return $this->json([
                            'success' => false,
                            'status' => 500,
                            'message' => "Unknown service '$service'",
                        ]);
                    } else {
                        if ($savedId == -3) {
//                            AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__);
                            return $this->json([
                                'success' => false,
                                'status' => 500,
                                'message' => "Unknown data set '$data_set' in '$service'",
                            ]);
                        } else {
//                            AppConstants::writeToLog('debug_transform.txt',
//                                'I\'m about to respond with a 500 error after ' . $data_set);
//                            AppConstants::writeToLog('debug_transform.txt',
//                                $request->getContent());

                            return $this->json([
                                'success' => false,
                                'status' => 500,
                                'message' => "Unknown error, saved is '" . $savedId . "'",
                            ]);
                        }
                    }
                }
            }
        }

    }

    /**
     * @Route("/sync/webhook/{service}", name="sync_webhook_get", methods={"GET"})
     * @param String          $service
     *
     * @param LoggerInterface $logger
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function sync_webhook_get(string $service, LoggerInterface $logger)
    {
        $request = Request::createFromGlobals();

        if (array_key_exists("MESH_MIRROR", $_ENV)) {
            if (is_array($_GET) && array_key_exists("verify", $_GET)) {
                AppConstants::writeToLog('debug_transform.txt',
                    __CLASS__ . '::' . __FUNCTION__ . '|' . __LINE__ . ":: Skiiping verify posts");
            } else {
                $this->postToMirror(clone($request));
            }
        }

        if (strtolower($service) == "habitica") {
            $jsonContent = json_decode($request->getContent(), false);

            AppConstants::writeToLog('debug_transform.txt', '[webhook:' . $service . '] - ' . $jsonContent->user->_id);
            AppConstants::writeToLog('webhook_' . $service . '.txt', $request->getContent());

            $response = new JsonResponse();
            $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
            return $response;
        }

        if (is_array($_GET) && array_key_exists("verify", $_GET)) {
            if ($_GET['verify'] != "42277fd08d12f9f93fafafc59001f4f4c192eb4dd3619d1ba80ff52955a791ac") {
                AppConstants::writeToLog('debug_transform.txt',
                    '[webhook:' . $service . '] - Verification ' . $_GET['verify'] . ' code invalid');

                $response = new JsonResponse();
                $response->setStatusCode(JsonResponse::HTTP_NOT_FOUND);
                return $response;
            } else {
                AppConstants::writeToLog('debug_transform.txt',
                    '[webhook:' . $service . '] - Verification ' . $_GET['verify'] . ' code valid');
            }

            $response = new JsonResponse();
            $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
            return $response;
        }

        $jsonContent = json_decode($request->getContent(), false);

        $serviceObject = AppConstants::getThirdPartyService($this->getDoctrine(), "Fitbit");
        foreach ($jsonContent as $item) {
            $patient = $this->getDoctrine()
                ->getRepository(Patient::class)
                ->findOneBy(['id' => $item->subscriptionId]);

            if (!$patient) {
                AppConstants::writeToLog('debug_transform.txt',
                    '[webhook:' . $service . '] - No patient with this ID ' . $item->subscriptionId);
            } else {
                $queueEndpoints = Constants::convertSubscriptionToClass($item->collectionType);
                if (is_array($queueEndpoints)) {
                    $queueEndpoints = join("::", $queueEndpoints);
                }

                if (!is_null($queueEndpoints)) {
                    /** @var PatientCredentials $patientCredential */
                    $patientCredential = $this->getDoctrine()
                        ->getRepository(PatientCredentials::class)
                        ->findOneBy(["service" => $serviceObject, "patient" => $patient]);

                    $serviceSyncQueue = $this->getDoctrine()
                        ->getRepository(SyncQueue::class)
                        ->findOneBy([
                            'credentials' => $patientCredential,
                            'service' => $serviceObject,
                            'endpoint' => $queueEndpoints,
                        ]);

                    if (!$serviceSyncQueue) {
                        $serviceSyncQueue = new SyncQueue();
                        $serviceSyncQueue->setService($serviceObject);
                        $serviceSyncQueue->setDatetime(new DateTime());
                        $serviceSyncQueue->setCredentials($patientCredential);
                        $serviceSyncQueue->setEndpoint($queueEndpoints);

                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($serviceSyncQueue);
                        $entityManager->flush();
                    }
                }

            }

        }

        $response = new JsonResponse();
        $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
        return $response;
    }

    /**
     * @Route("/sync/webhook/{service}", name="sync_webhook_post", methods={"POST"})
     * @param String          $service
     *
     * @param LoggerInterface $logger
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function sync_webhook_post(string $service, LoggerInterface $logger)
    {
        return $this->sync_webhook_get($service, $logger);
    }
}
