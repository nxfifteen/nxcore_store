<?php /**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nxcore/
 * @link      https://gitlab.com/nx-core/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */ /** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace App\Controller;

use App\AppConstants;
use App\Entity\Exercise;
use App\Entity\FitStepsDailySummary;
use App\Entity\FitStepsIntraDay;
use App\Entity\PatientDevice;
use DateInterval;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FeedUxController
 *
 * @package App\Controller
 */
class FeedUxController extends Common
{

    /**
     * @Route("/feed/activities/log/from/{dateFrom}/within/{searchRange}/limited/{limit}", name="index_activity_log")
     *
     * @param int    $limit
     * @param string $dateFrom
     * @param string $searchRange
     *
     * @return JsonResponse
     */
    public function index_activity_log(int $limit, string $dateFrom, string $searchRange)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);
        $this->setupRoute();

        $return['title'] = $dateFrom;
        $return['limit'] = $limit;
        $return['dateFrom'] = $dateFrom;
        $return['dateBackTill'] = $dateFrom;
        $return['searchRange'] = strtoupper($searchRange);
        $return['nav'] = [
            "nextMonth" => '',
            "thisMonth" => '',
            "prevMonth" => '',
        ];

        $return['durations'] = [];
        $return['durationsTotal'] = 0;
        $return['partOfDays'] = [
            "morning" => 0,
            "afternoon" => 0,
            "evening" => 0,
            "night" => 0,
        ];
        $return['partOfDaysTotal'] = 0;

        $return['periodDurations'] = [];
        $return['periodDurationsTotal'] = 0;
        $return['periodPartOfDays'] = [
            "morning" => 0,
            "afternoon" => 0,
            "evening" => 0,
            "night" => 0,
        ];
        $return['periodPartOfDaysTotal'] = 0;

        $return['results'] = [];

        try {
            $dateBackTill = new DateTime($dateFrom);
            $nextMonth = new DateTime($dateFrom);
            $prevMonth = new DateTime($dateFrom);
            $dateFrom = new DateTime($dateFrom);

            $return['title'] = $dateFrom->format("F, Y");

            $interval = new DateInterval('P' . strtoupper($searchRange));
            $dateBackTill->sub($interval);
            $return['dateBackTill'] = $dateBackTill->format("Y-m-d");

            $nextMonth->add($interval);
            $prevMonth->sub($interval);

            $return['nav']['thisMonth'] = date("Y-m-d");
            if ($nextMonth->format("U") <= date("U")) {
                $return['nav']['nextMonth'] = $nextMonth->format("Y-m-d");
            }
            $return['nav']['prevMonth'] = $prevMonth->format("Y-m-d");

        } catch (Exception $e) {
            $return['error'] = $e->getMessage();

            $b = microtime(true);
            $c = $b - $a;
            $return['genTime'] = round($c, 4);
            return $this->json($return);
        }

        $dbAllTimeDurations = $this->getDoctrine()
            ->getRepository(Exercise::class)->createQueryBuilder('e')
            ->leftJoin('e.patient', 'p')
            ->leftJoin('e.exerciseType', 't')
            ->andWhere('p.id = :patientId')
            ->setParameter('patientId', $this->patient->getId())
            ->groupBy('e.exerciseType')
            ->select('SUM(e.duration) as duration, t.tag as name')
            ->getQuery()->getResult();
        if ($dbAllTimeDurations && count($dbAllTimeDurations) > 0) {
            $newArray = ["labels" => [], "values" => []];
            foreach ($dbAllTimeDurations as $dbAllTimeDuration) {
                $newArray["labels"][] = ucwords($dbAllTimeDuration['name']);
                $newArray["values"][] = intval(round(($dbAllTimeDuration['duration'] / 60), 0));
                $return['durationsTotal'] = $return['durationsTotal'] + intval(round(($dbAllTimeDuration['duration'] / 60),
                        0));
            }
            $return['durations'] = $newArray;
        }

        $dbAllTimePartOfDays = $this->getDoctrine()
            ->getRepository(Exercise::class)->createQueryBuilder('e')
            ->leftJoin('e.patient', 'p')
            ->leftJoin('e.partOfDay', 't')
            ->andWhere('p.id = :patientId')
            ->setParameter('patientId', $this->patient->getId())
            ->groupBy('e.partOfDay')
            ->select('SUM(e.duration) as duration, t.name as name')
            ->getQuery()->getResult();
        if ($dbAllTimePartOfDays && count($dbAllTimePartOfDays) > 0) {
            $newArray = ["labels" => [], "values" => []];
            foreach ($dbAllTimePartOfDays as $dbAllTimePartOfDay) {
                $newArray["labels"][] = ucwords($dbAllTimePartOfDay['name']);
                $newArray["values"][] = intval(round(($dbAllTimePartOfDay['duration'] / 60), 0));
                $return['partOfDaysTotal'] = $return['partOfDaysTotal'] + intval(round(($dbAllTimePartOfDay['duration'] / 60),
                        0));
            }
            $return['partOfDays'] = $newArray;
        }

        /** @var Exercise[] $dbExercises */
        if ($limit < 0) {
            $dbExercises = $this->getDoctrine()
                ->getRepository(Exercise::class)->createQueryBuilder('e')
                ->leftJoin('e.patient', 'p')
                ->where('e.dateTimeStart <= :dateFrom')
                ->setParameter('dateFrom', $dateFrom->format("Y-m-d 23:59:59"))
                ->andWhere('e.dateTimeStart >= :dateBackTill')
                ->setParameter('dateBackTill', $dateBackTill->format("Y-m-d 00:00:00"))
                ->andWhere('p.id = :patientId')
                ->setParameter('patientId', $this->patient->getId())
                ->orderBy('e.dateTimeStart', 'DESC')
                ->getQuery()->getResult();
        } else {
            $dbExercises = $this->getDoctrine()
                ->getRepository(Exercise::class)->createQueryBuilder('e')
                ->leftJoin('e.patient', 'p')
                ->where('e.dateTimeStart <= :dateFrom')
                ->setParameter('dateFrom', $dateFrom->format("Y-m-d 23:59:59"))
                ->andWhere('e.dateTimeStart >= :dateBackTill')
                ->setParameter('dateBackTill', $dateBackTill->format("Y-m-d 00:00:00"))
                ->andWhere('p.id = :patientId')
                ->setParameter('patientId', $this->patient->getId())
                ->orderBy('e.dateTimeStart', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()->getResult();
        }
        if (!$dbExercises) {
            $b = microtime(true);
            $c = $b - $a;
            $return['genTime'] = round($c, 4);
            return $this->json($return);
        }

        foreach ($dbExercises as $dbExercise) {
            $exerciseDate = $dbExercise->getDateTimeStart()->format("Y-m-d");
            $exerciseDateStarted = $dbExercise->getDateTimeStart()->format("H:i:s");
            $exerciseDateFinished = $dbExercise->getDateTimeEnd()->format("H:i:s");
            $exerciseType = $dbExercise->getExerciseType()->getName();
            $exerciseTag = $dbExercise->getExerciseType()->getTag();
            $partOfDay = $dbExercise->getPartOfDay()->getName();

            $dayStepTotal = 0;
            $exerciseStepCountSum = 0;
            if ($exerciseType == "Walking") {
                if (is_null($dbExercise->getSteps())) {
                    $dbIntraDaySteps = $this->getDoctrine()
                        ->getRepository(FitStepsIntraDay::class)
                        ->findSumDates(
                            $this->patient->getUuid(),
                            $exerciseDate,
                            $exerciseDateStarted,
                            $exerciseDateFinished,
                            $dbExercise->getTrackingDevice()->getId()
                        );
                    if (is_array($dbIntraDaySteps) && count($dbIntraDaySteps) == 1 && array_key_exists("sum",
                            $dbIntraDaySteps[0])) {
                        if ($dbIntraDaySteps[0]['sum'] > 0) {
                            $dbExercise->setSteps($dbIntraDaySteps[0]['sum']);

                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($dbExercise);
                            $entityManager->flush();
                        }
                    }
                }

                $exerciseStepCountSum = $dbExercise->getSteps();

                /** @var FitStepsDailySummary[] $dbStepsForDay */
                $dbStepsForDay = $this->getDoctrine()
                    ->getRepository(FitStepsDailySummary::class)
                    ->findForDay($this->patient->getUuid(), $exerciseDate);
                if (is_array($dbStepsForDay) && count($dbStepsForDay) > 0) {
                    $dayStepTotal = $dbStepsForDay[0]->getValue();
                }
            }

            $return['results'][] = [
                "id" => $dbExercise->getId(),
                "tracker" => $dbExercise->getTrackingDevice()->getName(),
                "partOfDay" => $partOfDay,
                "exerciseType" => explode(',', $exerciseType)[0],
                "exerciseTag" => $exerciseTag,
                "date" => $exerciseDate,
                "dateFormatted" => $dbExercise->getDateTimeStart()->format("l ") . ucfirst($partOfDay) . $dbExercise->getDateTimeStart()->format(", F jS"),
                "started" => $exerciseDateStarted,
                "finished" => $exerciseDateFinished,
                "duration" => round(($dbExercise->getDuration() / 60), 0),
                "steps" => $exerciseStepCountSum,
                "stepsTotal" => $dayStepTotal,
                "calorie" => round($dbExercise->getExerciseSummary()->getCalorie(), 3),
                "distance" => round($dbExercise->getExerciseSummary()->getDistance() / 1000, 3),
            ];

            if (array_key_exists($exerciseTag, $return['periodDurations'])) {
                $return['periodDurations'][$exerciseTag] = $return['periodDurations'][$exerciseTag] + round(($dbExercise->getDuration() / 60),
                        0);
            } else {
                $return['periodDurations'][$exerciseTag] = round(($dbExercise->getDuration() / 60), 0);
            }

            if (array_key_exists($partOfDay, $return['periodPartOfDays'])) {
                $return['periodPartOfDays'][$partOfDay] = $return['periodPartOfDays'][$partOfDay] + round(($dbExercise->getDuration() / 60),
                        0);
            } else {
                $return['periodPartOfDays'][$partOfDay] = round(($dbExercise->getDuration() / 60), 0);
            }
        }

        $newArray = ["labels" => [], "values" => []];
        foreach ($return['periodPartOfDays'] as $key => $partOfDay) {
            $newArray["labels"][] = ucwords($key);
            $newArray["values"][] = $partOfDay;
            $return['periodPartOfDaysTotal'] = $return['periodPartOfDaysTotal'] + $partOfDay;
        }
        $return['periodPartOfDays'] = $newArray;

        $newArray = ["labels" => [], "values" => []];
        foreach ($return['periodDurations'] as $key => $partOfDay) {
            $newArray["labels"][] = ucwords($key);
            $newArray["values"][] = $partOfDay;
            $return['periodDurationsTotal'] = $return['periodDurationsTotal'] + $partOfDay;
        }
        $return['periodDurations'] = $newArray;

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/feed/activities/detail/{activityId}", name="index_activity_log_detail")
     *
     * @param int $activityId
     *
     * @return JsonResponse
     */
    public function index_activity_log_detail(int $activityId)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);
        $this->setupRoute();

        $return['nav'] = [
            "nextMonth" => '',
            "thisMonth" => '',
            "prevMonth" => '',
        ];

        /** @var Exercise[] $dbExercises */
        $dbExercises = $this->getDoctrine()
            ->getRepository(Exercise::class)->createQueryBuilder('e')
            ->where('e.id = :activityId')
            ->setParameter('activityId', $activityId)
            ->andWhere('e.patient = :patientId')
            ->setParameter('patientId', $this->patient)
            ->getQuery()->getResult();
        if (!$dbExercises) {
            $b = microtime(true);
            $c = $b - $a;
            $return['exit'] = __LINE__;
            $return['genTime'] = round($c, 4);
            return $this->json($return);
        }

        $dbChallengedNav = $this->getDoctrine()
            ->getRepository(Exercise::class)->createQueryBuilder('e')
            ->andwhere('e.patient = :patientId')
            ->setParameter('patientId', $this->patient)
            ->andwhere('e.id > :activityId')
            ->setParameter('activityId', $activityId)
            ->select('e.id as id')
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()->getResult();
        if (count($dbChallengedNav) > 0) {
            $return['nav']['nextMonth'] = array_pop($dbChallengedNav)['id'];
        }

        $dbChallengedNav = $this->getDoctrine()
            ->getRepository(Exercise::class)->createQueryBuilder('e')
            ->andwhere('e.patient = :patientId')
            ->setParameter('patientId', $this->patient)
            ->andwhere('e.id < :activityId')
            ->setParameter('activityId', $activityId)
            ->select('e.id as id')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getResult();
        if (count($dbChallengedNav) > 0) {
            $return['nav']['prevMonth'] = array_pop($dbChallengedNav)['id'];
        }

        foreach ($dbExercises as $dbExercise) {
            $exerciseDate = $dbExercise->getDateTimeStart()->format("Y-m-d");
            $exerciseDateStarted = $dbExercise->getDateTimeStart()->format("H:i:s");
            $exerciseDateFinished = $dbExercise->getDateTimeEnd()->format("H:i:s");
            $exerciseType = $dbExercise->getExerciseType()->getName();
            $exerciseTag = $dbExercise->getExerciseType()->getTag();
            $partOfDay = $dbExercise->getPartOfDay()->getName();

            $dayStepTotal = 0;
            $exerciseStepCountSum = 0;
            if ($exerciseType == "Walking") {
                if (is_null($dbExercise->getSteps())) {
                    $exerciseStepCount = [];
                    /** @var FitStepsIntraDay[] $dbIntraDaySteps */
                    $dbIntraDaySteps = $this->getDoctrine()
                        ->getRepository(FitStepsIntraDay::class)
                        ->findByDates(
                            $this->patient->getUuid(),
                            $exerciseDate,
                            $exerciseDateStarted,
                            $exerciseDateFinished,
                            $dbExercise->getTrackingDevice()->getId()
                        );
                    if (is_array($dbIntraDaySteps) && count($dbIntraDaySteps) > 0) {
                        foreach ($dbIntraDaySteps as $dbIntraDayStep) {
                            $exerciseStepCount[] = $dbIntraDayStep->getValue();
                        }

                        $exerciseStepCountSum = array_sum($exerciseStepCount);
                    }

                    $dbExercise->setSteps($exerciseStepCountSum);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($dbExercise);
                    $entityManager->flush();
                } else {
                    $exerciseStepCountSum = $dbExercise->getSteps();
                }

                /** @var FitStepsDailySummary[] $dbStepsForDay */
                $dbStepsForDay = $this->getDoctrine()
                    ->getRepository(FitStepsDailySummary::class)
                    ->findForDay($this->patient->getUuid(), $exerciseDate);
                if (is_array($dbStepsForDay) && count($dbStepsForDay) > 0) {
                    $dayStepTotal = $dbStepsForDay[0]->getValue();
                }
            }

            $daysExerciseSum = $this->getDoctrine()->getRepository(Exercise::class)->createQueryBuilder('c')
                ->leftJoin('c.patient', 'p')
                ->andWhere('p.id = :patientId')
                ->setParameter('patientId', $this->patient->getId())
                ->andWhere('c.dateTimeStart LIKE :dateTimeStart')
                ->setParameter('dateTimeStart', $dbExercise->getDateTimeStart()->format("Y-m-d") . "%")
                ->select('sum(c.duration) as sum')
                ->getQuery()->getOneOrNullResult()['sum'];

            $startedTimeStamp = intval($dbExercise->getDateTimeStart()->format("U")) * 1000;
            $return['results'] = [
                "id" => $dbExercise->getId(),
                "tracker" => $dbExercise->getTrackingDevice()->getName(),
                "partOfDay" => $partOfDay,
                "exerciseType" => $exerciseType,
                "exerciseTag" => $exerciseTag,
                "date" => $exerciseDate,
                "dateFormatted" => $dbExercise->getDateTimeStart()->format("l ") . ucfirst($partOfDay) . $dbExercise->getDateTimeStart()->format(", F jS"),
                "started" => $exerciseDateStarted,
                "finished" => $exerciseDateFinished,
                "duration" => round(($dbExercise->getDuration() / 60), 0),
                "durationTotal" => round((intval($daysExerciseSum) / 60), 0),
                "steps" => $exerciseStepCountSum,
                "stepsTotal" => $dayStepTotal,
                "altitudeMax" => round($dbExercise->getExerciseSummary()->getAltitudeMax(), 2),
                "altitudeMin" => round($dbExercise->getExerciseSummary()->getAltitudeMin(), 2),
                "altitudeGain" => round($dbExercise->getExerciseSummary()->getAltitudeMax() - $dbExercise->getExerciseSummary()->getAltitudeMin(),
                    2),
                "calorie" => round($dbExercise->getExerciseSummary()->getCalorie(), 2),
                "calorieTotal" => -1,
                "distance" => round($dbExercise->getExerciseSummary()->getDistance() / 1000, 2),
                "distanceTotal" => -1,
                "speedMax" => round($dbExercise->getExerciseSummary()->getSpeedMax(), 2),
                "speedMean" => round($dbExercise->getExerciseSummary()->getSpeedMean(), 2),
                "heartRateMax" => round($dbExercise->getExerciseSummary()->getHeartRateMax(), 2),
                "heartRateMin" => round($dbExercise->getExerciseSummary()->getHeartRateMin(), 2),
                "heartRateMean" => round($dbExercise->getExerciseSummary()->getHeartRateMean(), 2),
                "startedTimeStamp" => $startedTimeStamp,
                "maxDistance" => 0,
                "maxSpeed" => 0,
                "maxHeart" => 0,
                "maxAltitude" => 0,
                "liveData" => [],
                "locationData" => [],
            ];

            if ($return['results']['duration'] > 59) {
                $includeHours = true;
            } else {
                $includeHours = false;
            }

            $liveData = $dbExercise->getLiveDataBlob();
            if (!is_null($liveData)) {
                $liveData = json_decode(AppConstants::uncompressString($liveData), true);
                $return['results']['liveData'] = [];

                foreach ($liveData as $liveDatum) {
                    if (array_key_exists('distance', $liveDatum)) {
                        $key = 'distance';
                        if ($liveDatum[$key] > $return['results']['maxDistance']) {
                            $return['results']['maxDistance'] = round($liveDatum[$key], 2);
                        }
                    } else {
                        if (array_key_exists('speed', $liveDatum)) {
                            $key = 'speed';
                            if ($liveDatum[$key] > $return['results']['maxSpeed']) {
                                $return['results']['maxSpeed'] = round($liveDatum[$key], 2);
                            }
                        } else {
                            if (array_key_exists('heart_rate', $liveDatum)) {
                                $key = 'heart_rate';
                                if ($liveDatum[$key] > $return['results']['maxHeart']) {
                                    $return['results']['maxHeart'] = round($liveDatum[$key], 2);
                                }
                            } else {
                                $key = null;
                            }
                        }
                    }
                    if (!is_null($key)) {
                        if (!array_key_exists($key, $return['results']['liveData'])) {
                            $return['results']['liveData'][$key] = [];
                        }

                        $return['results']['liveData'][$key][] = [
                            "timestamp" => AppConstants::formatSeconds(round(($liveDatum['start_time'] - $startedTimeStamp) / 1000,
                                0), $includeHours),
                            "value" => round($liveDatum[$key], 2),
                        ];
                    }
                }
            }

            $locationData = $dbExercise->getLocationDataBlob();
            if (!is_null($locationData)) {
                $locationData = json_decode(AppConstants::uncompressString($locationData), true);

                foreach ($locationData as $locationDatum) {
                    if (array_key_exists("start_time", $locationDatum)) {
                        $newLocationArray = [];
                        $newLocationArray['start_time'] = AppConstants::formatSeconds(round(($locationDatum['start_time'] - $startedTimeStamp) / 1000,
                            0), $includeHours);
                        if (array_key_exists("latitude", $locationDatum)) {
                            $newLocationArray['latitude'] = $locationDatum['latitude'];
                        }
                        if (array_key_exists("longitude", $locationDatum)) {
                            $newLocationArray['longitude'] = $locationDatum['longitude'];
                        }
                        $return['results']['locationData'][] = $newLocationArray;

                        if (array_key_exists("altitude", $locationDatum)) {
                            if ($locationDatum['altitude'] > $return['results']['maxAltitude']) {
                                $return['results']['maxAltitude'] = round($locationDatum['altitude'], 2);
                            }
                            $return['results']['liveData']['altitude'][] = [
                                "timestamp" => $newLocationArray['start_time'],
                                "value" => round($locationDatum['altitude'], 2),
                            ];
                        }
                    }
                }
            }

        }

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/feed/activities/log", name="index_activity_log_no_param")
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index_activity_log_no_param()
    {
        return $this->index_activity_log(-1, date("Y-m-d"), '1y');
    }

    /**
     * @Route("/feed/activities/log/from/{dateFrom}", name="index_activity_log_with_date")
     *
     * @param string $dateFrom
     *
     * @return JsonResponse
     */
    public function index_activity_log_with_date(string $dateFrom)
    {
        return $this->index_activity_log(-1, $dateFrom, '1y');
    }

    /**
     * @Route("/feed/activities/log/from/{dateFrom}/within/{searchRange}", name="index_activity_log_with_date_within")
     *
     * @param string $dateFrom
     *
     * @param string $searchRange
     *
     * @return JsonResponse
     */
    public function index_activity_log_with_date_within(string $dateFrom, string $searchRange)
    {
        return $this->index_activity_log(-1, $dateFrom, $searchRange);
    }

    /**
     * @Route("/feed/activities/log/limited/{limit}", name="index_activity_log_with_limit")
     *
     * @param int $limit
     *
     * @return JsonResponse
     */
    public function index_activity_log_with_limit(int $limit)
    {
        return $this->index_activity_log($limit, date("Y-m-d"), '1y');
    }

    /**
     * @Route("/feed/activities/log/from/{dateFrom}/limited/{limit}", name="index_activity_log_with_limit_date")
     *
     * @param int    $limit
     *
     * @param string $dateFrom
     *
     * @return JsonResponse
     */
    public function index_activity_log_with_limit_date(int $limit, string $dateFrom)
    {
        return $this->index_activity_log($limit, $dateFrom, '1y');
    }

    /**
     * @Route("/feed/activities/log/within/{searchRange}/limited/{limit}", name="index_activity_log_with_limit_within")
     *
     * @param int    $limit
     *
     * @param string $searchRange
     *
     * @return JsonResponse
     */
    public function index_activity_log_with_limit_within(int $limit, string $searchRange)
    {
        return $this->index_activity_log($limit, date("Y-m-d"), $searchRange);
    }

    /**
     * @Route("/feed/activities/log/within/{searchRange}", name="index_activity_log_with_within")
     *
     * @param string $searchRange
     *
     * @return JsonResponse
     */
    public function index_activity_log_with_within(string $searchRange)
    {
        return $this->index_activity_log(-1, date("Y-m-d"), $searchRange);
    }



    /**
     * @Route("/loggedin/{deviceId}", name="index_loggedin_feedback")
     * @param string          $deviceId
     * @param ManagerRegistry $doctrine
     * @param Request         $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index_loggedin_feedback(string $deviceId, ManagerRegistry $doctrine, Request $request)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $requestBody = $request->getContent();
        $requestBody = str_replace("'", "\"", $requestBody);
        $requestBody = str_replace('&#39;', "'", $requestBody);
        $requestJson = json_decode($requestBody, false);

        //AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . ' ' . print_r($requestJson, true));

        if (property_exists($requestJson, "deviceInfo")) {
            $requestDeviceInfo = $requestJson->deviceInfo;
            $app = $requestJson->cordova;
            if ($requestJson->production == "development") {
                $version = $requestJson->version . "-dev";
                $production = false;
            } else {
                $version = $requestJson->version;
                $production = true;
            }
            if ($requestDeviceInfo->device == "Unknown") {
                $requestDeviceInfo->device = $requestJson->cordova;
            }
        } else {
            $requestDeviceInfo = $requestJson;
            $version = 'legacy';
            $app = 'unknown';
            $production = true;
        }

        $accessDevice = null;
        if ($deviceId > 0) {
            /** @var PatientDevice $accessDevice */
            $accessDevice = $this->getDoctrine()
                ->getRepository(PatientDevice::class)
                ->findOneBy([
                    'patient' => $this->patient,
                    'id' => $deviceId,
                    'production' => $production,
                ]);
        }

        if (!$accessDevice) {
            $deviceId = -1;

            /** @var PatientDevice $accessDevice */
            $accessDevice = $this->getDoctrine()
                ->getRepository(PatientDevice::class)
                ->findOneBy([
                    'patient' => $this->patient,
                    'os' => $requestDeviceInfo->os,
                    'browser' => $requestDeviceInfo->browser,
                    'device' => $requestDeviceInfo->device,
                    'os_version' => $requestDeviceInfo->os_version,
                    'browser_version' => $requestDeviceInfo->browser_version,
                    'production' => $production,
                ]);
        }

        if ($accessDevice && $deviceId > 0) {
            $accessDevice->setBrowser($requestDeviceInfo->browser);
            $accessDevice->setBrowserVersion($requestDeviceInfo->browser_version);
            $accessDevice->setDevice($requestDeviceInfo->device);
            $accessDevice->setOs($requestDeviceInfo->os);
            $accessDevice->setOsVersion($requestDeviceInfo->os_version);
            $accessDevice->setUserAgent($requestDeviceInfo->userAgent);
            $accessDevice->setVersion($version);
            $accessDevice->setApp($app);
            $accessDevice->setProduction($production);
            AppConstants::writeToLog('debug_transform.txt',
                __CLASS__ . '::' . __FUNCTION__ . '|' . __LINE__ . ' Device Updated');
        } else {
            if ($accessDevice) {
                AppConstants::writeToLog('debug_transform.txt',
                    __CLASS__ . '::' . __FUNCTION__ . '|' . __LINE__ . ' Device Found');
            } else {
                $accessDevice = new PatientDevice();
                $accessDevice->setPatient($this->patient);
                $accessDevice->setBrowser($requestDeviceInfo->browser);
                $accessDevice->setBrowserVersion($requestDeviceInfo->browser_version);
                $accessDevice->setDevice($requestDeviceInfo->device);
                $accessDevice->setOs($requestDeviceInfo->os);
                $accessDevice->setOsVersion($requestDeviceInfo->os_version);
                $accessDevice->setUserAgent($requestDeviceInfo->userAgent);
                $accessDevice->setVersion($version);
                $accessDevice->setApp($app);
                $accessDevice->setProduction($production);
                AppConstants::writeToLog('debug_transform.txt',
                    __CLASS__ . '::' . __FUNCTION__ . '|' . __LINE__ . ' New device');
            }
        }
        $accessDevice->setLastSeen(new DateTime());

        $entityManager = $doctrine->getManager();
        $entityManager->persist($accessDevice);
        $entityManager->flush();

        $return['id'] = $accessDevice->getId();

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }


    /**
     * @Route("/user/gsm", name="index_register_gsm")
     * @param ManagerRegistry $doctrine
     * @param Request         $request
     *
     * @return JsonResponse
     */
    public function index_register_gsm(ManagerRegistry $doctrine, Request $request)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $requestBody = $request->getContent();
        $requestBody = str_replace("'", "\"", $requestBody);
        $requestBody = str_replace('&#39;', "'", $requestBody);
        $requestJson = json_decode($requestBody, false);

        //AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . ' ' . print_r($requestJson, true));

        /** @var PatientDevice $accessDevice */
        $accessDevice = $this->getDoctrine()
            ->getRepository(PatientDevice::class)
            ->findOneBy([
                'patient' => $this->patient,
                'id' => $requestJson->deviceId,
            ]);

        if ($accessDevice) {
            $accessDevice->setSms($requestJson->fcm_id);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($accessDevice);
            $entityManager->flush();
        } else {
            AppConstants::writeToLog('debug_transform.txt',
                __LINE__ . ' Unknown Device ' . $requestJson->deviceId . ' for ' . $this->patient->getId());
        }

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/cmd/update/cordova/{currentVersion}", name="index_update_cordova")
     * @param string $currentVersion A users UUID
     *
     * @return JsonResponse
     */
    public function index_update_cordova(string $currentVersion)
    {
        $latestVersion = $_ENV['VERSION_CORDOVA'];

        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $return['latestVersion'] = $latestVersion;
        $return['yourVersion'] = $currentVersion;

        if (ip2long($latestVersion) > ip2long($currentVersion)) {
            $return['updateAvailable'] = true;
        } else {
            $return['updateAvailable'] = false;
        }

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }
}
