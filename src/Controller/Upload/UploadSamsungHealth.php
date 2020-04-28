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

namespace App\Controller\Upload;

use App\AppConstants;
use App\Controller\Common;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\CommsManager;
use App\Transform\SamsungHealth\CommonSamsung;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class SyncUploadController
 *
 * @package App\Controller
 */
class UploadSamsungHealth extends Common
{
    /**
     * @param $patientServiceProfile
     */
    private function log($patientServiceProfile)
    {
        if (!is_string($patientServiceProfile)) {
            AppConstants::writeToLog(
                'debug_transform.txt',
                "[UploadSamsungHealth] - " . print_r($patientServiceProfile, true)
            );
        } else {
            AppConstants::writeToLog(
                'debug_transform.txt',
                "[UploadSamsungHealth] - " . $patientServiceProfile
            );
        }
    }

    /**
     * @Route("/sync/upload/SamsungHealth/{data_set}", name="sync_upload_post", methods={"POST"})
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
        string $data_set,
        LoggerInterface $logger,
        AwardManager $awardManager,
        ChallengePve $challengePve,
        CommsManager $commsManager
    ) {
        $this->setupRoute();

        $request = Request::createFromGlobals();

        $service = "SamsungHealth";

        $entityType = CommonSamsung::convertDatasetToEntity($data_set);
        if (is_null($entityType)) {
            AppConstants::writeToLog($service . '_' . $data_set . '.txt', $request->getContent());
            $response = new JsonResponse();
            $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
            return $response;
        }

        $this->log("Receiving $entityType for " . $this->patient->getUsername());

        $internalSyncQueueMethod = "action" . ucfirst($entityType);
        //$this->log(" Looking for internal method: " . $internalSyncQueueMethod);

        if (method_exists($this, $internalSyncQueueMethod)) {
            $this->$internalSyncQueueMethod($request->getContent());
        } else {
            $transformerClassName = 'App\\Transform\\SamsungHealth\\' . ucfirst($entityType);
            if (class_exists($transformerClassName)) {
                $transformerClass = new $transformerClassName($this->getDoctrine(),
                    $logger, $awardManager, $challengePve,
                    $commsManager);
                $transformerClass->setPatientEntity($this->patient);
                $transformerClass->setApiReturn(json_decode($request->getContent(), true));
                $transformerClass->processData();
            } else {
                $this->log(" Couldn't find the Transformer class: " . $transformerClassName);
            }
        }

//        $transformerClassName = 'App\\Transform\\' . $service . '\\Entry';
//        if (!class_exists($transformerClassName)) {
//            $logger->error('I could not find a Entry class for ' . $service);
//            $savedId = -2;
//        } else {
//            if (!is_null($this->getUser()) && is_object($this->getUser()) && get_class($this->getUser()) == "App\Entity\Patient") {
//                $transformerClass = new $transformerClassName($logger, $this->getUser());
//            } else {
//                $transformerClass = new $transformerClassName($logger);
//            }
//            $savedId = $transformerClass->transform($data_set, $request->getContent(), $this->getDoctrine(),
//                $awardManager, $challengePve, $commsManager);
//        }
//
//        if (is_array($savedId)) {
//            return $this->json([
//                'success' => true,
//                'status' => 200,
//                'message' => "Saved multiple '$service/$data_set' entitles",
//                'entity_id' => $savedId,
//            ]);
//        } else {
//            if ($savedId > 0) {
//                return $this->json([
//                    'success' => true,
//                    'status' => 200,
//                    'message' => "Saved '$service/$data_set' as " . $savedId,
//                    'entity_id' => $savedId,
//                ]);
//            } else {
//                if ($savedId == -1) {
////                    AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__);
//                    return $this->json([
//                        'success' => false,
//                        'status' => 500,
//                        'message' => "Unable to save entity",
//                    ]);
//                } else {
//                    if ($savedId == -2) {
////                        AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__);
//                        return $this->json([
//                            'success' => false,
//                            'status' => 500,
//                            'message' => "Unknown service '$service'",
//                        ]);
//                    } else {
//                        if ($savedId == -3) {
////                            AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__);
//                            return $this->json([
//                                'success' => false,
//                                'status' => 500,
//                                'message' => "Unknown data set '$data_set' in '$service'",
//                            ]);
//                        } else {
////                            AppConstants::writeToLog('debug_transform.txt',
////                                'I\'m about to respond with a 500 error after ' . $data_set);
////                            AppConstants::writeToLog('debug_transform.txt',
////                                $request->getContent());
//
//                            return $this->json([
//                                'success' => false,
//                                'status' => 500,
//                                'message' => "Unknown error, saved is '" . $savedId . "'",
//                            ]);
//                        }
//                    }
//                }
//            }
//        }

        $response = new JsonResponse();
        $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
        return $response;

    }
}
