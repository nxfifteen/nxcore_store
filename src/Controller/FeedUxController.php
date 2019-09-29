<?php

namespace App\Controller;

use App\Entity\ApiAccessLog;
use App\Entity\BodyWeight;
use App\Entity\FitDistanceDailySummary;
use App\Entity\FitFloorsIntraDay;
use App\Entity\FitStepsDailySummary;
use App\Entity\FitStepsIntraDay;
use App\Entity\Patient;
use App\Entity\RpgChallengeFriends;
use App\Entity\RpgMilestones;
use App\Service\AwardManager;
use Sentry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FeedUxController extends AbstractController
{
    /** @var Patient $patient */
    private $patient;
    /**
     * @var int
     */
    private $challengeWins = 0;
    /**
     * @var int
     */
    private $challengeLoses = 0;
    /**
     * @var int
     */
    private $challengeDraws = 0;

    /**
     * @Route("/feed/dashboard", name="ux_aggregator")
     *
     * @param AwardManager $awardManager
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(AwardManager $awardManager)
    {
        $return = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
            ]);
        });

        $return['status'] = "okay";
        $return['code'] = "200";

        $return['steps'] = $this->getPatientSteps();
        $return['floors'] = $this->getPatientFloors();
        $return['distance'] = $this->getPatientDistance();
        $return['milestones'] = $this->getPatientMilestones();
        $return['rpg_friends'] = $this->getPatientFriends();
        $return['rpg_challenge_friends'] = $this->getPatientChallengesFriends(TRUE);
        $return['awards'] = $this->getPatientAwards();
        $return['weight'] = $this->getPatientWeight();

        $awardManager->giveXp($this->patient, 5, "First login for " . date("l jS F, Y"), new \DateTime(date("Y-m-d 00:00:00")));

        return $this->json($return);
    }

    /**
     * @return array|null
     */
    private function getPatientSteps()
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
        $returnSummary['intraDay'] = $this->getPatientStepsIntraDay(date("Y-m-d"));

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
     * @return array|null
     */
    private function getPatientStepsIntraDay(String $date)
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
        $returnSummary['intraDay'] = $this->getPatientDistanceIntraDay(date("Y-m-d"));

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
     * @return null
     */
    private function getPatientDistanceIntraDay(String $date)
    {
        return NULL;
    }

    /**
     * @return null
     */
    private function getPatientMilestones()
    {
        $returnSummary = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        $return['distance'] = [];
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var float $distance */
        $distance = $this->getDoctrine()
            ->getRepository(FitDistanceDailySummary::class)
            ->getSumOfValues($this->patient->getUuid());
        if (is_numeric($distance)) {
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
            "name" => $this->patient->getFirstName() . " " . $this->patient->getSurName(),
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
            ->findBy(["challenger" => $this->patient], ["startDate" => "DESC"]);
        foreach ($dbChallenger as $challengeFriends) {
            $runningFriendsChallenges = $this->populatePatientChallengesFriends($runningFriendsChallenges, $challengeFriends);
        }

        /** @var RpgChallengeFriends[] $dbChallenger */
        $dbChallenged = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findBy(["challenged" => $this->patient], ["startDate" => "DESC"]);
        foreach ($dbChallenged as $challengeFriends) {
            $runningFriendsChallenges = $this->populatePatientChallengesFriends($runningFriendsChallenges, $challengeFriends);
        }

        $runningFriendsChallenges['score'] = [
            "win" => $this->challengeWins,
            "lose" => $this->challengeLoses,
            "draw" => $this->challengeDraws,
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
                        $this->challengeDraws++;
                        break;
                    case 5:
                        if ($wasChallenger) {
                            $outcome = "win";
                            $this->challengeWins++;
                        } else {
                            $outcome = "lose";
                            $this->challengeLoses++;
                        }
                        break;
                    case 4:
                        if ($wasChallenger) {
                            $outcome = "lose";
                            $this->challengeLoses++;
                        } else {
                            $outcome = "win";
                            $this->challengeWins++;
                        }
                        break;
                    case 3:
                        $outcome = "uncompleted";
                        $this->challengeLoses++;
                        break;
                    case 2:
                        $outcome = "uncompleted";
                        $this->challengeLoses++;
                        break;
                    case 1:
                        $outcome = "uncompleted";
                        $this->challengeLoses++;
                        break;
                    default:
                        $outcome = "unknown";
                        break;
                }

                if (array_key_exists("completed", $runningFriendsChallenges)) {
                    $index = count($runningFriendsChallenges['completed']);
                    $runningFriendsChallenges['completed'][$index] = [
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
                    "name" => $reward->getReward()->getName(),
                    "awarded" => $reward->getDatetime()->format("Y-m-d"),
                    "image" => $reward->getReward()->getImage(),
                    "text" => $reward->getReward()->getText(),
                    "longtext" => $reward->getReward()->getTextLong(),
                    "count" => 1,
                ];
            }
        }

        return $returnSummary;
    }

    /**
     * @return array
     */
    private function getPatientWeight()
    {
        $returnSummary = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BodyWeight[] $product */
        $product = $this->getDoctrine()
            ->getRepository(BodyWeight::class)
            ->findByDateRangeHistorical($this->patient->getUuid(), date("Y-m-d"), 31);
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
        $returnSummary['widget'] = [];
        $returnSummary['widget']['labels'] = [];
        $returnSummary['widget']['data'] = [];
        $returnSummary['widget']['axis']['min'] = 0;
        $returnSummary['widget']['axis']['max'] = 0;

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
                    $returnSummary['widget']['data'][1]['data'][] = round($item->getMeasurement(), 2);
                    if (count($returnSummary['widget']['data'][1]['data']) == 1) {
                        $returnSummary['widget']['data'][2]['label'] = "Average " . $item->getUnitOfMeasurement()->getName();
                        $returnSummary['widget']['data'][2]['data'][] = round($item->getMeasurement(), 2);
                    } else {
                        $returnSummary['widget']['data'][2]['label'] = "Average " . $item->getUnitOfMeasurement()->getName();
                        $countWeight = $returnSummary['widget']['data'][1]['data'];
                        $sumWeight = array_sum($returnSummary['widget']['data'][1]['data']);
                        $returnSummary['widget']['data'][2]['data'][] = round($sumWeight / count($countWeight), 2);
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
        }

        $firstMeasurement = round($productFirst[0]->getMeasurement(), 2);
        $currentMeasurement = round($product[(count($product) - 1)]->getMeasurement(), 2);
        $targetMeasurement = round($product[(count($product) - 1)]->getPatientGoal()->getGoal(), 2);
        $totalToReach = round($firstMeasurement - $targetMeasurement, 2);
        $totalProgress = round($firstMeasurement - $currentMeasurement, 2);
        $progressPercentage = round(($totalProgress / $totalToReach) * 100, 2);

        $returnSummary['goal'] = $targetMeasurement;
        $returnSummary['progress'] = $progressPercentage;
        $returnSummary['widget']['axis']['min'] = $returnSummary['widget']['axis']['min'] - 1;
        $returnSummary['widget']['axis']['max'] = $returnSummary['widget']['axis']['max'] + 1;

        return $returnSummary;
    }

    /**
     * @Route("/feed/pvp/challenges", name="ux_aggregator_index_pvp_challenges")
     */
    public function index_pvp_challenges()
    {
        $return = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
            ]);
        });

        $return['status'] = "okay";
        $return['code'] = "200";

        $return['rpg_challenge_friends'] = $this->getPatientChallengesFriends();

        return $this->json($return);
    }

    /**
     * @Route("/forms/challenges/new", name="index_pvp_challenge_options")
     */
    public function index_pvp_challenge_options()
    {
        $return = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
            ]);
        });

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

        return $this->json($return);
    }

    /**
     * @Route("/feed/pvp/leaderboard", name="ux_aggregator_index_pvp_leaderboard")
     */
    public function index_pvp_leaderboard()
    {
        $return = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
            ]);
        });

        $return['status'] = "okay";
        $return['code'] = "200";

        $return['maxSteps'] = 0;
        $return['friends'] = $this->getPatientFriends();
        foreach ($return['friends'] as $friend) {
            if ($friend['steps'] > $return['maxSteps']) {
                $return['maxSteps'] = $friend['steps'];
            }
        }

        return $this->json($return);
    }


    /**
     * @Route("/feed/achievements/awards", name="index_achievements_badges")
     */
    public function index_achievements_badges()
    {
        $return = [];

        if (is_null($this->patient)) $this->patient = $this->getUser();

        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
            ]);
        });

        $return['awards'] = $this->getPatientAwards();
        $return['level'] = $this->patient->getRpgLevel();
        $return['xp'] = round($this->patient->getXpTotal(), 0);
        $return['xp_log'] = [];
        foreach ($this->patient->getXp() as $rpgXP) {
            $return['xp_log'][] = [
                "datetime" => str_replace(" 00:00:00", "", $rpgXP->getDatetime()->format("Y-m-d H:i:s")),
                "log" => $rpgXP->getReason(),
                "value" => $rpgXP->getValue(),
            ];
        }
        $return['xp_log'] = array_reverse($return['xp_log']);
        $return['factor'] = $this->patient->getRpgFactor();

        return $this->json($return);
    }
}
