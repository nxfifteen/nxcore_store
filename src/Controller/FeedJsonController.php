<?php

namespace App\Controller;

use App\Entity\BodyFat;
use App\Entity\BodyWeight;
use App\Entity\ConsumeWater;
use App\Entity\FitStepsDailySummary;
use App\Entity\Patient;
use Sentry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FeedJsonController extends AbstractController
{
    /** @var Patient $patient */
    private $patient;

    /**
     * @Route("/json/count/daily/steps", name="json_daily_step")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function FitStepsDailySummary()
    {
        $this->setupRoute();

        return $this->FitStepsDailySummaryDateTracker(date("Y-m-d"), -1);
    }

    private function setupRoute()
    {
        if (is_null($this->patient)) $this->patient = $this->getUser();

        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
            ]);
        });
    }

    /**
     * @Route("/json//count/daily/steps/{trackingDevice}/{date}", name="json_daily_step_date_trackingDevice")
     *
     * @param String $date
     * @param int    $trackingDevice
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function FitStepsDailySummaryDateTracker(String $date, int $trackingDevice)
    {
        $this->setupRoute();

        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(FitStepsDailySummary::class)
            ->findByDateRange($this->patient->getUuid(), $date, $trackingDevice);

        $timeStampsInTrack = [];
        $timeStampsInTrack['uuid'] = $this->patient->getUuid();
        $timeStampsInTrack['today'] = $date;
        $timeStampsInTrack['lastReading'] = $date;
        $timeStampsInTrack['sum'] = 0;
        $timeStampsInTrack['goal'] = 0;
        $timeStampsInTrack['values'] = [];

        if (count($product) > 0) {
            $goals = 0;

            /** @var FitStepsDailySummary[] $product */
            foreach ($product as $item) {
                if (is_numeric($item->getValue())) {
                    $timeStampsInTrack['sum'] = $timeStampsInTrack['sum'] + $item->getValue();
                    if (is_numeric($item->getGoal()->getGoal())) {
                        $goals = $goals + 1;
                        $timeStampsInTrack['goal'] = $item->getGoal()->getGoal();
                    }

                    $recordItem = [];
                    $recordItem['dateTime'] = $item->getDateTime()->format("H:i:s");
                    $recordItem['value'] = $item->getValue();
                    if (!is_null($item->getTrackingDevice())) $recordItem['tracker'] = $item->getTrackingDevice()->getName();
                    if (!is_null($item->getTrackingDevice()->getService())) $recordItem['service'] = $item->getTrackingDevice()->getService()->getName();

                    $timeStampsInTrack['values'][] = $recordItem;
                }
            }
            if (isset($item)) {
                $timeStampsInTrack['lastReading'] = $item->getDateTime()->format("H:i:s");
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
     * @Route("/json/count/daily/steps/{trackingDevice}", name="json_daily_step_trackingDevice")
     *
     * @param int $trackingDevice
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function FitStepsDailySummaryTracker(int $trackingDevice)
    {
        $this->setupRoute();

        return $this->FitStepsDailySummaryDateTracker(date("Y-m-d"), $trackingDevice);
    }

    /**
     * @Route("/json/count/daily/water", name="json_daily_water")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function consumeWater()
    {
        $this->setupRoute();

        return $this->consumeWaterDate(date("Y-m-d"));
    }

    /**
     * @Route("/json/count/daily/water/{date}", name="json_daily_water_date")
     *
     * @param String $date
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function consumeWaterDate(String $date)
    {
        $this->setupRoute();

        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(ConsumeWater::class)
            ->findByDateRange($this->patient->getUuid(), $date);

        $timeStampsInTrack = [];
        $timeStampsInTrack['uuid'] = $this->patient->getUuid();
        $timeStampsInTrack['today'] = $date;
        $timeStampsInTrack['lastReading'] = "00:00:00";
        $timeStampsInTrack['sum'] = 0;
        $timeStampsInTrack['goal'] = 0;
        $timeStampsInTrack['values'] = [];

        if (count($product) > 0) {
            /** @var ConsumeWater[] $product */
            foreach ($product as $item) {
                if (is_numeric($item->getMeasurement())) {
                    $timeStampsInTrack['sum'] = $timeStampsInTrack['sum'] + $item->getMeasurement();
                    if ($timeStampsInTrack['goal'] == 0) $timeStampsInTrack['goal'] = $item->getPatientGoal()->getGoal();

                    $recordItem = [];
                    $recordItem['dateTime'] = $item->getDateTime()->format("H:i:s");
                    $recordItem['value'] = $item->getMeasurement();
                    $recordItem['comment'] = $item->getComment();
                    if (!is_null($item->getTrackingDevice())) $recordItem['service'] = $item->getTrackingDevice()->getName();

                    $timeStampsInTrack['values'][] = $recordItem;
                }
            }
            if (isset($item)) {
                $timeStampsInTrack['lastReading'] = $item->getDateTime()->format("H:i:s");
            }
        }

        return $this->json($timeStampsInTrack);
    }

    /**
     * @Route("/json/count/daily/body", name="json_daily_body")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function body()
    {
        $this->setupRoute();

        return $this->bodyDate(date("Y-m-d"));
    }

    /**
     * @Route("/json/count/daily/body/{date}", name="json_daily_body_date")
     *
     * @param String $date
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function bodyDate(String $date)
    {
        $this->setupRoute();

        /** @var BodyWeight[] $bodyWeights */
        $bodyWeights = $this->getDoctrine()
            ->getRepository(BodyWeight::class)
            ->findByDateRange($this->patient->getUuid(), $date);
        $bodyWeights = $bodyWeights[0];

        /** @var BodyFat[] $bodyFats */
        $bodyFats = $this->getDoctrine()
            ->getRepository(BodyFat::class)
            ->findByDateRange($this->patient->getUuid(), $date);
        $bodyFats = $bodyFats[0];

        $timeStampsInTrack = [];
        $timeStampsInTrack['uuid'] = $this->patient->getUuid();
        $timeStampsInTrack['today'] = $date;
        $timeStampsInTrack['lastReading'] = $bodyWeights->getDateTime()->format("H:i:s");
        $timeStampsInTrack['value_kg'] = $bodyWeights->getMeasurement();
        $timeStampsInTrack['goal_kg'] = $bodyWeights->getPatientGoal()->getGoal();
        $timeStampsInTrack['value_lb'] = round($bodyWeights->getMeasurement() * 2.205, 2);
        $timeStampsInTrack['goal_lb'] = round($bodyWeights->getPatientGoal()->getGoal() * 2.205, 2);
        $timeStampsInTrack['fat'] = round($bodyFats->getMeasurement(), 2);
        $timeStampsInTrack['goal_fat'] = round($bodyFats->getPatientGoal()->getGoal(), 2);

        return $this->json($timeStampsInTrack);
    }

    /**
     * @Route("/json/rpg/xp", name="json_rpg_xp")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function rpgXp()
    {
        $this->setupRoute();

        $returnArray = [];
        $returnArray['uuid'] = $this->patient->getUuid();
        $returnArray['today'] = date("Y-m-d");
        $returnArray['lastReading'] = date("H:i:s");

        $returnArray['level'] = $this->patient->getRpgLevel();
        $returnArray['factor'] = $this->patient->getRpgFactor();
        $returnArray['current'] = round($this->patient->getXpTotal(), 0);
        $returnArray['next'] = ceil( $returnArray['current'] / 100 ) * 100;
        $returnArray['level_next'] = $returnArray['next'] - $returnArray['current'];
        $returnArray['level_percentage'] = 100 - ($returnArray['next'] - $returnArray['current']);

        $returnArray['log'] = $this->patient->getXp()->last()->getReason();

        return $this->json($returnArray);
    }

    /**
     * @Route("/json/profile", name="json_profile")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function profile()
    {
        $this->setupRoute();

        $returnArray = [];
        $returnArray['uuid'] = $this->patient->getUuid();
        $returnArray['today'] = date("Y-m-d");
        $returnArray['loginStreak'] = $this->patient->getLoginStreak();

        return $this->json($returnArray);
    }
}
