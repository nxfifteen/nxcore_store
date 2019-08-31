<?php

namespace App\Controller;

use App\AppConstants;
use App\Entity\FitStepsDailySummary;
use App\Entity\TrackingDevice;
use App\Transform\SamsungHealth\Constants AS SamsungHealthConstants;
use App\Transform\SamsungHealth\SamsungCountDailySteps;
use App\Transform\SamsungHealth\SamsungDevices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use /** @noinspection PhpUnusedAliasInspection */ Symfony\Component\Routing\Annotation\Route;


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
     * @param String $service
     * @param String $data_set
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_post(String $service, String $data_set)
    {
        $request = Request::createFromGlobals();

        AppConstants::writeToLog($service . '_' . $data_set . '.txt', $request->getContent());

        $savedId = -2;

        switch ($service) {
            case AppConstants::SAMSUNGHEALTH:
                $savedId = $this->transformSamsung($data_set, $request->getContent());
                break;
        }

        if ($savedId > 0) {
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
        }

    }

    /**
     * @param String $data_set
     * @param String $getContent
     *
     * @return int
     */
    private function transformSamsung(String $data_set, String $getContent)
    {
        $translateEntity = NULL;

        switch ($data_set) {
            case SamsungHealthConstants::SAMSUNGHEALTHEPDEVICES:
                /** @var TrackingDevice $translateEntity */
                $translateEntity = SamsungDevices::translate($this->getDoctrine(), $getContent);
                break;
            case SamsungHealthConstants::SAMSUNGHEALTHEPDAILYSTEPS:
                /** @var FitStepsDailySummary $translateEntity */
                $translateEntity = SamsungCountDailySteps::translate($this->getDoctrine(), $getContent);
                break;
        }

        if (!is_null($translateEntity)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($translateEntity);
            $entityManager->flush();

            return $translateEntity->getId();
        } else {
            return -1;
        }

    }
}