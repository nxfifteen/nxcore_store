<?php

namespace App\Controller;

use App\Entity\BodyFat;
use App\Entity\BodyWeight;
use App\Entity\Exercise;
use App\Entity\FitCaloriesDailySummary;
use App\Entity\FitDistanceDailySummary;
use App\Entity\FitFloorsIntraDay;
use App\Entity\FitStepsDailySummary;
use App\Entity\FitStepsIntraDay;
use App\Entity\Patient;
use App\Entity\RpgMilestones;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AngularController extends AbstractController
{
    private $dateRange = 14;

    /**
     * @Route("/help/angular", name="angular_help")
     */
    public function index_help()
    {
        return $this->render('angular/index.html.twig', [
            'controller_name' => 'AngularController',
        ]);
    }

    /**
     * @Route("/ux", name="angular")
     */
    public function index()
    {
        return $this->render('angular/index.html.twig', [
            'controller_name' => 'AngularController',
        ]);
    }

    /**
     * @Route("/{uuid}/ux/profile", name="angular_profile")
     * @param String $uuid A users UUID
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_profile(String $uuid)
    {
        $this->hasAccess($uuid);

        $return = [];

        /** @var Patient $patient */
        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findOneBy(['uuid' => $uuid]);

        if (!$patient) {
            $return['status'] = "error";
            $return['code'] = "404";
            $return['message'] = "Patient not found with UUID specified";
            $return['payload'] = "2000-01-01 00:00:00.000";

            return $this->json($return);
        }

        $return['status'] = "okay";
        $return['code'] = "200";
        $return['name'] = $patient->getFirstName();
        $return['nameFull'] = $patient->getFirstName() . " " . $patient->getSurName();
        $return['avatar'] = $patient->getAvatar();

        return $this->json($return);
    }

    /**
     * @param String $uuid
     *
     * @throws \LogicException If the Security component is not available
     */
    private function hasAccess(String $uuid)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', NULL, 'User tried to access a page without having ROLE_USER');

        /** @var \App\Entity\Patient $user */
        $user = $this->getUser();
        if ($user->getUuid() != $uuid) {
            $exception = $this->createAccessDeniedException("User tried to access another users information");
            throw $exception;
        }
    }

    /**
     * @Route("/{uuid}/ux/config", name="angular_config")
     * @param String $uuid A users UUID
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_config(String $uuid)
    {
        $this->hasAccess($uuid);

        $return = [];

        /** @var Patient $patient */
        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findOneBy(['uuid' => $uuid]);

        if (!$patient) {
            $return['status'] = "error";
            $return['code'] = "404";
            return $this->json($return);
        }

        $return['uiSettings'] = [];
        foreach ($patient->getUiSettings() as $uiSetting) {
            $uiSetting = explode("::", $uiSetting);
            $return['uiSettings'][$uiSetting[0]] = $uiSetting[1];
        }

        return $this->json($return);
    }

    /**
     * @Route("/{uuid}/ux/feed/dashboard", name="angular_dashboard")
     * @param String $uuid A users UUID
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_dashboard(String $uuid)
    {
        $this->hasAccess($uuid);

        $return = [];

        /** @var Patient $patient */
        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findOneBy(['uuid' => $uuid]);

        if (!$patient) {
            $return['status'] = "error";
            $return['code'] = "404";
            $return['message'] = "Patient not found with UUID specified";
            $return['payload'] = "2000-01-01 00:00:00.000";

            return $this->json($return);
        }

        $return['status'] = "okay";
        $return['code'] = "200";
        $return['exercise'] = $this->angularGetExerciseForMonth($uuid, date("Y-m-d"));
        $return['milestones'] = $this->angularGetMilestones($uuid, 2, 3, 3);
        $return['best'] = $this->angularGetTheBest($uuid, 3);
        $return['floors'] = $this->angularGetDailySummaryFloors($uuid, date("Y-m-d"), 2);
        $return['floorsIntraDay'] = $this->angularGetIntraDayFloors($uuid, date("Y-m-d"), 2);
        $return['steps'] = $this->angularGetDailySummarySteps($uuid, date("Y-m-d"), 3);
        $return['stepsIntraDay'] = $this->angularGetIntraDaySteps($uuid, date("Y-m-d"), 2);
        $return['distance'] = $this->angularGetDailySummaryDistance($uuid, date("Y-m-d"), 3);
        $return['calories'] = $this->angularGetDailySummaryCalories($uuid, date("Y-m-d"), 3);
        $return['weight'] = $this->angularGetBodyWeight($uuid, date("Y-m-d"));
        $return['fat'] = $this->angularGetBodyFat($uuid, date("Y-m-d"));

        return $this->json($return);
    }

    private function angularGetExerciseForMonth(String $uuid, String $date)
    {
        $this->hasAccess($uuid);

        $exerciseCounts = [];

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Exercise[] $exercises */
        $exercises = $this->getDoctrine()
            ->getRepository(Exercise::class)
            ->findByDateRangeHistorical($uuid, $date, 31);

        $retrievedDates = [];
        $return = [];
        $return['history'] = [];
        $return['labels'] = [];
        foreach ($exercises as $exercise) {
            $exerciseName = $exercise->getExerciseType()->getName();
            $exerciseDate = $exercise->getDateTimeStart()->format("Y-m-d");
            $retrievedExercises[] = $exerciseName;

            $return['history'][] = $exerciseDate . " " . $exercise->getDateTimeStart()->format("H:i") . ": __" . $exerciseName . "__ for **" . $this->returnHumanTime($exercise->getDuration()) . "**";

            if (!array_key_exists($exerciseName, $exerciseCounts)) {
                $exerciseCounts[$exerciseName] = [];
                $exerciseCounts[$exerciseName]['label'] = $exerciseName;
                $exerciseCounts[$exerciseName]['value'] = [];
            }

            if (array_key_exists($exerciseDate, $exerciseCounts[$exerciseName]['value'])) {
                $exerciseCounts[$exerciseName]['value'][$exerciseDate] = $exerciseCounts[$exerciseName]['value'][$exerciseDate] + 1;
            } else {
                $exerciseCounts[$exerciseName]['value'][$exerciseDate] = 1;
            }

            if (!in_array($exercise->getDateTimeStart()->format("D, jS M"), $return['labels'])) {
                $return['labels'][] = $exercise->getDateTimeStart()->format("D, jS M");
                $retrievedDates[] = $exerciseDate;
            }
        }

        $return['history'] = array_reverse($return['history']);
        $return['history'] = array_slice($return['history'], 0, 14);

        $i = 0;
        foreach ($exerciseCounts as $exerciseCount) {
            $return['data'][$i] = [];
            $return['data'][$i]['label'] = $exerciseCount['label'];
            $return['data'][$i]['data'] = [];
            foreach ($retrievedDates as $j => $retrievedDate) {
                if (array_key_exists($retrievedDate, $exerciseCount['value'])) {
                    $return['data'][$i]['data'][] = $exerciseCount['value'][$retrievedDate];
                } else {
                    $return['data'][$i]['data'][] = 0;
                }
            }
            $i++;
        }
        $exerciseCounts['$retrievedDates'] = $retrievedDates;

        return $return;
    }

    private function returnHumanTime(int $seconds)
    {
        $returnString = "";
        if ($seconds > 0) {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds / 60) % 60);
            $seconds = $seconds % 60;

            if ($hours > 0) {
                $returnString = $returnString . $hours . " hrs";
            }
            if ($minutes > 0) {
                if ($returnString != "") $returnString = $returnString . " ";
                $returnString = $returnString . $minutes . " min";
            }
            if ($seconds > 9) {
                if ($returnString != "") $returnString = $returnString . " ";
                $returnString = $returnString . $seconds . " sec";
            }
        }

        if ($seconds == 0) {
            $returnString = "0 sec";
        }

        return $returnString;
    }

    private function angularGetMilestones(String $uuid, int $trackingDeviceFloors, int $trackingDeviceSteps, int $trackingDeviceDistance)
    {
        $this->hasAccess($uuid);

        $return = [];

        $return['distance'] = [];
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var float $distance */
        $distance = $this->getDoctrine()
            ->getRepository(FitDistanceDailySummary::class)
            ->getSumOfValues($uuid, $trackingDeviceDistance);
        $distance = ($distance / 1000);

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var RpgMilestones[] $distanceMileStonesLess */
        $distanceMileStonesLess = $this->getDoctrine()
            ->getRepository(RpgMilestones::class)
            ->getLessThan('distance', $distance);
        foreach ($distanceMileStonesLess as $distanceMileStoneLess) {
            $return['distance']['less'][] = "**" . number_format($distanceMileStoneLess->getValue() - $distance, 2) . " km** till you've walked *" . $distanceMileStoneLess->getMsgLess() . "*";
        }

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var RpgMilestones[] $distanceMileStonesMore */
        $distanceMileStonesMore = $this->getDoctrine()
            ->getRepository(RpgMilestones::class)
            ->getMoreThan('distance', $distance);
        foreach ($distanceMileStonesMore as $distanceMileStoneMore) {
            $times = number_format($distance / $distanceMileStoneMore->getValue(), 0);
            if ($times == 1) {
                $return['distance']['more'][] = "You've walked *" . $distanceMileStoneMore->getMsgLess() . "*";
            } else if ($times == 2) {
                $return['distance']['more'][] = "You've walked *" . $distanceMileStoneMore->getMsgLess() . "* and **back**!";
            } else {
                $return['distance']['more'][] = "You've walked *" . $distanceMileStoneMore->getMsgLess() . "* **"
                    . $times . "** times.";
            }
        }

        return $return;
    }

    private function angularGetTheBest(String $uuid, $trackingDevice)
    {
        $this->hasAccess($uuid);

        $timeStampsInTrack = [];

        /** @var FitStepsDailySummary[] $steps */
        $steps = $this->getDoctrine()
            ->getRepository(FitStepsDailySummary::class)
            ->findHighest($uuid, $trackingDevice);
        $timeStampsInTrack[] = "Your highest step count, totalling " . number_format($steps[0]->getValue()) . ", for a day was on " . $steps[0]->getDateTime()->format("jS M, Y") . ".";

        /** @var FitDistanceDailySummary[] $distance */
        $distance = $this->getDoctrine()
            ->getRepository(FitDistanceDailySummary::class)
            ->findHighest($uuid, $trackingDevice);
        $timeStampsInTrack[] = "You traveled the furthest, " . $this->convertDistance($distance[0]) . ", on " . $distance[0]->getDateTime()->format("jS M, Y") . ".";

        return $timeStampsInTrack;
    }

    private function convertDistance(FitDistanceDailySummary $distance)
    {
        switch ($distance->getUnitOfMeasurement()->getName()) {
            case "meter":
                $value = ($distance->getValue() / 1609.34);
                $unit = "miles";
                break;
            default:
                $value = $distance->getValue();
                $unit = $distance->getUnitOfMeasurement()->getName();
                break;
        }

        return number_format($value, 2) . " " . $unit;
    }

    private function angularGetDailySummaryFloors(String $uuid, String $date, int $trackingDevice)
    {
        $this->hasAccess($uuid);

        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(FitFloorsIntraDay::class)
            ->findByDateRange($uuid, $date, $trackingDevice);

        $timeStampsInTrack = [];
        $timeStampsInTrack['period'] = 0;
        $timeStampsInTrack['value'] = 0;
        $timeStampsInTrack['goal'] = 10;

        if (count($product) > 0) {
            /** @var FitFloorsIntraDay[] $product */
            foreach ($product as $item) {
                if (is_numeric($item->getValue())) {
                    $timeStampsInTrack['value'] = $timeStampsInTrack['value'] + $item->getValue();
                }
            }
        }

        if ($timeStampsInTrack['goal'] > 0) {
            $timeStampsInTrack['progress'] = round(($timeStampsInTrack['value'] / $timeStampsInTrack['goal']) * 100, 0);
        } else {
            $timeStampsInTrack['progress'] = 100;
        }

        return $timeStampsInTrack;
    }

    private function angularGetIntraDayFloors(String $uuid, String $date, int $trackingDevice)
    {
        $this->hasAccess($uuid);

        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(FitFloorsIntraDay::class)
            ->findByDateRange($uuid, $date, $trackingDevice);

        $timeStampsInTrack = [];
        $timeStampsInTrack['widget'] = [];
        $timeStampsInTrack['widget']['labels'] = [];
        $timeStampsInTrack['widget']['data'] = [];
        $timeStampsInTrack['widget']['data']['label'] = "Floors";
        $timeStampsInTrack['widget']['data']['data'] = [];

        $dbHours = $this->getHoursArray();
        if (count($product) > 0) {
            /** @var FitFloorsIntraDay[] $product */
            foreach ($product as $item) {
                if (is_numeric($item->getValue())) {
                    $dbHours[$item->getDateTime()->format("G")] = $dbHours[$item->getDateTime()->format("G")] + $item->getValue();
                }
            }
        }
        $timeStampsInTrack['widget']['labels'] = array_keys($dbHours);
        $timeStampsInTrack['widget']['data']['data'] = $dbHours;

        return $timeStampsInTrack;
    }

    private function getHoursArray(int $currentHour = NULL)
    {
        if (is_null($currentHour)) {
            $currentHour = 23;
        }
        $dbHours = [];
        for ($i = 0; $i <= $currentHour; $i++) {
            $dbHours[$i] = 0;
        }
        return $dbHours;
    }

    private function angularGetDailySummarySteps(String $uuid, String $date, int $trackingDevice)
    {
        $this->hasAccess($uuid);

        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(FitStepsDailySummary::class)
            ->findByDateRangeHistorical($uuid, $date, $this->dateRange, $trackingDevice);

        $timeStampsInTrack = [];
        $timeStampsInTrack['widget'] = [];
        $timeStampsInTrack['widget']['labels'] = [];
        $timeStampsInTrack['widget']['data'] = [];
        $timeStampsInTrack['widget']['data']['label'] = "Steps";
        $timeStampsInTrack['widget']['data']['data'] = [];
        $timeStampsInTrack['period'] = 0;
        $timeStampsInTrack['value'] = 0;
        $timeStampsInTrack['goal'] = 0;

        if (count($product) > 0) {
            /** @var FitStepsDailySummary[] $product */
            foreach ($product as $item) {
                if (is_numeric($item->getValue())) {
                    $timeStampsInTrack['period'] = $timeStampsInTrack['period'] + $item->getValue();
                    $timeStampsInTrack['value'] = $item->getValue();
                    if (is_numeric($item->getGoal()->getGoal())) {
                        $timeStampsInTrack['goal'] = $item->getGoal()->getGoal();
                    }
                    $timeStampsInTrack['widget']['data']['data'][] = $item->getValue();
                    $timeStampsInTrack['widget']['labels'][] = $item->getDateTime()->format("Y-m-d");
                }
            }
        }

        if ($timeStampsInTrack['goal'] > 0) {
            $timeStampsInTrack['progress'] = round(($timeStampsInTrack['value'] / $timeStampsInTrack['goal']) * 100, 0);
        } else {
            $timeStampsInTrack['progress'] = 100;
        }

        return $timeStampsInTrack;
    }

    private function angularGetIntraDaySteps(String $uuid, String $date, int $trackingDevice)
    {
        $this->hasAccess($uuid);

        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(FitStepsIntraDay::class)
            ->findByDateRange($uuid, $date, $trackingDevice);

        $timeStampsInTrack = [];
        $timeStampsInTrack['widget'] = [];
        $timeStampsInTrack['widget']['labels'] = [];
        $timeStampsInTrack['widget']['data'] = [];
        $timeStampsInTrack['widget']['data']['label'] = "Steps";
        $timeStampsInTrack['widget']['data']['data'] = [];

        $dbHours = $this->getHoursArray();
        if (count($product) > 0) {
            /** @var FitStepsIntraDay[] $product */
            foreach ($product as $item) {
                if (is_numeric($item->getValue())) {
                    $dbHours[$item->getDateTime()->format("G")] = $dbHours[$item->getDateTime()->format("G")] + $item->getValue();
                }
            }
        }
        $timeStampsInTrack['widget']['labels'] = array_keys($dbHours);
        $timeStampsInTrack['widget']['data']['data'] = $dbHours;

        return $timeStampsInTrack;
    }

    private function angularGetDailySummaryDistance(String $uuid, String $date, int $trackingDevice)
    {
        $this->hasAccess($uuid);

        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(FitDistanceDailySummary::class)
            ->findByDateRangeHistorical($uuid, $date, $this->dateRange, $trackingDevice);

        $timeStampsInTrack = [];
        $timeStampsInTrack['widget'] = [];
        $timeStampsInTrack['widget']['labels'] = [];
        $timeStampsInTrack['widget']['data'] = [];
        $timeStampsInTrack['widget']['data']['label'] = "Kilometers";
        $timeStampsInTrack['widget']['data']['data'] = [];
        $timeStampsInTrack['period'] = 0;
        $timeStampsInTrack['value'] = 0;
        $timeStampsInTrack['goal'] = 5;

        if (count($product) > 0) {
            /** @var FitDistanceDailySummary[] $product */
            foreach ($product as $item) {
                if (is_numeric($item->getValue())) {
                    $timeStampsInTrack['period'] = $timeStampsInTrack['period'] + $item->getValue();
                    $timeStampsInTrack['value'] = $item->getValue();
                    if (is_numeric($item->getGoal()->getGoal())) {
                        $timeStampsInTrack['goal'] = $item->getGoal()->getGoal();
                    }
                    $timeStampsInTrack['widget']['data']['data'][] = round($item->getValue() / 1000, 0);
                    $timeStampsInTrack['widget']['labels'][] = $item->getDateTime()->format("Y-m-d");
                }
            }
        }
        $timeStampsInTrack['period'] = round($timeStampsInTrack['period'] / 1000, 2);
        $timeStampsInTrack['value'] = round($timeStampsInTrack['value'] / 1000, 2);

        if ($timeStampsInTrack['goal'] > 0) {
            $timeStampsInTrack['progress'] = round(($timeStampsInTrack['value'] / $timeStampsInTrack['goal']) * 100, 2);
            $timeStampsInTrack['goal'] = round($timeStampsInTrack['goal'] / 1000, 2);
        } else {
            $timeStampsInTrack['progress'] = 100;
        }

        return $timeStampsInTrack;
    }

    private function angularGetDailySummaryCalories(String $uuid, String $date, int $trackingDevice)
    {
        $this->hasAccess($uuid);

        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(FitCaloriesDailySummary::class)
            ->findByDateRange($uuid, $date, $trackingDevice);

        $timeStampsInTrack = [];
        $timeStampsInTrack['value'] = 0;
        $timeStampsInTrack['goal'] = 0;

        if (count($product) > 0) {
            /** @var FitCaloriesDailySummary[] $product */
            foreach ($product as $item) {
                if (is_numeric($item->getValue())) {
                    $timeStampsInTrack['value'] = $timeStampsInTrack['value'] + $item->getValue();
                }
            }
        }

        if ($timeStampsInTrack['goal'] > 0) {
            $timeStampsInTrack['progress'] = round(($timeStampsInTrack['value'] / $timeStampsInTrack['goal']) * 100, 0);
        } else {
            $timeStampsInTrack['progress'] = 100;
        }

        return $timeStampsInTrack;
    }

    private function angularGetBodyWeight(String $uuid, String $date)
    {
        $this->hasAccess($uuid);

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BodyWeight[] $product */
        $product = $this->getDoctrine()
            ->getRepository(BodyWeight::class)
            ->findByDateRangeHistorical($uuid, $date, 31);

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BodyWeight[] $productFirst */
        $productFirst = $this->getDoctrine()
            ->getRepository(BodyWeight::class)
            ->findFirst($uuid);

        $timeStampsInTrack = [];
        $timeStampsInTrack['value'] = 0;
        $timeStampsInTrack['unit'] = 0;
        $timeStampsInTrack['goal'] = 0;
        $timeStampsInTrack['progress'] = 0;
        $timeStampsInTrack['since'] = $product[0]->getDateTime()->format("M Y");
        $timeStampsInTrack['widget'] = [];
        $timeStampsInTrack['widget']['labels'] = [];
        $timeStampsInTrack['widget']['data'] = [];
        $timeStampsInTrack['widget']['data']['label'] = "Kg";
        $timeStampsInTrack['widget']['data']['data'] = [];
        $timeStampsInTrack['widget']['axis']['min'] = 40;
        $timeStampsInTrack['widget']['axis']['max'] = 110;

        if (count($product) > 0) {
            /** @var BodyWeight[] $product */
            foreach ($product as $item) {
                if (is_numeric($item->getMeasurement())) {
                    $timeStampsInTrack['value'] = round($item->getMeasurement(), 2);
                    $timeStampsInTrack['unit'] = $item->getUnitOfMeasurement()->getName();
                    $timeStampsInTrack['widget']['data'][0]['label'] = "Recorded " . $item->getUnitOfMeasurement()->getName();
                    $timeStampsInTrack['widget']['data'][0]['data'][] = round($item->getMeasurement(), 2);
                    $timeStampsInTrack['widget']['labels'][] = $item->getDateTime()->format("D, jS M");
                    if (is_numeric($item->getPatientGoal()->getGoal())) {
                        $timeStampsInTrack['widget']['data'][1]['label'] = "Goal " . $item->getPatientGoal()->getUnitOfMeasurement()->getName();
                        $timeStampsInTrack['widget']['data'][1]['data'][] = round($item->getPatientGoal()->getGoal(), 2);
                    }
                }
            }
        }

        $firstMeasurement = round($productFirst[0]->getMeasurement(), 2);
        $currentMeasurement = round($product[(count($product) - 1)]->getMeasurement(), 2);
        $targetMeasurement = round($product[(count($product) - 1)]->getPatientGoal()->getGoal(), 2);
        $totalToReach = round($firstMeasurement - $targetMeasurement, 2);
        $totalProgress = round($firstMeasurement - $currentMeasurement, 2);
        $progressPercentage = round(($totalProgress / $totalToReach) * 100, 2);

        $timeStampsInTrack['goal'] = $targetMeasurement;
        $timeStampsInTrack['progress'] = $progressPercentage;

        return $timeStampsInTrack;
    }

    private function angularGetBodyFat(String $uuid, String $date)
    {
        $this->hasAccess($uuid);

        /** @noinspection PhpUndefinedMethodInspection */
        $product = $this->getDoctrine()
            ->getRepository(BodyFat::class)
            ->findByDateRangeHistorical($uuid, $date, 31);

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BodyFat[] $productFirst */
        $productFirst = $this->getDoctrine()
            ->getRepository(BodyFat::class)
            ->findFirst($uuid);

        $timeStampsInTrack = [];
        $timeStampsInTrack['value'] = 0;
        $timeStampsInTrack['unit'] = 0;
        $timeStampsInTrack['goal'] = 0;
        $timeStampsInTrack['progress'] = 0;
        $timeStampsInTrack['widget'] = [];
        $timeStampsInTrack['widget']['labels'] = [];
        $timeStampsInTrack['widget']['data'] = [];
        $timeStampsInTrack['widget']['data']['label'] = "Kg";
        $timeStampsInTrack['widget']['data']['data'] = [];
        $timeStampsInTrack['widget']['axis']['min'] = 40;
        $timeStampsInTrack['widget']['axis']['max'] = 110;

        if (count($product) > 0) {
            /** @var BodyFat[] $product */
            foreach ($product as $item) {
                if (is_numeric($item->getMeasurement())) {
                    $timeStampsInTrack['value'] = round($item->getMeasurement(), 2);
                    $timeStampsInTrack['unit'] = $item->getUnitOfMeasurement()->getName();
                    $timeStampsInTrack['widget']['data'][0]['label'] = "Recorded " . $item->getUnitOfMeasurement()->getName();
                    $timeStampsInTrack['widget']['data'][0]['data'][] = round($item->getMeasurement(), 2);
                    $timeStampsInTrack['widget']['labels'][] = $item->getDateTime()->format("D, jS");
                    if (is_numeric($item->getPatientGoal()->getGoal())) {
                        $timeStampsInTrack['widget']['data'][1]['label'] = "Goal " . $item->getPatientGoal()->getUnitOfMeasurement()->getName();
                        $timeStampsInTrack['widget']['data'][1]['data'][] = round($item->getPatientGoal()->getGoal(), 2);
                    }
                }
            }
        }

        $firstMeasurement = round($productFirst[0]->getMeasurement(), 2);
        $currentMeasurement = round($product[(count($product) - 1)]->getMeasurement(), 2);
        $targetMeasurement = round($product[(count($product) - 1)]->getPatientGoal()->getGoal(), 2);
        $totalToReach = round($firstMeasurement - $targetMeasurement, 2);
        $totalProgress = round($firstMeasurement - $currentMeasurement, 2);
        $progressPercentage = round(($totalProgress / $totalToReach) * 100, 2);

        $timeStampsInTrack['goal'] = $targetMeasurement;
        $timeStampsInTrack['progress'] = $progressPercentage;

        return $timeStampsInTrack;
    }
}
