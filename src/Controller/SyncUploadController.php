<?php

namespace App\Controller;

use App\AppConstants;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class SyncUploadController extends AbstractController
{
    /**
     * @Route("/sync/upload", name="sync_upload", methods={"GET","HEAD"})
     */
    public function index()
    {
        return $this->render('sync_upload/index.html.twig', [
            'controller_name' => 'SyncUploadController',
        ]);
    }

    /**
     * @Route("/sync/upload/{service}/{data_set}", name="sync_upload_post", methods={"POST"})
     * @param String          $service
     * @param String          $data_set
     *
     * @param LoggerInterface $logger
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_post(String $service, String $data_set, LoggerInterface $logger)
    {
        $request = Request::createFromGlobals();

        /*return $this->json([
            'success' => TRUE,
            'status' => 201,
            'message' => "Disabled",
        ]);*/

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
            $data_set != "exercises") {
            AppConstants::writeToLog($service . '_' . $data_set . '.txt', $request->getContent());
        }

        $transformerClassName = 'App\\Transform\\' . $service . '\\Entry';
        if (!class_exists($transformerClassName)) {
            $logger->error('I could not find a Entry class for ' . $service);
            $savedId = -2;
        } else {
            $transformerClass = new $transformerClassName($logger);
            /** @noinspection PhpUndefinedMethodInspection */
            $savedId = $transformerClass->transform($data_set, $request->getContent(), $this->getDoctrine());
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
        } else  {
            return $this->json([
                'success' => FALSE,
                'status' => 500,
                'message' => "Unknown error",
            ]);
        }

    }
}