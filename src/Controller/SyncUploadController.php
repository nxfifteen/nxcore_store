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

        switch ($service) {
            case AppConstants::SAMSUNGHEALTH:
                $this->transformSamsung($data_set, $request->getContent());
                break;
        }

        return $this->json([
            'data_set' => $data_set,
            'body' => $request->getContent(),
        ]);
    }

    /**
     * @param String $data_set
     * @param String $getContent
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
        }

    }
}