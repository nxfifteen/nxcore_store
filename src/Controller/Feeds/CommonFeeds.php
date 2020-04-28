<?php /**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nxcore/
 * @link      https://gitlab.com/nx-core/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */ /** @noinspection DuplicatedCode */

namespace App\Controller\Feeds;


use App\Controller\Common;
use App\Entity\ApiAccessLog;
use App\Entity\BodyWeight;
use App\Entity\FitDistanceDailySummary;
use App\Entity\FitFloorsIntraDay;
use App\Entity\FitStepsDailySummary;
use App\Entity\FitStepsIntraDay;
use App\Entity\RpgChallengeFriends;
use App\Entity\RpgChallengeGlobal;
use App\Entity\RpgChallengeGlobalPatient;
use App\Entity\RpgMilestones;
use Exception;

class CommonFeeds extends Common
{
    private function calcChallengeDraws()
    {
        /** @var int $dbChallenger */
        $dbChallenger = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findChallengeDraws($this->patient);

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

    private function calcChallengeWins()
    {
        /** @var int $dbChallenger */
        $dbChallenger = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findChallengeWins($this->patient);

        return $dbChallenger;
    }

    private function findChallengeChildren(
        RpgChallengeGlobal $dbRpgChallengeGlobal,
        RpgChallengeGlobalPatient $challengePatientDetails = null
    ) {
        $return = [
            "id" => $dbRpgChallengeGlobal->getId(),
            "name" => $dbRpgChallengeGlobal->getName(),
            "description" => $dbRpgChallengeGlobal->getDescripton(),
            "progression" => $dbRpgChallengeGlobal->getProgression(),
            "criteria" => null,
            "target" => $dbRpgChallengeGlobal->getTarget(),
            "targetHuman" => number_format($dbRpgChallengeGlobal->getTarget(), 0),
            "reward" => null,
            "depth" => 0,
            "isCollapsed" => false,
        ];

        $return = $this->participationInChallenge($return, $challengePatientDetails, $dbRpgChallengeGlobal);

        if (!is_null($dbRpgChallengeGlobal->getReward())) {
            $return['reward'] = [];
            $return['reward']["badge"] = $dbRpgChallengeGlobal->getReward()->getName();
            $return['reward']["xp"] = null;
        } else {
            if (!is_null($dbRpgChallengeGlobal->getXp())) {
                $return['reward'] = [
                    "xp" => null,
                    "badge" => null,
                ];
            }
        }

        if (!is_null($dbRpgChallengeGlobal->getUnitOfMeasurement())) {
            $return['criteria'] = $dbRpgChallengeGlobal->getUnitOfMeasurement()->getName() . "(s)";
        } else {
            if (!is_null($dbRpgChallengeGlobal->getCriteria())) {
                $return['criteria'] = strtolower(str_replace("Fit", "",
                    str_replace("DailySummary", "", $dbRpgChallengeGlobal->getCriteria())));
            }
        }

        /** @var RpgChallengeGlobal[] $dbRpgChallengeGlobals */
        $dbRpgChallengeGlobalChildren = $this->getDoctrine()
            ->getRepository(RpgChallengeGlobal::class)
            ->findBy(['childOf' => $dbRpgChallengeGlobal, 'active' => true], ['target' => 'asc', 'id' => 'asc']);

        if ($dbRpgChallengeGlobalChildren) {
            $return['children'] = [];

            /** @var RpgChallengeGlobal $dbRpgChallengeGlobalChild */
            foreach ($dbRpgChallengeGlobalChildren as $dbRpgChallengeGlobalChild) {
                $return['children'][] = $this->buildChallengeArray($dbRpgChallengeGlobalChild,
                    $challengePatientDetails);
            }
            $return['depth'] = $return['children'][0]['depth'] + 1;
        } else {
            return null;
        }

        if ($return['depth'] > 0) {
            $return['isCollapsed'] = true;
        }

        return $return;
    }

    /**
     * @param int|NULL $currentHour
     *
     * @return array
     */
    private function getHoursArray(int $currentHour = null)
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
            return null;
        }
        $timeStampsInTrack['widget']['labels'] = array_keys($dbHours);
        $timeStampsInTrack['widget']['data']['data'] = $dbHours;

