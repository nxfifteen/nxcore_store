<?php
/** @noinspection PhpUnused */

namespace App\Controller;

use App\Entity\ApiAccessLog;
use App\Entity\BodyWeight;
use App\Entity\Exercise;
use App\Entity\FitDistanceDailySummary;
use App\Entity\FitFloorsIntraDay;
use App\Entity\FitStepsDailySummary;
use App\Entity\FitStepsIntraDay;
use App\Entity\Patient;
use App\Entity\RpgChallengeFriends;
use App\Entity\RpgMilestones;
use App\Entity\RpgRewards;
use App\Entity\RpgRewardsAwarded;
use App\Service\AwardManager;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use LogicException;
use Sentry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FeedUxController extends AbstractController
{
    /** @var Patient $patient */
    private $patient;

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
        $a = microtime(TRUE);
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

            $interval = new \DateInterval('P' . strtoupper($searchRange));
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

            $b = microtime(TRUE);
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
                $return['durationsTotal'] = $return['durationsTotal'] + intval(round(($dbAllTimeDuration['duration'] / 60), 0));
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
                $return['partOfDaysTotal'] = $return['partOfDaysTotal'] + intval(round(($dbAllTimePartOfDay['duration'] / 60), 0));
            }
            $return['partOfDays'] = $newArray;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Exercise[] $dbExercises */
        if ($limit < 0) {
            $dbExercises = $this->getDoctrine()
                ->getRepository(Exercise::class)->createQueryBuilder('e')
                ->where('e.dateTimeStart <= :dateFrom')
                ->setParameter('dateFrom', $dateFrom->format("Y-m-d 23:59:59"))
                ->andWhere('e.dateTimeStart >= :dateBackTill')
                ->setParameter('dateBackTill', $dateBackTill->format("Y-m-d 00:00:00"))
                ->orderBy('e.dateTimeStart', 'DESC')
                ->getQuery()->getResult();
        } else {
            $dbExercises = $this->getDoctrine()
                ->getRepository(Exercise::class)->createQueryBuilder('e')
                ->where('e.dateTimeStart <= :dateFrom')
                ->setParameter('dateFrom', $dateFrom->format("Y-m-d 23:59:59"))
                ->andWhere('e.dateTimeStart >= :dateBackTill')
                ->setParameter('dateBackTill', $dateBackTill->format("Y-m-d 00:00:00"))
                ->orderBy('e.dateTimeStart', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()->getResult();
        }
        if (!$dbExercises) {
            $b = microtime(TRUE);
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

            $return['results'][] = [
                "id" => $dbExercise->getId(),
                "tracker" => $dbExercise->getTrackingDevice()->getName(),
                "partOfDay" => $partOfDay,
                "exerciseType" => $exerciseType,
                "exerciseTag" => $exerciseTag,
                "date" => $exerciseDate,
                "dateFormatted" => $dbExercise->getDateTimeStart()->format("l ") . $partOfDay . $dbExercise->getDateTimeStart()->format(", F jS"),
                "started" => $exerciseDateStarted,
                "finished" => $exerciseDateFinished,
                "duration" => round(($dbExercise->getDuration() / 60), 0),
                "steps" => $exerciseStepCountSum,
                "stepsTotal" => $dayStepTotal,
                "altitudeMax" => round($dbExercise->getExerciseSummary()->getAltitudeMax(), 3),
                "altitudeMin" => round($dbExercise->getExerciseSummary()->getAltitudeMin(), 3),
                "calorie" => round($dbExercise->getExerciseSummary()->getCalorie(), 3),
                "distance" => round($dbExercise->getExerciseSummary()->getDistance() / 1000, 3),
                "speedMax" => round($dbExercise->getExerciseSummary()->getSpeedMax(), 3),
                "speedMean" => round($dbExercise->getExerciseSummary()->getSpeedMean(), 3),
                "heartRateMax" => round($dbExercise->getExerciseSummary()->getHeartRateMax(), 3),
                "heartRateMin" => round($dbExercise->getExerciseSummary()->getHeartRateMin(), 3),
                "heartRateMean" => round($dbExercise->getExerciseSummary()->getHeartRateMean(), 3),
            ];

            if (array_key_exists($exerciseTag, $return['periodDurations'])) {
                $return['periodDurations'][$exerciseTag] = $return['periodDurations'][$exerciseTag] + round(($dbExercise->getDuration() / 60), 0);
            } else {
                $return['periodDurations'][$exerciseTag] = round(($dbExercise->getDuration() / 60), 0);
            }

            if (array_key_exists($partOfDay, $return['periodPartOfDays'])) {
                $return['periodPartOfDays'][$partOfDay] = $return['periodPartOfDays'][$partOfDay] + round(($dbExercise->getDuration() / 60), 0);
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

        $b = microtime(TRUE);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    private function setupRoute(String $userRole = 'ROLE_USER')
    {
        if (is_null($this->patient)) $this->patient = $this->getUser();

        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
            ]);
        });

        $this->hasAccess($userRole);
    }

    /**
     * @param String $userRole
     *
     * @throws LogicException If the Security component is not available
     */
    private function hasAccess(String $userRole = 'ROLE_USER')
    {
        $this->denyAccessUnlessGranted($userRole, NULL, 'User tried to access a page without having ' . $userRole);
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
        $a = microtime(TRUE);
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
            $b = microtime(TRUE);
            $c = $b - $a;
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

            $return['results'] = [
                "id" => $dbExercise->getId(),
                "tracker" => $dbExercise->getTrackingDevice()->getName(),
                "partOfDay" => $partOfDay,
                "exerciseType" => $exerciseType,
                "exerciseTag" => $exerciseTag,
                "date" => $exerciseDate,
                "dateFormatted" => $dbExercise->getDateTimeStart()->format("l ") . $partOfDay . $dbExercise->getDateTimeStart()->format(", F jS"),
                "started" => $exerciseDateStarted,
                "finished" => $exerciseDateFinished,
                "duration" => round(($dbExercise->getDuration() / 60), 0),
                "steps" => $exerciseStepCountSum,
                "stepsTotal" => $dayStepTotal,
                "altitudeMax" => round($dbExercise->getExerciseSummary()->getAltitudeMax(), 3),
                "altitudeMin" => round($dbExercise->getExerciseSummary()->getAltitudeMin(), 3),
                "calorie" => round($dbExercise->getExerciseSummary()->getCalorie(), 3),
                "distance" => round($dbExercise->getExerciseSummary()->getDistance() / 1000, 3),
                "speedMax" => round($dbExercise->getExerciseSummary()->getSpeedMax(), 3),
                "speedMean" => round($dbExercise->getExerciseSummary()->getSpeedMean(), 3),
                "heartRateMax" => round($dbExercise->getExerciseSummary()->getHeartRateMax(), 3),
                "heartRateMin" => round($dbExercise->getExerciseSummary()->getHeartRateMin(), 3),
                "heartRateMean" => round($dbExercise->getExerciseSummary()->getHeartRateMean(), 3),
            ];
        }

        $b = microtime(TRUE);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
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
     * @Route("/feed/dashboard", name="ux_aggregator")
     *
     * @param AwardManager $awardManager
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(AwardManager $awardManager)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(TRUE);

        $this->setupRoute();

        $return['status'] = "okay";
        $return['code'] = "200";

        $return['milestones'] = $this->getPatientMilestones();
        $return['steps'] = $this->getPatientSteps();
        $return['floors'] = $this->getPatientFloors();
        $return['distance'] = $this->getPatientDistance();
        $return['rpg_friends'] = $this->getPatientFriends();
        $return['rpg_challenge_friends'] = $this->getPatientChallengesFriends(TRUE);
        $return['awards'] = $this->getPatientAwards();
        $return['weight'] = $this->getPatientWeight();

        if (
            is_null($this->patient->getLastLoggedIn()) ||
            $this->patient->getLastLoggedIn()->format("Y-m-d") <> date("Y-m-d")
        ) {
            $this->patient->setLastLoggedIn(new DateTime());
            $this->patient->setLoginStreak($this->patient->getLoginStreak() + 1);
            $awardManager->giveXp($this->patient, 5, "First login for " . date("l jS F, Y"), new DateTime(date("Y-m-d 00:00:00")));

            if ($this->patient->getLoginStreak() % 5 == 0) {
                $awardManager->giveXp($this->patient, 5, "You've logged in " . $this->patient->getLoginStreak() . " days in a row!", new DateTime(date("Y-m-d 00:00:00")));
            }

            if ($this->patient->getLoginStreak() % 31 == 0) {
                $this->patient = $awardManager->giveBadge(
                    $this->patient,
                    [
                        'patients_name' => $this->patient->getFirstName(),
                        'html_title' => "Awarded the Full Month badge",
                        'header_image' => '../badges/streak_month_header.png',
                        "dateTime" => new DateTime(),
                        'relevant_date' => (new DateTime())->format("F jS, Y"),
                        "name" => "Full Month",
                        "repeat" => FALSE,
                        'badge_name' => 'Full Month',
                        'badge_xp' => 31,
                        'badge_image' => 'streak_month',
                        'badge_text' => "31 Day Streak",
                        'badge_longtext' => "You've logged in every day for a full month",
                        'badge_citation' => "You've logged in every day for a full month",
                    ]
                );
            }

            if ($this->patient->getLoginStreak() % 186 == 0) {
                $this->patient = $awardManager->giveBadge(
                    $this->patient,
                    [
                        'patients_name' => $this->patient->getFirstName(),
                        'html_title' => "Awarded the Six Month badge",
                        'header_image' => '../badges/streak_six_month_header.png',
                        "dateTime" => new DateTime(),
                        'relevant_date' => (new DateTime())->format("F jS, Y"),
                        "name" => "Six Months",
                        "repeat" => FALSE,
                        'badge_name' => 'Six Months',
                        'badge_xp' => 186,
                        'badge_image' => 'streak_six_month',
                        'badge_text' => "6 Month Streak",
                        'badge_longtext' => "You've logged in every day for a six month! That's incredible",
                        'badge_citation' => "You've logged in every day for a six month! That's incredible",
                    ]
                );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($this->patient);
            $entityManager->flush();
        }

        $b = microtime(TRUE);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @return null
     */
    private function getPatientMilestones()
    {
        $return = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        $return['distance'] = [];
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var float $distance */
        $distance = $this->getDoctrine()
            ->getRepository(FitDistanceDailySummary::class)
            ->getSumOfValues($this->patient->getUuid());
        if (!is_numeric($distance)) {
            return NULL;
        }
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

    /**
     * @param bool $pro
     *
     * @return array|null
     */
    private function getPatientSteps($pro = FALSE)
    {
        $returnSummary = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var FitStepsDailySummary[] $dbStepsSummary */
        $dbStepsSummary = $this->getDoctrine()
            ->getRepository(FitStepsDailySummary::class)
            ->findByDateRangeHistorical($this->patient->getUuid(), date("Y-m-d"), 0);
        if (count($dbStepsSummary) == 0) {
            return NULL;
        }
        $dbStepsSummary = array_pop($dbStepsSummary);

        $returnSummary['value'] = $dbStepsSummary->getValue();
        $returnSummary['goal'] = $dbStepsSummary->getGoal()->getGoal();
        if ($returnSummary['goal'] == 0) $returnSummary['goal'] = 10000;
        $returnSummary['progress'] = round(($returnSummary['value'] / $returnSummary['goal']) * 100, 0);
        $returnSummary['intraDay'] = $this->getPatientStepsIntraDay(date("Y-m-d"), $pro);

        if ($returnSummary['progress'] > 100) {
            $returnSummary['progressBar'] = 100;
        } else {
            $returnSummary['progressBar'] = $returnSummary['progress'];
        }

        return $returnSummary;
    }

    /**
     * @param String $date
     *
     * @param bool   $pro
     *
     * @return array|null
     */
    private function getPatientStepsIntraDay(String $date, $pro = FALSE)
    {
        if (is_null($this->patient)) $this->patient = $this->getUser();

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var FitStepsIntraDay[] $dbStepsSummary */
        $dbStepsIntraDay = $this->getDoctrine()
            ->getRepository(FitStepsIntraDay::class)
            ->findByDateRange($this->patient->getUuid(), $date);
        if (count($dbStepsIntraDay) == 0) {
            return NULL;
        }

        $timeStampsInTrack = [];

        $timeStampsInTrack['widget'] = [];
        $timeStampsInTrack['widget']['labels'] = [];
        $timeStampsInTrack['widget']['data'] = [];
        $timeStampsInTrack['widget']['data']['label'] = "Steps";
        $timeStampsInTrack['widget']['data']['data'] = [];

        $dbHours = $this->getHoursArray();
        if (count($dbStepsIntraDay) > 0) {
            /** @var FitStepsIntraDay $item */
            foreach ($dbStepsIntraDay as $item) {
                $hourIndex = intval($item->getDateTime()->format("G")) + 1;
                if (is_numeric($item->getValue())) {
                    $dbHours[$hourIndex] = $dbHours[$hourIndex] + $item->getValue();
                }
            }
        } else {
            return NULL;
        }

        if (!$pro) {
            $timeStampsInTrack['widget']['labels'] = array_keys($dbHours);
            $timeStampsInTrack['widget']['data']['data'] = $dbHours;
        } else {
            $timeStampsInTrack['widget'] = [];
            foreach ($dbHours as $dbHour => $value) {
                if ($value == 0) $value = 50;
                $timeStampsInTrack['widget'][] = [
                    "name" => $dbHour,
                    "value" => $value,
                ];
            }
        }

        return $timeStampsInTrack;
    }

    /**
     * @param int|NULL $currentHour
     *
     * @return array
     */
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

    /**
     * @return array|null
     */
    private function getPatientFloors()
    {
        $returnSummary = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var FitFloorsIntraDay[] $dbStepsSummary */
        $dbStepsSummary = $this->getDoctrine()
            ->getRepository(FitFloorsIntraDay::class)
            ->findByDateRange($this->patient->getUuid(), date("Y-m-d"));
        if (count($dbStepsSummary) == 0) {
            return NULL;
        }

        $returnSummary['value'] = 0;
        $returnSummary['goal'] = 10;
        if (count($dbStepsSummary) > 0) {
            /** @var FitFloorsIntraDay $item */
            foreach ($dbStepsSummary as $item) {
                if (is_numeric($item->getValue())) {
                    $returnSummary['value'] = $returnSummary['value'] + $item->getValue();
                }
            }
        } else {
            return NULL;
        }

        if ($returnSummary['goal'] > 0) {
            $returnSummary['progress'] = round(($returnSummary['value'] / $returnSummary['goal']) * 100, 0);
        } else {
            $returnSummary['progress'] = 100;
        }

        if ($returnSummary['progress'] > 100) {
            $returnSummary['progressBar'] = 100;
        } else {
            $returnSummary['progressBar'] = $returnSummary['progress'];
        }

        $returnSummary['intraDay'] = $this->getPatientFloorsIntraDay($dbStepsSummary);

        return $returnSummary;
    }

    /**
     * @param array $dbStepsIntraDay
     *
     * @return array|null
     */
    private function getPatientFloorsIntraDay(array $dbStepsIntraDay)
    {
        /** @var FitFloorsIntraDay[] $dbStepsIntraDay */

        $timeStampsInTrack = [];
        $timeStampsInTrack['widget'] = [];
        $timeStampsInTrack['widget']['labels'] = [];
        $timeStampsInTrack['widget']['data'] = [];
        $timeStampsInTrack['widget']['data']['label'] = "Steps";
        $timeStampsInTrack['widget']['data']['data'] = [];

        $dbHours = $this->getHoursArray();
        if (count($dbStepsIntraDay) > 0) {
            /** @var FitStepsIntraDay $item */
            foreach ($dbStepsIntraDay as $item) {
                if (is_numeric($item->getValue())) {
                    $dbHours[$item->getDateTime()->format("G")] = $dbHours[$item->getDateTime()->format("G")] + $item->getValue();
                }
            }
        } else {
            return NULL;
        }
        $timeStampsInTrack['widget']['labels'] = array_keys($dbHours);
        $timeStampsInTrack['widget']['data']['data'] = $dbHours;

        return $timeStampsInTrack;
    }

    /**
     * @return array|null
     */
    private function getPatientDistance()
    {
        $returnSummary = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var FitDistanceDailySummary[] $dbDistanceSummary */
        $dbDistanceSummary = $this->getDoctrine()
            ->getRepository(FitDistanceDailySummary::class)
            ->findByDateRangeHistorical($this->patient->getUuid(), date("Y-m-d"), 0);
        if (count($dbDistanceSummary) == 0) {
            return NULL;
        }

        $dbDistanceSummary = array_pop($dbDistanceSummary);

        $returnSummary['value'] = $dbDistanceSummary->getValue();
        $returnSummary['goal'] = $dbDistanceSummary->getGoal()->getGoal();
        if ($returnSummary['goal'] == 0) $returnSummary['goal'] = 3000;
        $returnSummary['progress'] = round(($returnSummary['value'] / $returnSummary['goal']) * 100, 0);
        if ($dbDistanceSummary->getUnitOfMeasurement()->getName()) {
            $returnSummary['value'] = round($returnSummary['value'] / 1000, 2);
            $returnSummary['goal'] = round($returnSummary['goal'] / 1000, 2);
            $returnSummary['units'] = "km";
        } else {
            $returnSummary['units'] = $dbDistanceSummary->getUnitOfMeasurement()->getName();
        }
        $returnSummary['intraDay'] = NULL;

        if ($returnSummary['progress'] > 100) {
            $returnSummary['progressBar'] = 100;
        } else {
            $returnSummary['progressBar'] = $returnSummary['progress'];
        }

        return $returnSummary;
    }

    /**
     * @param bool $summarise
     *
     * @return array
     */
    private function getPatientFriends(bool $summarise = FALSE)
    {
        $returnSummary = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();


        $returnSummary[0] = [
            "uuid" => $this->patient->getUuid(),
            "name" => $this->patient->getFirstName(),
            "avatar" => $this->patient->getAvatar(),
            "you" => "you",
        ];

        if (!$summarise) {
            $yourStepCount = 0;

            /** @noinspection PhpUndefinedMethodInspection */
            /** @var FitStepsDailySummary[] $dbStepsCounts */
            $dbStepsCounts = $this->getDoctrine()
                ->getRepository(FitStepsDailySummary::class)
                ->findByDateRangeHistorical($this->patient->getUuid(), date("Y-m-d"), 7);
            if (count($dbStepsCounts) > 0) {
                foreach ($dbStepsCounts as $dbStepsCount) {
                    $yourStepCount = $yourStepCount + $dbStepsCount->getValue();
                }
            }
            $returnSummary[0]['steps'] = $yourStepCount;
            $returnSummary[0]['steps_friendly'] = number_format($returnSummary[0]['steps'], 0);
        }

        foreach ($this->patient->getFriendsWith() as $otherPatient) {
            $friendIndex = count($returnSummary);

            $returnSummary[$friendIndex] = [
                "uuid" => $otherPatient['uuid'],
                "name" => $otherPatient['name'],
                "avatar" => $otherPatient['avatar'],
            ];

            $returnSummary[$friendIndex]['you'] = "friend";

            if (!$summarise) {
                $friendStepCount = 0;

                /** @noinspection PhpUndefinedMethodInspection */
                /** @var FitStepsDailySummary[] $dbStepsCounts */
                $dbStepsCounts = $this->getDoctrine()
                    ->getRepository(FitStepsDailySummary::class)
                    ->findByDateRangeHistorical($otherPatient['uuid'], date("Y-m-d"), 7);
                if (count($dbStepsCounts) > 0) {
                    foreach ($dbStepsCounts as $dbStepsCount) {
                        $friendStepCount = $friendStepCount + $dbStepsCount->getValue();
                    }
                }
                $returnSummary[$friendIndex]['steps'] = $friendStepCount;
                $returnSummary[$friendIndex]['steps_friendly'] = number_format($returnSummary[$friendIndex]['steps'], 0);
            }
        }

        if (!$summarise) {
            usort($returnSummary, function ($a, $b) {
                return ($a["steps"] < $b["steps"]);
            });
        } else {
            usort($returnSummary, function ($a, $b) {
                return strcmp($a["name"], $b["name"]);
            });
        }

        return $returnSummary;
    }

    /**
     * @param bool $summaryOnly
     *
     * @return array
     */
    private function getPatientChallengesFriends($summaryOnly = FALSE)
    {
        $runningFriendsChallenges = [];
        $runningFriendsChallenges['score'] = [];
        $runningFriendsChallenges['running'] = [];
        if (!$summaryOnly) $runningFriendsChallenges['toAccept'] = [];
        if (!$summaryOnly) $runningFriendsChallenges['pending'] = [];
        if (!$summaryOnly) $runningFriendsChallenges['completed'] = [];

        /** @var RpgChallengeFriends[] $dbChallenger */
        $dbChallenger = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findBy(["challenger" => $this->patient], ["endDate" => "DESC"]);
        foreach ($dbChallenger as $challengeFriends) {
            $runningFriendsChallenges = $this->populatePatientChallengesFriends($runningFriendsChallenges, $challengeFriends);
        }

        /** @var RpgChallengeFriends[] $dbChallenger */
        $dbChallenged = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findBy(["challenged" => $this->patient], ["endDate" => "DESC"]);
        foreach ($dbChallenged as $challengeFriends) {
            $runningFriendsChallenges = $this->populatePatientChallengesFriends($runningFriendsChallenges, $challengeFriends);
        }

        $runningFriendsChallenges['score'] = [
            "win" => $this->calcChallengeWins(),
            "lose" => $this->calcChallengeLoses(),
            "draw" => $this->calcChallengeDraws(),
        ];

        return $runningFriendsChallenges;
    }

    /**
     * @param array               $runningFriendsChallenges
     * @param RpgChallengeFriends $challengeFriends
     *
     * @return array
     */
    private function populatePatientChallengesFriends(array $runningFriendsChallenges, RpgChallengeFriends $challengeFriends)
    {
        if ($challengeFriends->getChallenger()->getId() == $this->getUser()->getId()) {
            $wasChallenger = TRUE;
            $opponent = $challengeFriends->getChallenged();
        } else {
            $wasChallenger = FALSE;
            $opponent = $challengeFriends->getChallenger();
        }

        if (is_null($challengeFriends->getStartDate()) && $challengeFriends->getChallenger()->getId() == $this->getUser()->getId()) {
            /*
             * Challenges other users have issued to you
             */
            if (array_key_exists("toAccept", $runningFriendsChallenges)) {
                $runningFriendsChallenges['toAccept'][] = [
                    "id" => $challengeFriends->getId(),
                    "opponent" => $opponent->getFirstName(),
                    "opponentAvatar" => $opponent->getAvatar(),
                    "criteria" => $this->friendlyNameChallengeCriteria($challengeFriends->getCriteria()),
                    "target" => $challengeFriends->getTarget(),
                    "duration" => $challengeFriends->getDuration(),
                    "invited" => $challengeFriends->getInviteDate()->format("Y-m-d H:i"),
                ];
            }
        } else if (is_null($challengeFriends->getStartDate()) && $challengeFriends->getChallenger()->getId() != $this->getUser()->getId()) {
            /*
             * Challenges you have sent to other users
             */
            if (array_key_exists("pending", $runningFriendsChallenges)) {
                $runningFriendsChallenges['pending'][] = [
                    "id" => $challengeFriends->getId(),
                    "opponent" => $opponent->getFirstName(),
                    "opponentAvatar" => $opponent->getAvatar(),
                    "criteria" => $this->friendlyNameChallengeCriteria($challengeFriends->getCriteria()),
                    "target" => $challengeFriends->getTarget(),
                    "duration" => $challengeFriends->getDuration(),
                    "invited" => $challengeFriends->getInviteDate()->format("Y-m-d H:i"),
                ];
            }
        } else {
            /*
             * Challenges that have ether finished or are currently running
             */
            if (!is_null($challengeFriends->getOutcome())) {
                /*
                 * Challenges that have finished
                 */
                switch ($challengeFriends->getOutcome()) {
                    case 6:
                        $outcome = "draw";
                        break;
                    case 5:
                        if ($wasChallenger) {
                            $outcome = "win";
                        } else {
                            $outcome = "lose";
                        }
                        break;
                    case 4:
                        if ($wasChallenger) {
                            $outcome = "lose";
                        } else {
                            $outcome = "win";
                        }
                        break;
                    case 2:
                    case 3:
                    case 1:
                        $outcome = "uncompleted";
                        break;
                    default:
                        $outcome = "unknown";
                        break;
                }

                if (array_key_exists("completed", $runningFriendsChallenges)) {
                    $index = count($runningFriendsChallenges['completed']);
                    $runningFriendsChallenges['completed'][$index] = [
                        "id" => $challengeFriends->getId(),
                        "opponent" => $opponent->getFirstName(),
                        "opponentAvatar" => $opponent->getAvatar(),
                        "criteria" => $this->friendlyNameChallengeCriteria($challengeFriends->getCriteria()),
                        "target" => $challengeFriends->getTarget(),
                        "startDate" => $challengeFriends->getStartDate()->format("Y-m-d"),
                        "endDate" => $challengeFriends->getEndDate()->format("Y-m-d"),
                        "progressDate" => 100,
                        "duration" => $challengeFriends->getDuration(),
                        "outcome" => $outcome,
                        "wasChallenger" => $wasChallenger,
                    ];

                    if ($wasChallenger) {
                        $runningFriendsChallenges['completed'][$index]['userDetail'] = [];
                        $runningFriendsChallenges['completed'][$index]['userDetail']['sum'] = $challengeFriends->getChallengerSum();
                        $runningFriendsChallenges['completed'][$index]['userDetail']['completion'] = round(($challengeFriends->getChallengerSum() / $challengeFriends->getTarget()) * 100, 0);
                        if ($runningFriendsChallenges['completed'][$index]['userDetail']['completion'] > 100) $runningFriendsChallenges['completed'][$index]['userDetail']['completion'] = 100;
                        switch ($outcome) {
                            case "win":
                                $runningFriendsChallenges['completed'][$index]['userDetail']['outcomeType'] = "success";
                                break;
                            case "lose":
                                $runningFriendsChallenges['completed'][$index]['userDetail']['outcomeType'] = "danger";
                                break;
                            case "draw":
                                $runningFriendsChallenges['completed'][$index]['userDetail']['outcomeType'] = "warning";
                                break;
                        }
                        $runningFriendsChallenges['completed'][$index]['userDetail']['detail'] = $challengeFriends->getChallengerDetails();

                        $runningFriendsChallenges['completed'][$index]['opponentDetail'] = [];
                        $runningFriendsChallenges['completed'][$index]['opponentDetail']['sum'] = $challengeFriends->getChallengedSum();
                        $runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] = round(($challengeFriends->getChallengedSum() / $challengeFriends->getTarget()) * 100, 0);
                        if ($runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] > 100) $runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] = 100;
                        switch ($outcome) {
                            case "win":
                                $runningFriendsChallenges['completed'][$index]['opponentDetail']['outcomeType'] = "danger";
                                break;
                            case "lose":
                                $runningFriendsChallenges['completed'][$index]['opponentDetail']['outcomeType'] = "success";
                                break;
                            case "draw":
                                $runningFriendsChallenges['completed'][$index]['opponentDetail']['outcomeType'] = "warning";
                                break;
                        }
                        $runningFriendsChallenges['completed'][$index]['opponentDetail']['detail'] = $challengeFriends->getChallengedDetails();
                    } else {
                        $runningFriendsChallenges['completed'][$index]['userDetail'] = [];
                        $runningFriendsChallenges['completed'][$index]['userDetail']['sum'] = $challengeFriends->getChallengedSum();
                        $runningFriendsChallenges['completed'][$index]['userDetail']['completion'] = round(($challengeFriends->getChallengedSum() / $challengeFriends->getTarget()) * 100, 0);
                        if ($runningFriendsChallenges['completed'][$index]['userDetail']['completion'] > 100) $runningFriendsChallenges['completed'][$index]['userDetail']['completion'] = 100;
                        switch ($outcome) {
                            case "win":
                                $runningFriendsChallenges['completed'][$index]['userDetail']['outcomeType'] = "success";
                                break;
                            case "lose":
                                $runningFriendsChallenges['completed'][$index]['userDetail']['outcomeType'] = "danger";
                                break;
                            case "draw":
                                $runningFriendsChallenges['completed'][$index]['userDetail']['outcomeType'] = "warning";
                                break;
                        }
                        $runningFriendsChallenges['completed'][$index]['userDetail']['detail'] = $challengeFriends->getChallengedDetails();

                        $runningFriendsChallenges['completed'][$index]['opponentDetail'] = [];
                        $runningFriendsChallenges['completed'][$index]['opponentDetail']['sum'] = $challengeFriends->getChallengerSum();
                        $runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] = round(($challengeFriends->getChallengerSum() / $challengeFriends->getTarget()) * 100, 0);
                        if ($runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] > 100) $runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] = 100;
                        switch ($outcome) {
                            case "win":
                                $runningFriendsChallenges['completed'][$index]['opponentDetail']['outcomeType'] = "danger";
                                break;
                            case "lose":
                                $runningFriendsChallenges['completed'][$index]['opponentDetail']['outcomeType'] = "success";
                                break;
                            case "draw":
                                $runningFriendsChallenges['completed'][$index]['opponentDetail']['outcomeType'] = "warning";
                                break;
                        }
                        $runningFriendsChallenges['completed'][$index]['opponentDetail']['detail'] = $challengeFriends->getChallengerDetails();
                    }
                }

            } else if (!is_null($challengeFriends->getEndDate())) {
                /*
                 * Challenges that are currently running
                 */
                $durationInSeconds = $challengeFriends->getEndDate()->format("U") - $challengeFriends->getStartDate()->format("U");
                $runForInSeconds = date("U") - $challengeFriends->getStartDate()->format("U");
                $runForInPercentage = ($runForInSeconds / $durationInSeconds) * 100;
                if ($runForInPercentage > 100) $runForInPercentage = 100;

                $index = count($runningFriendsChallenges['running']);
                $runningFriendsChallenges['running'][$index] = [
                    "id" => $challengeFriends->getId(),
                    "opponent" => $opponent->getFirstName(),
                    "opponentAvatar" => $opponent->getAvatar(),
                    "criteria" => $this->friendlyNameChallengeCriteria($challengeFriends->getCriteria()),
                    "target" => $challengeFriends->getTarget(),
                    "startDate" => $challengeFriends->getStartDate()->format("Y-m-d"),
                    "endDate" => $challengeFriends->getEndDate()->format("Y-m-d"),
                    "progressDate" => round($runForInPercentage, 2),
                    "duration" => $challengeFriends->getDuration(),
                ];

                if ($wasChallenger) {
                    $runningFriendsChallenges['running'][$index]['userDetail'] = [];
                    $runningFriendsChallenges['running'][$index]['userDetail']['sum'] = $challengeFriends->getChallengerSum();
                    $runningFriendsChallenges['running'][$index]['userDetail']['completion'] = round(($challengeFriends->getChallengerSum() / $challengeFriends->getTarget()) * 100, 0);
                    if ($runningFriendsChallenges['running'][$index]['userDetail']['completion'] > 100) $runningFriendsChallenges['running'][$index]['userDetail']['completion'] = 100;
                    if ($challengeFriends->getChallengerSum() > $challengeFriends->getChallengedSum()) {
                        $runningFriendsChallenges['running'][$index]['userDetail']['outcomeType'] = "success";
                    } else {
                        $runningFriendsChallenges['running'][$index]['userDetail']['outcomeType'] = "warning";
                    }
                    $runningFriendsChallenges['running'][$index]['userDetail']['detail'] = $challengeFriends->getChallengerDetails();

                    $runningFriendsChallenges['running'][$index]['opponentDetail'] = [];
                    $runningFriendsChallenges['running'][$index]['opponentDetail']['sum'] = $challengeFriends->getChallengedSum();
                    $runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] = round(($challengeFriends->getChallengedSum() / $challengeFriends->getTarget()) * 100, 0);
                    if ($runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] > 100) $runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] = 100;
                    if ($challengeFriends->getChallengerSum() < $challengeFriends->getChallengedSum()) {
                        $runningFriendsChallenges['running'][$index]['opponentDetail']['outcomeType'] = "success";
                    } else {
                        $runningFriendsChallenges['running'][$index]['opponentDetail']['outcomeType'] = "info";
                    }
                    $runningFriendsChallenges['running'][$index]['opponentDetail']['detail'] = $challengeFriends->getChallengedDetails();
                } else {
                    $runningFriendsChallenges['running'][$index]['userDetail'] = [];
                    $runningFriendsChallenges['running'][$index]['userDetail']['sum'] = $challengeFriends->getChallengedSum();
                    $runningFriendsChallenges['running'][$index]['userDetail']['completion'] = round(($challengeFriends->getChallengedSum() / $challengeFriends->getTarget()) * 100, 0);
                    if ($runningFriendsChallenges['running'][$index]['userDetail']['completion'] > 100) $runningFriendsChallenges['running'][$index]['userDetail']['completion'] = 100;
                    if ($challengeFriends->getChallengedSum() > $challengeFriends->getChallengerSum()) {
                        $runningFriendsChallenges['running'][$index]['userDetail']['outcomeType'] = "success";
                    } else {
                        $runningFriendsChallenges['running'][$index]['userDetail']['outcomeType'] = "warning";
                    }
                    $runningFriendsChallenges['running'][$index]['userDetail']['detail'] = $challengeFriends->getChallengedDetails();

                    $runningFriendsChallenges['running'][$index]['opponentDetail'] = [];
                    $runningFriendsChallenges['running'][$index]['opponentDetail']['sum'] = $challengeFriends->getChallengerSum();
                    $runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] = round(($challengeFriends->getChallengerSum() / $challengeFriends->getTarget()) * 100, 0);
                    if ($runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] > 100) $runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] = 100;
                    if ($challengeFriends->getChallengedSum() < $challengeFriends->getChallengerSum()) {
                        $runningFriendsChallenges['running'][$index]['opponentDetail']['outcomeType'] = "success";
                    } else {
                        $runningFriendsChallenges['running'][$index]['opponentDetail']['outcomeType'] = "info";
                    }
                    $runningFriendsChallenges['running'][$index]['opponentDetail']['detail'] = $challengeFriends->getChallengerDetails();
                }

                /** @noinspection PhpUndefinedMethodInspection */
                /** @var ApiAccessLog $apiLogLastSync */
                $apiLogLastSyncUser = $this->getDoctrine()
                    ->getRepository(ApiAccessLog::class)
                    ->findOneBy(["patient" => $this->patient, "entity" => $challengeFriends->getCriteria()]);
                if (!is_null($apiLogLastSyncUser)) {
                    $runningFriendsChallenges['running'][$index]['userDetail']['lastPulled'] = $apiLogLastSyncUser->getLastRetrieved()->format("Y-m-d H:i:s");
                } else {
                    $runningFriendsChallenges['running'][$index]['userDetail']['lastPulled'] = "0000-00-00 00:00:00";
                }

                /** @var ApiAccessLog $apiLogLastSync */
                $apiLogLastSyncOpponent = $this->getDoctrine()
                    ->getRepository(ApiAccessLog::class)
                    ->findOneBy(["patient" => $opponent, "entity" => $challengeFriends->getCriteria()]);
                if (!is_null($apiLogLastSyncOpponent)) {
                    $runningFriendsChallenges['running'][$index]['opponentDetail']['lastPulled'] = $apiLogLastSyncOpponent->getLastRetrieved()->format("Y-m-d H:i:s");
                } else {
                    $runningFriendsChallenges['running'][$index]['opponentDetail']['lastPulled'] = "0000-00-00 00:00:00";
                }
            }
        }

        return $runningFriendsChallenges;
    }

    /**
     * @param string $getCriteria
     *
     * @return string
     */
    private function friendlyNameChallengeCriteria(string $getCriteria)
    {
        switch ($getCriteria) {
            case "FitStepsDailySummary":
                return "Steps";
            default:
                return $getCriteria;
        }
    }

    private function calcChallengeWins()
    {
        /** @var int $dbChallenger */
        $dbChallenger = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findChallengeWins($this->patient);

        return $dbChallenger;
    }

    private function calcChallengeLoses()
    {
        /** @var int $dbChallenger */
        $dbChallenger = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findChallengeLoses($this->patient);

        return $dbChallenger;
    }

    private function calcChallengeDraws()
    {
        /** @var int $dbChallenger */
        $dbChallenger = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findChallengeDraws($this->patient);

        return $dbChallenger;
    }

    /**
     * @return array
     */
    private function getPatientAwards()
    {
        $returnSummary = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        foreach ($this->patient->getRewards() as $reward) {
            if (array_key_exists($reward->getReward()->getId(), $returnSummary)) {
                $returnSummary[$reward->getReward()->getId()]['count']++;
                if (strtotime($returnSummary[$reward->getReward()->getId()]['awarded']) < $reward->getDatetime()->format("U")) {
                    $returnSummary[$reward->getReward()->getId()]['awarded'] = $reward->getDatetime()->format("Y-m-d");
                }
            } else {
                $returnSummary[$reward->getReward()->getId()] = [
                    "id" => $reward->getReward()->getId(),
                    "name" => $reward->getReward()->getName(),
                    "awarded" => $reward->getDatetime()->format("Y-m-d"),
                    "image" => $reward->getReward()->getImageUrl(),
                    "text" => $reward->getReward()->getText(),
                    "longtext" => $reward->getReward()->getTextLong(),
                    "count" => 1,
                ];
            }
        }

        return $returnSummary;
    }

    /**
     * @param bool        $pro
     * @param String|null $date
     * @param int         $dateRange
     *
     * @return array
     */
    private function getPatientWeight($pro = FALSE, String $date = NULL, int $dateRange = 31)
    {
        $returnSummary = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        if (is_null($date)) $date = date("Y-m-d");

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BodyWeight[] $product */
        $product = $this->getDoctrine()
            ->getRepository(BodyWeight::class)
            ->findByDateRangeHistorical($this->patient->getUuid(), $date, $dateRange);
        if (count($product) == 0) {
            return NULL;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BodyWeight[] $productFirst */
        $productFirst = $this->getDoctrine()
            ->getRepository(BodyWeight::class)
            ->findFirst($this->patient->getUuid());

        $returnSummary['value'] = 0;
        $returnSummary['unit'] = 0;
        $returnSummary['goal'] = 0;
        $returnSummary['progress'] = 0;
        $returnSummary['since'] = $product[0]->getDateTime()->format("M Y");

        if ($pro) {
            $returnSummary['widget'] = [];
            $buildArrayRecorded = [];
            $buildArrayAverage = [];
            $buildArrayAverageLoss = [];

            if (count($product) > 0) {
                $countWeight = 0;
                $sumWeight = 0;

                /** @var BodyWeight[] $product */
                foreach ($product as $item) {
                    if (is_numeric($item->getMeasurement())) {
                        $returnSummary['value'] = round($item->getMeasurement(), 2);
                        $returnSummary['unit'] = $item->getUnitOfMeasurement()->getName();

                        $buildArrayRecorded[] = [
                            "name" => intval($item->getDateTime()->format("U")),
                            "value" => round($item->getMeasurement(), 2),
                        ];

                        $sumWeight = $sumWeight + $item->getMeasurement();
                        $countWeight++;

                        if (count($buildArrayAverage) == 0) {
                            $buildArrayAverage[] = [
                                "name" => intval($item->getDateTime()->format("U")),
                                "value" => round($item->getMeasurement(), 2),
                            ];
                        } else {
                            $newAvgIndex = count($buildArrayAverage) - 1;
                            $buildArrayAverage[] = [
                                "name" => intval($item->getDateTime()->format("U")),
                                "value" => round($sumWeight / $countWeight, 2),
                            ];

                            $buildArrayAverageLoss[] = $buildArrayAverage[$newAvgIndex]['value'] - $buildArrayAverage[$newAvgIndex - 1]['value'];
                        }
                    }
                }

                $returnSummary['widget'][] = [
                    "name" => "Recorded " . $returnSummary['unit'],
                    "series" => $buildArrayRecorded,
                ];
                $returnSummary['widget'][] = [
                    "name" => "Average " . $returnSummary['unit'],
                    "series" => $buildArrayAverage,
                ];

                $returnSummary['loss'] = $buildArrayAverageLoss;
            }


        } else {

            $returnSummary['loss'] = [];

            $returnSummary['widget'] = [];
            $returnSummary['widget']['labels'] = [];
            $returnSummary['widget']['data'] = [];
            $returnSummary['widget']['axis']['min'] = 0;
            $returnSummary['widget']['axis']['max'] = 0;

            $buildArrayAverageLoss = [];

            if (count($product) > 0) {
                /** @var BodyWeight[] $product */
                foreach ($product as $item) {
                    if (is_numeric($item->getMeasurement())) {
                        $returnSummary['value'] = round($item->getMeasurement(), 2);
                        $returnSummary['unit'] = $item->getUnitOfMeasurement()->getName();

                        if (is_numeric($item->getPatientGoal()->getGoal())) {
                            $returnSummary['widget']['data'][0]['label'] = "Goal " . $item->getPatientGoal()->getUnitOfMeasurement()->getName();
                            $returnSummary['widget']['data'][0]['data'][] = round($item->getPatientGoal()->getGoal(), 2);
                        }

                        $returnSummary['widget']['data'][1]['label'] = "Recorded " . $item->getUnitOfMeasurement()->getName();

                        if ($item->getMeasurement() == 0) {
                            $newAvgIndex = count($returnSummary['widget']['data'][1]['data']) - 1;
                            $returnSummary['widget']['data'][1]['data'][] = $returnSummary['widget']['data'][1]['data'][$newAvgIndex];
                            $returnSummary['value'] = $returnSummary['widget']['data'][1]['data'][$newAvgIndex];
                        } else {
                            $returnSummary['widget']['data'][1]['data'][] = round($item->getMeasurement(), 2);
                        }

                        if (count($returnSummary['widget']['data'][1]['data']) == 1) {
                            $returnSummary['widget']['data'][2]['label'] = "Average " . $item->getUnitOfMeasurement()->getName();
                            $returnSummary['widget']['data'][2]['data'][] = round($item->getMeasurement(), 2);

                            $buildArrayAverageLoss[] = 0;
                        } else {
                            $newAvgIndex = count($returnSummary['widget']['data'][2]['data']) - 1;

                            $returnSummary['widget']['data'][2]['label'] = "Average " . $item->getUnitOfMeasurement()->getName();
                            $countWeight = $returnSummary['widget']['data'][1]['data'];
                            $sumWeight = array_sum($returnSummary['widget']['data'][1]['data']);
                            $returnSummary['widget']['data'][2]['data'][] = round($sumWeight / count($countWeight), 2);

                            $buildArrayAverageLoss[] = round($returnSummary['widget']['data'][2]['data'][$newAvgIndex] - ($sumWeight / count($countWeight)), 2);
                        }

                        $returnSummary['widget']['labels'][] = $item->getDateTime()->format("D, jS M");

                        if ($returnSummary['widget']['axis']['min'] == 0 || $returnSummary['widget']['axis']['min'] > $returnSummary['value']) {
                            $returnSummary['widget']['axis']['min'] = $returnSummary['value'];
                        }

                        if ($returnSummary['widget']['axis']['max'] == 0 || $returnSummary['widget']['axis']['max'] < $returnSummary['value']) {
                            $returnSummary['widget']['axis']['max'] = $returnSummary['value'];
                        }
                    }
                }

                $returnSummary['loss'] = $buildArrayAverageLoss;
            }

            $firstMeasurement = round($productFirst[0]->getMeasurement(), 2);
            $currentMeasurement = round($product[(count($product) - 1)]->getMeasurement(), 2);
            $targetMeasurement = round($product[(count($product) - 1)]->getPatientGoal()->getGoal(), 2);
            $totalToReach = round($firstMeasurement - $targetMeasurement, 2);
            $totalProgress = round($firstMeasurement - $currentMeasurement, 2);
            $progressPercentage = round(($totalProgress / $totalToReach) * 100, 2);
            if ($progressPercentage > 100) $progressPercentage = 100;

            $returnSummary['goal'] = $targetMeasurement;
            $returnSummary['progress'] = $progressPercentage;
            $returnSummary['widget']['axis']['min'] = $returnSummary['widget']['axis']['min'] - 1;
            $returnSummary['widget']['axis']['max'] = $returnSummary['widget']['axis']['max'] + 1;
        }

        return $returnSummary;
    }

    /**
     * @Route("/feed/pvp/challenges", name="ux_aggregator_index_pvp_challenges")
     */
    public function index_pvp_challenges()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(TRUE);

        $this->setupRoute();

        $return['status'] = "okay";
        $return['code'] = 200;

        $return['rpg_challenge_friends'] = $this->getPatientChallengesFriends();

        $b = microtime(TRUE);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/feed/pvp/challenges/{badgeId}", name="ux_aggregator_index_pvp_challenges_detail")
     * @param int $badgeId
     *
     * @return JsonResponse
     */
    public function index_pvp_challenges_detail(int $badgeId)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(TRUE);

        $return['nav'] = [
            "nextMonth" => '',
            "prevMonth" => '',
        ];

        $this->setupRoute();

        /** @var RpgChallengeFriends $dbChallenger */
        $dbChallenger = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findOneBy(["id" => $badgeId], ["startDate" => "DESC"]);

        if (
            $dbChallenger->getChallenger()->getUuid() != $this->patient->getUuid() &&
            $dbChallenger->getChallenged()->getUuid() != $this->patient->getUuid()
        ) {
            $exception = $this->createAccessDeniedException("You weren't part of this challenge");
            throw $exception;
        }

        if ($dbChallenger->getChallenger()->getId() == $this->getUser()->getId()) {
            $wasChallenger = TRUE;
            $opponent = $dbChallenger->getChallenged();
        } else {
            $wasChallenger = FALSE;
            $opponent = $dbChallenger->getChallenger();
        }

        switch ($dbChallenger->getOutcome()) {
            case 6:
                $outcome = "draw";
                break;
            case 5:
                if ($wasChallenger) {
                    $outcome = "win";
                } else {
                    $outcome = "lose";
                }
                break;
            case 4:
                if ($wasChallenger) {
                    $outcome = "lose";
                } else {
                    $outcome = "win";
                }
                break;
            case 1:
            case 2:
            case 3:
                $outcome = "uncompleted";
                break;
            default:
                $outcome = "unknown";
                break;
        }

        if ($outcome == "unknown") {
            $dbChallengedNav = $this->getDoctrine()
                ->getRepository(RpgChallengeFriends::class)->createQueryBuilder('c')
                ->leftJoin('c.challenger', 'per')
                ->leftJoin('c.challenged', 'ped')
                ->andWhere('per.id = :patientId OR ped.id = :patientId')
                ->setParameter('patientId', $this->patient->getId())
                ->andWhere('c.id > :queriedId')
                ->setParameter('queriedId', $badgeId)
                ->andWhere('c.outcome IS NULL')
                ->select('c.id as id')
                ->orderBy('c.id', 'ASC')
                ->setMaxResults(1)
                ->getQuery()->getResult();
            if (count($dbChallengedNav) > 0) {
                $return['nav']['nextMonth'] = array_pop($dbChallengedNav)['id'];
            }

            $dbChallengedNav = $this->getDoctrine()
                ->getRepository(RpgChallengeFriends::class)->createQueryBuilder('c')
                ->leftJoin('c.challenger', 'per')
                ->leftJoin('c.challenged', 'ped')
                ->andWhere('per.id = :patientId OR ped.id = :patientId')
                ->setParameter('patientId', $this->patient->getId())
                ->andWhere('c.id < :queriedId')
                ->setParameter('queriedId', $badgeId)
                ->andWhere('c.outcome IS NULL')
                ->select('c.id as id')
                ->orderBy('c.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()->getResult();
            if (count($dbChallengedNav) > 0) {
                $return['nav']['prevMonth'] = array_pop($dbChallengedNav)['id'];
            }
        } else {
            $dbChallengedNav = $this->getDoctrine()
                ->getRepository(RpgChallengeFriends::class)->createQueryBuilder('c')
                ->leftJoin('c.challenger', 'per')
                ->leftJoin('c.challenged', 'ped')
                ->andWhere('per.id = :patientId OR ped.id = :patientId')
                ->setParameter('patientId', $this->patient->getId())
                ->andWhere('c.id > :queriedId')
                ->setParameter('queriedId', $badgeId)
                ->andWhere('c.outcome IS NOT NULL')
                ->select('c.id as id')
                ->orderBy('c.id', 'ASC')
                ->setMaxResults(1)
                ->getQuery()->getResult();
            if (count($dbChallengedNav) > 0) {
                $return['nav']['nextMonth'] = array_pop($dbChallengedNav)['id'];
            }

            $dbChallengedNav = $this->getDoctrine()
                ->getRepository(RpgChallengeFriends::class)->createQueryBuilder('c')
                ->leftJoin('c.challenger', 'per')
                ->leftJoin('c.challenged', 'ped')
                ->andWhere('per.id = :patientId OR ped.id = :patientId')
                ->setParameter('patientId', $this->patient->getId())
                ->andWhere('c.id < :queriedId')
                ->setParameter('queriedId', $badgeId)
                ->andWhere('c.outcome IS NOT NULL')
                ->select('c.id as id')
                ->orderBy('c.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()->getResult();
            if (count($dbChallengedNav) > 0) {
                $return['nav']['prevMonth'] = array_pop($dbChallengedNav)['id'];
            }
        }

        $return['id'] = $dbChallenger->getId();
        $return['opponent'] = $opponent->getFirstName();
        $return['opponentAvatar'] = $opponent->getAvatar();
        $return['user'] = $this->patient->getFirstName();
        $return['userAvatar'] = $this->patient->getAvatar();
        $return['criteria'] = $this->friendlyNameChallengeCriteria($dbChallenger->getCriteria());
        $return['target'] = $dbChallenger->getTarget();
        $return['outcome'] = $outcome;
        $return['startDate'] = $dbChallenger->getStartDate()->format("Y-m-d");
        if ($outcome != "unknown") {
            $return['endDate'] = $dbChallenger->getCompletedAt()->format("Y-m-d");
            $return['duration'] = $dbChallenger->getDuration();
        } else {
            $return['endDate'] = $dbChallenger->getEndDate()->format("Y-m-d");
            $return['duration'] = $dbChallenger->getDuration();
        }
        $return['timeElapsed'] = 0;
        $return['timeLeft'] = 0;
        $return['userDetail'] = [
            "sum" => "",
            "raw" => "",
            "lastPulled" => "",
            "progress" => 0,
        ];
        $return['opponentDetail'] = [
            "sum" => "",
            "raw" => "",
            "lastPulled" => "",
            "progress" => 0,
            "diff" => "",
            "diffRaw" => "",
            "direction" => "",
        ];
        $return['pacesetterDetail'] = [
            "sum" => "",
            "raw" => "",
            "diff" => "",
            "diffRaw" => "",
            "direction" => "",
        ];
        $return['challenge'] = [
            "axis" => [
                "min" => 0,
                "max" => 0,
            ],
            "labels" => [],
            "data" => [
                [
                    "label" => "user",
                    "data" => [],
                ],
                [
                    "label" => "opponent",
                    "data" => [],
                ],
                [
                    "label" => "target",
                    "data" => [],
                ],
            ],
        ];
        $return['widget'] = [
            "axis" => [
                "min" => 0,
                "max" => 0,
            ],
            "labels" => [],
            "data" => [
                [
                    "label" => "Pacesetter",
                    "data" => [],
                ],
                [
                    "label" => "user",
                    "data" => [],
                ],
                [
                    "label" => "opponent",
                    "data" => [],
                ],
            ],
        ];

        $return['widget']['data'][1]['label'] = $this->patient->getFirstName();
        $return['widget']['data'][2]['label'] = $opponent->getFirstName();

        $return['challenge']['data'][0]['label'] = $this->patient->getFirstName();
        $return['challenge']['data'][1]['label'] = $opponent->getFirstName();

        $runForInSeconds = date("U") - $dbChallenger->getStartDate()->format("U");
        $return['timeElapsed'] = round(($runForInSeconds / 60 / 60 / 24), 0) - 1;
        $return['timeLeft'] = $dbChallenger->getDuration() - $return['timeElapsed'];

        foreach ($dbChallenger->getChallengerDetails() as $key => $value) {
            $return['widget']['labels'][] = date("D", strtotime($key));
            $return['challenge']['labels'][] = date("D", strtotime($key));
        }

        $userSum = 0;
        $opponentSum = 0;

        $pacesetterDaily = round($dbChallenger->getTarget() / $dbChallenger->getDuration(), 0, PHP_ROUND_HALF_UP);
        $pacesetterHourly = $pacesetterDaily / 24;
        $return['pacesetterDetail']['raw'] = round(($runForInSeconds / 60 / 60) * $pacesetterHourly, 0, PHP_ROUND_HALF_UP);
        $return['pacesetterDetail']['sum'] = number_format($return['pacesetterDetail']['raw'], 0);

        if ($wasChallenger) {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @var ApiAccessLog $apiLogLastSync */
            $apiLogLastSyncUser = $this->getDoctrine()
                ->getRepository(ApiAccessLog::class)
                ->findOneBy(["patient" => $this->patient, "entity" => $dbChallenger->getCriteria()]);
            if (!is_null($apiLogLastSyncUser)) {
                $return['userDetail']['lastPulled'] = $apiLogLastSyncUser->getLastRetrieved()->format("Y-m-d H:i:s");
            } else {
                $return['userDetail']['lastPulled'] = "0000-00-00 00:00:00";
            }

            /** @var ApiAccessLog $apiLogLastSync */
            $apiLogLastSyncOpponent = $this->getDoctrine()
                ->getRepository(ApiAccessLog::class)
                ->findOneBy(["patient" => $opponent, "entity" => $dbChallenger->getCriteria()]);
            if (!is_null($apiLogLastSyncOpponent)) {
                $return['opponentDetail']['lastPulled'] = $apiLogLastSyncOpponent->getLastRetrieved()->format("Y-m-d H:i:s");
            } else {
                $return['opponentDetail']['lastPulled'] = "0000-00-00 00:00:00";
            }

            $return['userDetail']['raw'] = $dbChallenger->getChallengerSum();
            $return['userDetail']['sum'] = number_format($return['userDetail']['raw'], 0);
            $return['opponentDetail']['raw'] = $dbChallenger->getChallengedSum();
            $return['opponentDetail']['sum'] = number_format($return['opponentDetail']['raw'], 0);

            $return['userDetail']['progress'] = 100 - round(($dbChallenger->getChallengerSum() / $dbChallenger->getTarget()) * 100, 0);
            $return['opponentDetail']['progress'] = 100 - round(($dbChallenger->getChallengedSum() / $dbChallenger->getTarget()) * 100, 0);

            foreach ($dbChallenger->getChallengerDetails() as $key => $value) {
                $return['widget']['data'][0]['data'][] = $pacesetterDaily;
                $return['challenge']['data'][2]['data'][] = $dbChallenger->getTarget();
                if ($value > 0) {
                    $userSum = $userSum + $value;
                    $return['challenge']['data'][0]['data'][] = $userSum;

                    $return['widget']['data'][1]['data'][] = $value;
                    if ($value > $return['widget']['axis']['max']) {
                        $return['widget']['axis']['max'] = $value;
                    }
                }
            }

            foreach ($dbChallenger->getChallengedDetails() as $key => $value) {
                if ($value > 0) {
                    $opponentSum = $opponentSum + $value;
                    $return['challenge']['data'][1]['data'][] = $opponentSum;

                    $return['widget']['data'][2]['data'][] = $value;
                    if ($value > $return['widget']['axis']['max']) {
                        $return['widget']['axis']['max'] = $value;
                    }
                }
            }
        } else {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @var ApiAccessLog $apiLogLastSync */
            $apiLogLastSyncUser = $this->getDoctrine()
                ->getRepository(ApiAccessLog::class)
                ->findOneBy(["patient" => $this->patient, "entity" => $dbChallenger->getCriteria()]);
            if (!is_null($apiLogLastSyncUser)) {
                $return['opponentDetail']['lastPulled'] = $apiLogLastSyncUser->getLastRetrieved()->format("Y-m-d H:i:s");
            } else {
                $return['opponentDetail']['lastPulled'] = "0000-00-00 00:00:00";
            }

            /** @var ApiAccessLog $apiLogLastSync */
            $apiLogLastSyncOpponent = $this->getDoctrine()
                ->getRepository(ApiAccessLog::class)
                ->findOneBy(["patient" => $opponent, "entity" => $dbChallenger->getCriteria()]);
            if (!is_null($apiLogLastSyncOpponent)) {
                $return['userDetail']['lastPulled'] = $apiLogLastSyncOpponent->getLastRetrieved()->format("Y-m-d H:i:s");
            } else {
                $return['userDetail']['lastPulled'] = "0000-00-00 00:00:00";
            }

            $return['userDetail']['raw'] = $dbChallenger->getChallengedSum();
            $return['userDetail']['sum'] = number_format($return['userDetail']['raw'], 0);
            $return['opponentDetail']['raw'] = $dbChallenger->getChallengerSum();
            $return['opponentDetail']['sum'] = number_format($return['opponentDetail']['raw'], 0);

            $return['userDetail']['progress'] = 100 - round(($dbChallenger->getChallengedSum() / $dbChallenger->getTarget()) * 100, 0);
            $return['opponentDetail']['progress'] = 100 - round(($dbChallenger->getChallengerSum() / $dbChallenger->getTarget()) * 100, 0);

            foreach ($dbChallenger->getChallengerDetails() as $key => $value) {
                $return['widget']['data'][0]['data'][] = round($dbChallenger->getTarget() / $dbChallenger->getDuration(), 0, PHP_ROUND_HALF_UP);
                $return['challenge']['data'][2]['data'][] = $dbChallenger->getTarget();
                if ($value > 0) {
                    $userSum = $userSum + $value;
                    $return['challenge']['data'][1]['data'][] = $userSum;

                    $return['widget']['data'][2]['data'][] = $value;
                    if ($value > $return['widget']['axis']['max']) {
                        $return['widget']['axis']['max'] = $value;
                    }
                }
            }

            foreach ($dbChallenger->getChallengedDetails() as $key => $value) {
                if ($value > 0) {
                    $opponentSum = $opponentSum + $value;
                    $return['challenge']['data'][0]['data'][] = $opponentSum;

                    $return['widget']['data'][1]['data'][] = $value;
                    if ($value > $return['widget']['axis']['max']) {
                        $return['widget']['axis']['max'] = $value;
                    }
                }
            }
        }

        if ($return['userDetail']['progress'] > 100) $return['userDetail']['progress'] = 100;
        if ($return['userDetail']['progress'] < 0) $return['userDetail']['progress'] = 0;

        if ($return['opponentDetail']['progress'] > 100) $return['opponentDetail']['progress'] = 100;
        if ($return['opponentDetail']['progress'] < 0) $return['opponentDetail']['progress'] = 0;

        $return['pacesetterDetail']['diffRaw'] = $return['userDetail']['raw'] - $return['pacesetterDetail']['raw'];
        if ($return['pacesetterDetail']['diffRaw'] < 0) {
            $return['pacesetterDetail']['diffRaw'] = $return['pacesetterDetail']['diffRaw'] * -1;
            $return['pacesetterDetail']['direction'] = "behind";
        } else {
            $return['pacesetterDetail']['direction'] = "ahead of";
        }
        $return['pacesetterDetail']['diff'] = number_format($return['pacesetterDetail']['diffRaw'], 0);

        $return['opponentDetail']['diffRaw'] = $return['userDetail']['raw'] - $return['opponentDetail']['raw'];
        if ($return['opponentDetail']['diffRaw'] < 0) {
            $return['opponentDetail']['diffRaw'] = $return['opponentDetail']['diffRaw'] * -1;
            $return['opponentDetail']['direction'] = "behind";
        } else {
            $return['opponentDetail']['direction'] = "ahead of";
        }
        $return['opponentDetail']['diff'] = number_format($return['opponentDetail']['diffRaw'], 0);

        $b = microtime(TRUE);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/forms/challenges/new", name="index_pvp_challenge_options")
     */
    public function index_pvp_challenge_options()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(TRUE);

        $this->setupRoute();

        $return['status'] = "okay";
        $return['code'] = "200";

        $return['friends'] = $this->getPatientFriends(TRUE);
        $return['criteria'] = [
            "steps",
        ];
        $return['targets'] = [
            10000, 30000, 50000, 70000, 100000,
        ];
        $return['durations'] = [
            1, 2, 3, 5, 7, 10, 14, 31,
        ];

        $b = microtime(TRUE);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/feed/pvp/leaderboard", name="ux_aggregator_index_pvp_leaderboard")
     */
    public function index_pvp_leaderboard()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(TRUE);

        $this->setupRoute();

        $return['status'] = "okay";
        $return['code'] = "200";

        $return['maxSteps'] = 0;
        $return['friends'] = $this->getPatientFriends();
        foreach ($return['friends'] as $friend) {
            if ($friend['steps'] > $return['maxSteps']) {
                $return['maxSteps'] = $friend['steps'];
            }
        }

        $b = microtime(TRUE);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/feed/achievements/awards/{badgeId}", name="index_achievements_badges_detail")
     * @param int $badgeId
     *
     * @return JsonResponse
     */
    public function index_achievements_badges_detail(int $badgeId)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(TRUE);

        $this->setupRoute();

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var RpgRewards $rewardsAwarded */
        $rewardsAwarded = $this->getDoctrine()
            ->getRepository(RpgRewards::class)
            ->findOneBy(['id' => $badgeId]);
        if ($rewardsAwarded) {
            /** @var RpgRewardsAwarded[] $countAwards */
            $countAwards = $this->getDoctrine()
                ->getRepository(RpgRewardsAwarded::class)
                ->findBy(['reward' => $rewardsAwarded, 'patient' => $this->patient]);

            $return = [
                "id" => $rewardsAwarded->getId(),
                "name" => $rewardsAwarded->getName(),
                "image" => $rewardsAwarded->getImageUrl(),
                "text" => $rewardsAwarded->getText(),
                "textLong" => $rewardsAwarded->getTextLong(),
                "xp" => $rewardsAwarded->getXp(),
                "monday" => "",
                "sunday" => "",
                "awards" => [],
            ];

            if (date("w") == 0) { // Sunday
                $startDateRange = date("Y-m-d", strtotime('last monday'));
                $endDateRange = date("Y-m-d");
            } else if (date("w") == 1) { // Monday
                $startDateRange = date("Y-m-d");
                $endDateRange = date("Y-m-d", strtotime('next sunday'));
            } else {
                $startDateRange = date("Y-m-d", strtotime('last monday'));
                $endDateRange = date("Y-m-d", strtotime('next sunday'));
            }

            $return['monday'] = $startDateRange;
            $return['sunday'] = $endDateRange;
            try {
                $awardWeekReport = [];

                /** @var DateTime[] $periods */
                $periods = new DatePeriod(
                    new DateTime($startDateRange),
                    new DateInterval('P1D'),
                    new DateTime($endDateRange)
                );
                foreach ($periods as $period) {
                    if (strtotime($period->format("Y-m-d")) > date("U")) {
                        $awardWeekReport[$period->format("Y-m-d")] = 0;
                    } else if ($period->format("Y-m-d") == date("Y-m-d")) {
                        $awardWeekReport[$period->format("Y-m-d")] = 2;
                    } else {
                        $awardWeekReport[$period->format("Y-m-d")] = 1;
                    }
                }

                foreach ($countAwards as $countAward) {
                    if (array_key_exists($countAward->getDatetime()->format("Y-m-d"), $awardWeekReport)) {
                        $awardWeekReport[$countAward->getDatetime()->format("Y-m-d")] = 3;
                    }
                }

                foreach ($awardWeekReport as $keyDate => $value) {
                    $return['awards'][] = [
                        "date" => date("D", strtotime($keyDate)),
                        "awarded" => $value,
                    ];
                }
            } catch (Exception $e) {
            }
        }

        $b = microtime(TRUE);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/feed/achievements/awards", name="index_achievements_badges")
     */
    public function index_achievements_badges()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(TRUE);

        $this->setupRoute();

        $return['level'] = $this->patient->getRpgLevel();
        $return['factor'] = $this->patient->getRpgFactor();
        $return['xp'] = round($this->patient->getXpTotal(), 0);
        $return['xp_next'] = ceil($return['xp'] / 100) * 100;
        $return['level_next'] = $return['xp_next'] - $return['xp'];
        $return['level_percentage'] = 100 - ($return['xp_next'] - $return['xp']);
        $return['xp_log'] = [];
        foreach ($this->patient->getXp() as $rpgXP) {
            $return['xp_log'][] = [
                "datetime" => str_replace(" 00:00:00", "", $rpgXP->getDatetime()->format("Y-m-d H:i:s")),
                "log" => $rpgXP->getReason(),
                "value" => $rpgXP->getValue(),
            ];
        }
        $return['xp_log'] = array_slice(array_reverse($return['xp_log']), 0, 10);
        $return['awards'] = $this->getPatientAwards();

        $b = microtime(TRUE);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/feed/body/weight/{readings}", name="angular_body_weight")
     * @param int $readings A users UUID
     *
     * @return JsonResponse
     */
    public function index_body_weight(int $readings)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(TRUE);

        $this->setupRoute();

        $return['status'] = "okay";
        $return['code'] = "200";
        $return['weight'] = $this->getPatientWeight(FALSE, date("Y-m-d"), $readings);

        $b = microtime(TRUE);
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
        $a = microtime(TRUE);

        $return['latestVersion'] = $latestVersion;
        $return['yourVersion'] = $currentVersion;

        if (ip2long($latestVersion) > ip2long($currentVersion)) {
            $return['updateAvailable'] = TRUE;
        } else {
            $return['updateAvailable'] = FALSE;
        }

        $b = microtime(TRUE);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }
}
