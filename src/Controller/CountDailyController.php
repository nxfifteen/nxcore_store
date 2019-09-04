<?php

namespace App\Controller;

use App\Entity\ConsumeWater;
use App\Entity\FitStepsDailySummary;
use App\Entity\Patient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CountDailyController extends AbstractController
{

    /**
     * @param String $uuid
     *
     * @throws \LogicException If the Security component is not available
     */
    private function hasAccess(String $uuid) {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without having ROLE_USER');

        /** @var \App\Entity\Patient $user */
        $user = $this->getUser();
        if ($user->getUuid() != $uuid) {
            $exception = $this->createAccessDeniedException("User tried to access another users information");
            throw $exception;
        }
    }

    /**
     * @Route("/json/{uuid}/count/daily/steps", name="count_daily_step")
     * @param String $uuid
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function FitStepsDailySummary( String $uuid )
    {
        $this->hasAccess($uuid);

        return $this->FitStepsDailySummaryDateTracker($uuid, date("Y-m-d"), -1);
    }

    /**
     * @Route("/json/{uuid}/count/daily/steps/{trackingDevice}", name="count_daily_step_trackingDevice")
     * @param String $uuid
     * @param int $trackingDevice
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function FitStepsDailySummaryTracker(String $uuid, int $trackingDevice)
    {
        $this->hasAccess($uuid);

        return $this->FitStepsDailySummaryDateTracker($uuid, date("Y-m-d"), $trackingDevice);
    }

    /**
     * @Route("/json/{uuid}/count/daily/steps/{trackingDevice}/{date}", name="count_daily_step_date_trackingDevice")
     * @param String $uuid
     * @param String $date
     * @param int $trackingDevice
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function FitStepsDailySummaryDateTracker(String $uuid, String $date, int $trackingDevice)
    {
        $this->hasAccess($uuid);

        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(FitStepsDailySummary::class)
            ->findByDateRange($uuid, $date, $trackingDevice);

        $timeStampsInTrack = [];
        $timeStampsInTrack[ 'uuid' ] = $uuid;
        $timeStampsInTrack[ 'today' ] = $date;
        $timeStampsInTrack[ 'lastReading' ] = $date;
        $timeStampsInTrack[ 'sum' ] = 0;
        $timeStampsInTrack[ 'goal' ] = 0;
        $timeStampsInTrack[ 'values' ] = [];

        if ( count($product) > 0 ) {
            $goals = 0;

            /** @var FitStepsDailySummary[] $product */
            foreach ( $product as $item ) {
                if ( is_numeric($item->getValue()) ) {
                    $timeStampsInTrack[ 'sum' ] = $timeStampsInTrack[ 'sum' ] + $item->getValue();
                    if (is_numeric($item->getGoal()->getGoal())) {
                        $goals = $goals + 1;
                        $timeStampsInTrack['goal'] = $item->getGoal()->getGoal();
                    }

                    $recordItem = [];
                    $recordItem[ 'dateTime' ] = $item->getDateTime()->format("H:i:s");
                    $recordItem[ 'value' ] = $item->getValue();
                    if (!is_null($item->getTrackingDevice())) $recordItem[ 'tracker' ] = $item->getTrackingDevice()->getName();
                    if (!is_null($item->getTrackingDevice()->getService())) $recordItem[ 'service' ] = $item->getTrackingDevice()->getService()->getName();

                    $timeStampsInTrack[ 'values' ][] = $recordItem;
                }
            }
            if ( isset($item) ) {
                $timeStampsInTrack[ 'lastReading' ] = $item->getDateTime()->format("H:i:s");
            }

            if ($goals > 0) {
                $timeStampsInTrack['goal'] = $timeStampsInTrack['goal'] / $goals;
            } else {
                $timeStampsInTrack['goal'] = 0;
            }
        }

        return $this->json($timeStampsInTrack);
    }

    /**
     * @Route("/json/{uuid}/count/daily/water", name="count_daily_water")
     * @param String $uuid
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function consumeWater( String $uuid )
    {
        return $this->consumeWaterDate($uuid, date("Y-m-d"));
    }

    /**
     * @Route("/json/{uuid}/count/daily/water/{date}", name="count_daily_water_date")
     * @param String $uuid
     * @param String $date
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function consumeWaterDate( String $uuid, String $date )
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(ConsumeWater::class)
            ->findByDateRange($uuid, $date);

        $timeStampsInTrack = [];
        $timeStampsInTrack[ 'uuid' ] = $uuid;
        $timeStampsInTrack[ 'today' ] = $date;
        $timeStampsInTrack[ 'lastReading' ] = $date;
        $timeStampsInTrack[ 'sum' ] = 0;
        $timeStampsInTrack[ 'goal' ] = 0;
        $timeStampsInTrack[ 'values' ] = [];

        if ( count($product) > 0 ) {
            /** @var ConsumeWater[] $product */
            foreach ( $product as $item ) {
                if ( is_numeric($item->getMeasurement()) ) {
                    $timeStampsInTrack[ 'sum' ] = $timeStampsInTrack[ 'sum' ] + $item->getMeasurement();
                    if ($timeStampsInTrack[ 'goal' ] == 0) $timeStampsInTrack[ 'goal' ] = $item->getPatientGoal()->getGoal();

                    $recordItem = [];
                    $recordItem[ 'dateTime' ] = $item->getDateTime()->format("H:i:s");
                    $recordItem[ 'value' ] = $item->getMeasurement();
                    $recordItem[ 'comment' ] = $item->getComment();
                    if (!is_null($item->getTrackingDevice())) $recordItem[ 'service' ] = $item->getTrackingDevice()->getName();

                    $timeStampsInTrack[ 'values' ][] = $recordItem;
                }
            }
            if ( isset($item) ) {
                $timeStampsInTrack[ 'lastReading' ] = $item->getDateTime()->format("H:i:s");
            }
        }

        return $this->json($timeStampsInTrack);
    }

}