        return $timeStampsInTrack;
    }

    /**
     * @param String $date
     *
     * @param bool   $pro
     *
     * @return array|null
     */
    private function getPatientStepsIntraDay(string $date, $pro = false)
    {
        if (is_null($this->patient)) {
            $this->patient = $this->getUser();
        }

        /** @var FitStepsIntraDay[] $dbStepsSummary */
        $dbStepsIntraDay = $this->getDoctrine()
            ->getRepository(FitStepsIntraDay::class)
            ->findByDateRange($this->patient->getUuid(), $date);
        if (count($dbStepsIntraDay) == 0) {
            return null;
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
                $hourIndex = intval($item->getDateTime()->format("G"));
                if (is_numeric($item->getValue())) {
                    $dbHours[$hourIndex] = $dbHours[$hourIndex] + $item->getValue();
                }
            }
        } else {
            return null;
        }

        if (!$pro) {
            $timeStampsInTrack['widget']['labels'] = array_keys($dbHours);
            $timeStampsInTrack['widget']['data']['data'] = $dbHours;
        } else {
            $timeStampsInTrack['widget'] = [];
            foreach ($dbHours as $dbHour => $value) {
                if ($value == 0) {
                    $value = 50;
                }
                $timeStampsInTrack['widget'][] = [
                    "name" => $dbHour,
                    "value" => $value,
                ];
            }
        }

        return $timeStampsInTrack;
    }

    private function participationInChallenge(
        array $return,
        RpgChallengeGlobalPatient $challengePatientDetails,
        RpgChallengeGlobal $dbRpgChallengeGlobal
    ) {
        if (!is_null($challengePatientDetails)) {
            if ($challengePatientDetails->getChallenge()->getId() != $dbRpgChallengeGlobal->getId()) {
                /** @var RpgChallengeGlobalPatient $challengePatientDetails */
                $challengePatientDetails = $this->getDoctrine()
                    ->getRepository(RpgChallengeGlobalPatient::class)
                    ->findOneBy(['patient' => $this->patient, 'challenge' => $dbRpgChallengeGlobal->getId()]);
            }

            if ($challengePatientDetails) {
                $return['participation'] = [];
                $return['participation']['progress'] = round($challengePatientDetails->getProgress(), 0,
                    PHP_ROUND_HALF_DOWN);
                if (!is_null($challengePatientDetails->getStartDateTime())) {
                    $return['participation']['startDateTime'] = $challengePatientDetails->getStartDateTime()->format("Y-m-d H:i:s");
                } else {
                    $return['participation']['startDateTime'] = null;
                }
                if (!is_null($challengePatientDetails->getFinishDateTime())) {
                    $return['participation']['finishDateTime'] = $challengePatientDetails->getFinishDateTime()->format("Y-m-d H:i:s");
                } else {
                    $return['participation']['finishDateTime'] = null;
                }
            } else {
                $return['participation'] = -1;
            }
        }

        return $return;
    }

    /**
     * @param array               $runningFriendsChallenges
     * @param RpgChallengeFriends $challengeFriends
     *
     * @return array
     */
    private function populatePatientChallengesFriends(
        array $runningFriendsChallenges,
        RpgChallengeFriends $challengeFriends
    ) {
        if ($challengeFriends->getChallenger()->getId() == $this->getUser()->getId()) {
            $wasChallenger = true;
            $opponent = $challengeFriends->getChallenged();
        } else {
            $wasChallenger = false;
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
        } else {
            if (is_null($challengeFriends->getStartDate()) && $challengeFriends->getChallenger()->getId() != $this->getUser()->getId()) {
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
                            $runningFriendsChallenges['completed'][$index]['userDetail']['completion'] = round(($challengeFriends->getChallengerSum() / $challengeFriends->getTarget()) * 100,
                                0);
                            if ($runningFriendsChallenges['completed'][$index]['userDetail']['completion'] > 100) {
                                $runningFriendsChallenges['completed'][$index]['userDetail']['completion'] = 100;
                            }
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
                            $runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] = round(($challengeFriends->getChallengedSum() / $challengeFriends->getTarget()) * 100,
                                0);
                            if ($runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] > 100) {
                                $runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] = 100;
                            }
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
                            $runningFriendsChallenges['completed'][$index]['userDetail']['completion'] = round(($challengeFriends->getChallengedSum() / $challengeFriends->getTarget()) * 100,
                                0);
                            if ($runningFriendsChallenges['completed'][$index]['userDetail']['completion'] > 100) {
                                $runningFriendsChallenges['completed'][$index]['userDetail']['completion'] = 100;
                            }
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
                            $runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] = round(($challengeFriends->getChallengerSum() / $challengeFriends->getTarget()) * 100,
                                0);
                            if ($runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] > 100) {
                                $runningFriendsChallenges['completed'][$index]['opponentDetail']['completion'] = 100;
                            }
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

                } else {
                    if (!is_null($challengeFriends->getEndDate())) {
                        /*
                         * Challenges that are currently running
                         */
                        $durationInSeconds = $challengeFriends->getEndDate()->format("U") - $challengeFriends->getStartDate()->format("U");
                        $runForInSeconds = date("U") - $challengeFriends->getStartDate()->format("U");
                        $runForInPercentage = ($runForInSeconds / $durationInSeconds) * 100;
                        if ($runForInPercentage > 100) {
                            $runForInPercentage = 100;
                        }

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
                            $runningFriendsChallenges['running'][$index]['userDetail']['completion'] = round(($challengeFriends->getChallengerSum() / $challengeFriends->getTarget()) * 100,
                                0);
                            if ($runningFriendsChallenges['running'][$index]['userDetail']['completion'] > 100) {
                                $runningFriendsChallenges['running'][$index]['userDetail']['completion'] = 100;
                            }
                            if ($challengeFriends->getChallengerSum() > $challengeFriends->getChallengedSum()) {
                                $runningFriendsChallenges['running'][$index]['userDetail']['outcomeType'] = "success";
                            } else {
                                $runningFriendsChallenges['running'][$index]['userDetail']['outcomeType'] = "warning";
                            }
                            $runningFriendsChallenges['running'][$index]['userDetail']['detail'] = $challengeFriends->getChallengerDetails();

                            $runningFriendsChallenges['running'][$index]['opponentDetail'] = [];
                            $runningFriendsChallenges['running'][$index]['opponentDetail']['sum'] = $challengeFriends->getChallengedSum();
                            $runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] = round(($challengeFriends->getChallengedSum() / $challengeFriends->getTarget()) * 100,
                                0);
                            if ($runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] > 100) {
                                $runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] = 100;
                            }
                            if ($challengeFriends->getChallengerSum() < $challengeFriends->getChallengedSum()) {
                                $runningFriendsChallenges['running'][$index]['opponentDetail']['outcomeType'] = "success";
                            } else {
                                $runningFriendsChallenges['running'][$index]['opponentDetail']['outcomeType'] = "info";
                            }
                            $runningFriendsChallenges['running'][$index]['opponentDetail']['detail'] = $challengeFriends->getChallengedDetails();
                        } else {
                            $runningFriendsChallenges['running'][$index]['userDetail'] = [];
                            $runningFriendsChallenges['running'][$index]['userDetail']['sum'] = $challengeFriends->getChallengedSum();
                            $runningFriendsChallenges['running'][$index]['userDetail']['completion'] = round(($challengeFriends->getChallengedSum() / $challengeFriends->getTarget()) * 100,
                                0);
                            if ($runningFriendsChallenges['running'][$index]['userDetail']['completion'] > 100) {
                                $runningFriendsChallenges['running'][$index]['userDetail']['completion'] = 100;
                            }
                            if ($challengeFriends->getChallengedSum() > $challengeFriends->getChallengerSum()) {
                                $runningFriendsChallenges['running'][$index]['userDetail']['outcomeType'] = "success";
                            } else {
                                $runningFriendsChallenges['running'][$index]['userDetail']['outcomeType'] = "warning";
                            }
                            $runningFriendsChallenges['running'][$index]['userDetail']['detail'] = $challengeFriends->getChallengedDetails();

                            $runningFriendsChallenges['running'][$index]['opponentDetail'] = [];
                            $runningFriendsChallenges['running'][$index]['opponentDetail']['sum'] = $challengeFriends->getChallengerSum();
                            $runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] = round(($challengeFriends->getChallengerSum() / $challengeFriends->getTarget()) * 100,
                                0);
                            if ($runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] > 100) {
                                $runningFriendsChallenges['running'][$index]['opponentDetail']['completion'] = 100;
                            }
                            if ($challengeFriends->getChallengedSum() < $challengeFriends->getChallengerSum()) {
                                $runningFriendsChallenges['running'][$index]['opponentDetail']['outcomeType'] = "success";
                            } else {
                                $runningFriendsChallenges['running'][$index]['opponentDetail']['outcomeType'] = "info";
                            }
                            $runningFriendsChallenges['running'][$index]['opponentDetail']['detail'] = $challengeFriends->getChallengerDetails();
                        }

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
            }
        }

        return $runningFriendsChallenges;
    }

    protected function buildChallengeArray(
        RpgChallengeGlobal $dbRpgChallengeGlobal,
        RpgChallengeGlobalPatient $challengePatientDetails = null
    ) {
        if (!is_null($dbRpgChallengeGlobal->getProgression())) {
            $return = $this->findChallengeChildren($dbRpgChallengeGlobal, $challengePatientDetails);
            if (is_null($return)) {
                return null;
            }

            $xp = 0;
            if (!is_null($dbRpgChallengeGlobal->getReward())) {
                $xp = 0;
            } else {
                if (!is_null($dbRpgChallengeGlobal->getXp())) {
                    $xp = $dbRpgChallengeGlobal->getXp();
                }
            }

            if (is_array($return) && array_key_exists("children", $return)) {
                foreach ($return['children'] as $parent) {
                    if (is_array($parent) && array_key_exists("children", $parent)) {
                        foreach ($parent['children'] as $child) {
                            $xp = $xp + $child['reward']['xp'];
                        }
                    } else {
                        $xp = $xp + $parent['reward']['xp'];
                    }
                }
            }

            $return['reward']['xp'] = $xp;
        } else {
            $return = [
                "id" => $dbRpgChallengeGlobal->getId(),
                "name" => $dbRpgChallengeGlobal->getName(),
                "description" => $dbRpgChallengeGlobal->getDescripton(),
                "criteria" => null,
                "target" => $dbRpgChallengeGlobal->getTarget(),
                "targetHuman" => number_format($dbRpgChallengeGlobal->getTarget(), 0),
                "reward" => null,
                "depth" => 0,
                "isCollapsed" => false,
            ];

            if (!is_null($dbRpgChallengeGlobal->getUnitOfMeasurement())) {
                $return['criteria'] = $dbRpgChallengeGlobal->getUnitOfMeasurement()->getName() . "(s)";
            } else {
                if (!is_null($dbRpgChallengeGlobal->getCriteria())) {
                    $return['criteria'] = strtolower(str_replace("Fit", "",
                        str_replace("DailySummary", "", $dbRpgChallengeGlobal->getCriteria())));
                }
            }

            $return = $this->participationInChallenge($return, $challengePatientDetails, $dbRpgChallengeGlobal);

            if (!is_null($dbRpgChallengeGlobal->getReward())) {
                $return['reward'] = [
                    "xp" => 0,
                    "badge" => $dbRpgChallengeGlobal->getReward()->getName(),
                ];
            } else {
                if (!is_null($dbRpgChallengeGlobal->getXp())) {
                    $return['reward'] = [
                        "xp" => $dbRpgChallengeGlobal->getXp(),
                        "badge" => null,
                    ];
                }
            }
        }

        return $return;
    }

    /**
     * @param string $getCriteria
     *
     * @return string
     */
    protected function friendlyNameChallengeCriteria(string $getCriteria)
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
    protected function getPatientAwards()
    {
        $returnSummary = [];

        if (is_null($this->patient)) {
            $this->patient = $this->getUser();
        }

        foreach ($this->patient->getRewards() as $reward) {
            if (array_key_exists($reward->getReward()->getId(), $returnSummary)) {
                $returnSummary[$reward->getReward()->getId()]['count']++;
                if (strtotime($returnSummary[$reward->getReward()->getId()]['awarded']) < $reward->getDatetime()->format("U")) {
                    $returnSummary[$reward->getReward()->getId()]['awarded'] = $reward->getDatetime()->format("Y-m-d");
                }
            } else {
                $payloadArray = json_decode($reward->getReward()->getPayload());
                $returnSummary[$reward->getReward()->getId()] = [
                    "id" => $reward->getReward()->getId(),
                    "name" => $payloadArray->name,
                    "awarded" => $reward->getDatetime()->format("Y-m-d"),
                    "image" => $payloadArray->badge_image,
                    "text" => $payloadArray->badge_text,
                    "longtext" => $payloadArray->badge_longtext,
                    "count" => 1,
                ];
            }
        }

        return $returnSummary;
    }

    /**
     * @param bool $summaryOnly
     *
     * @return array
     */
    protected function getPatientChallengesFriends($summaryOnly = false)
    {
        $runningFriendsChallenges = [];
        $runningFriendsChallenges['score'] = [];
        $runningFriendsChallenges['running'] = [];
        if (!$summaryOnly) {
            $runningFriendsChallenges['toAccept'] = [];
        }
        if (!$summaryOnly) {
            $runningFriendsChallenges['pending'] = [];
        }
        if (!$summaryOnly) {
            $runningFriendsChallenges['completed'] = [];
        }

        /** @var RpgChallengeFriends[] $dbChallenger */
        /** @var RpgChallengeFriends $challengeFriends */

        $dbChallenger = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findBy(["challenger" => $this->patient], ["endDate" => "DESC"]);
        foreach ($dbChallenger as $challengeFriends) {
            $runningFriendsChallenges = $this->populatePatientChallengesFriends($runningFriendsChallenges,
                $challengeFriends);
        }

        $dbChallenged = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findBy(["challenged" => $this->patient], ["endDate" => "DESC"]);
        foreach ($dbChallenged as $challengeFriends) {
            $runningFriendsChallenges = $this->populatePatientChallengesFriends($runningFriendsChallenges,
                $challengeFriends);
        }

        $runningFriendsChallenges['score'] = [
            "win" => $this->calcChallengeWins(),
            "lose" => $this->calcChallengeLoses(),
            "draw" => $this->calcChallengeDraws(),
        ];

        return $runningFriendsChallenges;
    }

    /**
     * @return array|null
     * @throws Exception
     */
    protected function getPatientDistance()
    {
        $returnSummary = [];

        if (is_null($this->patient)) {
            $this->patient = $this->getUser();
        }

        /** @var FitDistanceDailySummary[] $dbDistanceSummary */
        $dbDistanceSummary = $this->getDoctrine()
            ->getRepository(FitDistanceDailySummary::class)
            ->findByDateRangeHistorical($this->patient->getUuid(), date("Y-m-d"), 0);
        if (count($dbDistanceSummary) == 0) {
            return null;
        }

        $dbDistanceSummary = array_pop($dbDistanceSummary);

        $returnSummary['value'] = $dbDistanceSummary->getValue();
        $returnSummary['goal'] = $dbDistanceSummary->getGoal()->getGoal();
        if ($returnSummary['goal'] == 0) {
            $returnSummary['goal'] = 3000;
        }
        $returnSummary['progress'] = round(($returnSummary['value'] / $returnSummary['goal']) * 100, 0);
        if ($dbDistanceSummary->getUnitOfMeasurement()->getName()) {
            $returnSummary['value'] = round($returnSummary['value'] / 1000, 2);
            $returnSummary['goal'] = round($returnSummary['goal'] / 1000, 2);
            $returnSummary['units'] = "km";
        } else {
            $returnSummary['units'] = $dbDistanceSummary->getUnitOfMeasurement()->getName();
        }
        $returnSummary['intraDay'] = null;

        if ($returnSummary['progress'] > 100) {
            $returnSummary['progressBar'] = 100;
        } else {
            $returnSummary['progressBar'] = $returnSummary['progress'];
        }

        return $returnSummary;
    }

    /**
     * @return array|null
     */
    protected function getPatientFloors()
    {
        $returnSummary = [];

        if (is_null($this->patient)) {
            $this->patient = $this->getUser();
        }

        /** @var FitFloorsIntraDay[] $dbStepsSummary */
        $dbStepsSummary = $this->getDoctrine()
            ->getRepository(FitFloorsIntraDay::class)
            ->findByDateRange($this->patient->getUuid(), date("Y-m-d"));
        if (count($dbStepsSummary) == 0) {
            return null;
        }

        $returnSummary['value'] = 0;
        $returnSummary['goal'] = 10;
        if (count($dbStepsSummary) > 0) {
            foreach ($dbStepsSummary as $item) {
                if (is_numeric($item->getValue())) {
                    $returnSummary['value'] = $returnSummary['value'] + $item->getValue();
                }
            }
        } else {
            return null;
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
     * @param bool $summarise
     *
     * @return array
     * @throws Exception
     */
    protected function getPatientFriends(bool $summarise = false)
    {
        $returnSummary = [];

        if (is_null($this->patient)) {
            $this->patient = $this->getUser();
        }


        $returnSummary[0] = [
            "uuid" => $this->patient->getUuid(),
            "name" => $this->patient->getFirstName(),
            "avatar" => $this->patient->getAvatar(),
            "you" => "you",
        ];

        if (!$summarise) {
            $yourStepCount = 0;

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
                $returnSummary[$friendIndex]['steps_friendly'] = number_format($returnSummary[$friendIndex]['steps'],
                    0);
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
     * @return null
     */
    protected function getPatientMilestones()
    {
        $return = [];

        if (is_null($this->patient)) {
            $this->patient = $this->getUser();
        }

        $return['distance'] = [];
        /** @var float $distance */
        $distance = $this->getDoctrine()
            ->getRepository(FitDistanceDailySummary::class)
            ->getSumOfValues($this->patient->getUuid());
        if (!is_numeric($distance)) {
            return null;
        }
        $distance = ($distance / 1000);

        /** @var RpgMilestones[] $distanceMileStonesLess */
        $distanceMileStonesLess = $this->getDoctrine()
            ->getRepository(RpgMilestones::class)
            ->getLessThan('distance', $distance);
        foreach ($distanceMileStonesLess as $distanceMileStoneLess) {
            $return['distance']['less'][] = "**" . number_format($distanceMileStoneLess->getValue() - $distance,
                    2) . " km** till you've walked *" . $distanceMileStoneLess->getMsgLess() . "*";
        }

        /** @var RpgMilestones[] $distanceMileStonesMore */
        $distanceMileStonesMore = $this->getDoctrine()
            ->getRepository(RpgMilestones::class)
            ->getMoreThan('distance', $distance);

        foreach ($distanceMileStonesMore as $distanceMileStoneMore) {
            $times = number_format($distance / $distanceMileStoneMore->getValue(), 0);
            if ($times == 1) {
                $return['distance']['more'][] = "You've walked *" . $distanceMileStoneMore->getMsgLess() . "*";
            } else {
                if ($times == 2) {
                    $return['distance']['more'][] = "You've walked *" . $distanceMileStoneMore->getMsgLess() . "* and **back**!";
                } else {
                    $return['distance']['more'][] = "You've walked *" . $distanceMileStoneMore->getMsgLess() . "* **"
                        . $times . "** times.";
                }
            }
        }

        return $return;
    }

    /**
     * @param bool $pro
     *
     * @return array|null
     * @throws Exception
     */
    protected function getPatientSteps($pro = false)
    {
        $returnSummary = [];

        if (is_null($this->patient)) {
            $this->patient = $this->getUser();
        }

        /** @var FitStepsDailySummary[] $dbStepsSummary */
        $dbStepsSummary = $this->getDoctrine()
            ->getRepository(FitStepsDailySummary::class)
            ->findByDateRangeHistorical($this->patient->getUuid(), date("Y-m-d"), 0);
        if (count($dbStepsSummary) == 0) {
            return null;
        }
        $dbStepsSummary = array_pop($dbStepsSummary);

        $returnSummary['value'] = $dbStepsSummary->getValue();
        $returnSummary['goal'] = $dbStepsSummary->getGoal()->getGoal();
        if ($returnSummary['goal'] == 0) {
            $returnSummary['goal'] = 10000;
        }
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
     * @param bool        $pro
     * @param String|null $date
     * @param int         $dateRange
     *
     * @return array
     * @throws Exception
     */
    protected function getPatientWeight($pro = false, string $date = null, int $dateRange = 31)
    {
        $returnSummary = [];

        if (is_null($this->patient)) {
            $this->patient = $this->getUser();
        }

        if (is_null($date)) {
            $date = date("Y-m-d");
        }

        /** @var BodyWeight[] $product */
        $product = $this->getDoctrine()
            ->getRepository(BodyWeight::class)
            ->findByDateRangeHistorical($this->patient->getUuid(), $date, $dateRange);
        if (count($product) == 0) {
            return null;
        }

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
                foreach ($product as $item) {
                    if (is_numeric($item->getMeasurement())) {
                        $returnSummary['value'] = round($item->getMeasurement(), 2);
                        $returnSummary['unit'] = $item->getUnitOfMeasurement()->getName();

                        if (is_numeric($item->getPatientGoal()->getGoal())) {
                            $returnSummary['widget']['data'][0]['label'] = "Goal " . $item->getPatientGoal()->getUnitOfMeasurement()->getName();
                            $returnSummary['widget']['data'][0]['data'][] = round($item->getPatientGoal()->getGoal(),
                                2);
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

                            $buildArrayAverageLoss[] = round($returnSummary['widget']['data'][2]['data'][$newAvgIndex] - ($sumWeight / count($countWeight)),
                                2);
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
            if ($progressPercentage > 100) {
                $progressPercentage = 100;
            }

            $returnSummary['goal'] = $targetMeasurement;
            $returnSummary['progress'] = $progressPercentage;
            $returnSummary['widget']['axis']['min'] = $returnSummary['widget']['axis']['min'] - 1;
            $returnSummary['widget']['axis']['max'] = $returnSummary['widget']['axis']['max'] + 1;
        }

        return $returnSummary;
    }
}
