<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
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
use App\Service\TweetManager;
use App\Transform\Fitbit\Constants;
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
     * @Route("/sync/webhook/{service}", name="sync_webhook_post", methods={"POST"})
     * @param String          $service
     *
     * @param LoggerInterface $logger
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function sync_webhook_post(String $service, LoggerInterface $logger)
    {
        return $this->sync_webhook_get($service, $logger);
    }

    /**
     * @Route("/sync/webhook/{service}", name="sync_webhook_get", methods={"GET"})
     * @param String          $service
     *
     * @param LoggerInterface $logger
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function sync_webhook_get(String $service, LoggerInterface $logger)
    {
        if (strtolower($service) == "habitica") {
            $request = Request::createFromGlobals();
            $jsonContent = json_decode($request->getContent(), FALSE);

            AppConstants::writeToLog('debug_transform.txt', '[webhook:' . $service . '] - ' . $jsonContent->user->_id);
            AppConstants::writeToLog('webhook_' . $service . '.txt', $request->getContent());

            $response = new JsonResponse();
            $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
            return $response;
        }

        if (is_array($_GET) && array_key_exists("verify", $_GET)) {
            if ($_GET['verify'] != "c9dc17fc026d90cf8ddb6d7e1960828962265bac03605449054fb2e9033c927c") {
                AppConstants::writeToLog('debug_transform.txt', '[webhook:' . $service . '] - Verification ' . $_GET['verify'] . ' code invalid');
                throw $this->createNotFoundException('404');
            } else {
                AppConstants::writeToLog('debug_transform.txt', '[webhook:' . $service . '] - Verification ' . $_GET['verify'] . ' code valid');
            }

            $response = new JsonResponse();
            $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
            return $response;
        }

        $request = Request::createFromGlobals();
        $jsonContent = json_decode($request->getContent(), FALSE);

        $serviceObject = AppConstants::getThirdPartyService($this->getDoctrine(), "Fitbit");
        foreach ($jsonContent as $item) {
            $patient = $this->getDoctrine()
                ->getRepository(Patient::class)
                ->findOneBy(['id' => $item->subscriptionId]);

            if (!$patient) {
                AppConstants::writeToLog('debug_transform.txt', '[webhook:' . $service . '] - No patient with this ID ' . $item->subscriptionId);
            } else {
                $queueEndpoints = Constants::convertSubscriptionToClass($item->collectionType);
                if (is_array($queueEndpoints)) {
                    $queueEndpoints = join("::", $queueEndpoints);
                }

                if (!is_null($queueEndpoints)) {
                    $patientCredential = $this->getDoctrine()
                        ->getRepository(PatientCredentials::class)
                        ->findOneBy(["service" => $serviceObject, "patient" => $patient]);

                    $serviceSyncQueue = $this->getDoctrine()
                        ->getRepository(SyncQueue::class)
                        ->findOneBy(['credentials' => $patientCredential, 'service' => $serviceObject, 'endpoint' => $queueEndpoints]);

                    if (!$serviceSyncQueue) {
                        $serviceSyncQueue = new SyncQueue();
                        $serviceSyncQueue->setService($serviceObject);
                        $serviceSyncQueue->setDatetime(new \DateTime());
                        $serviceSyncQueue->setCredentials($patientCredential);
                        $serviceSyncQueue->setEndpoint($queueEndpoints);

                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($serviceSyncQueue);
                        $entityManager->flush();

                        AppConstants::writeToLog('debug_transform.txt', '[webhook:' . $service . '] - Queue new ' . $item->collectionType . ' item for ' . $patient->getFirstName());
                    } else {
                        AppConstants::writeToLog('debug_transform.txt', '[webhook:' . $service . '] - Queue new ' . $item->collectionType . ' item for ' . $patient->getFirstName() . ' -- But already queued');
                    }
                }

            }

        }

        $response = new JsonResponse();
        $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
        return $response;
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
     * @param TweetManager    $tweetManager
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_post(String $service, String $data_set, LoggerInterface $logger, AwardManager $awardManager, ChallengePve $challengePve, TweetManager $tweetManager)
    {
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
            /** @noinspection PhpUndefinedMethodInspection */
            $savedId = $transformerClass->transform($data_set, $request->getContent(), $this->getDoctrine(), $awardManager, $challengePve, $tweetManager);
        }

        if (is_array($savedId)) {
            return $this->json([
                'success' => TRUE,
                'status' => 200,
                'message' => "Saved multiple '$service/$data_set' entitles",
                'entity_id' => $savedId,
            ]);
        } else if ($savedId > 0) {
            return $this->json([
                'success' => TRUE,
                'status' => 200,
                'message' => "Saved '$service/$data_set' as " . $savedId,
                'entity_id' => $savedId,
            ]);
        } else if ($savedId == -1) {
            return $this->json([
                'success' => FALSE,
                'status' => 500,
                'message' => "Unable to save entity",
            ]);
        } else if ($savedId == -2) {
            return $this->json([
                'success' => FALSE,
                'status' => 500,
                'message' => "Unknown service '$service'",
            ]);
        } else if ($savedId == -3) {
            return $this->json([
                'success' => FALSE,
                'status' => 500,
                'message' => "Unknown data set '$data_set' in '$service'",
            ]);
        } else {
            return $this->json([
                'success' => FALSE,
                'status' => 500,
                'message' => "Unknown error",
            ]);
        }

    }
}
