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


use App\Entity\ApiAccessLog;
use App\Entity\RpgChallengeFriends;
use App\Entity\RpgChallengeGlobal;
use App\Entity\RpgChallengeGlobalPatient;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @noinspection PhpUnused */

class PveRoute extends CommonFeeds
{
    /**
     * @Route("/feed/pve/challenges/all", name="index_global_challenge")
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index_global_challenge()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $return['status'] = "okay";
        $return['code'] = "200";

        /** @var RpgChallengeGlobal[] $dbRpgChallengeGlobals */
        $dbRpgChallengeGlobals = $this->getDoctrine()
            ->getRepository(RpgChallengeGlobal::class)
            ->findBy(['childOf' => null, 'active' => true], ['id' => 'asc']);

        if ($dbRpgChallengeGlobals) {
            $return['challenges'] = [];
            foreach ($dbRpgChallengeGlobals as $dbRpgChallengeGlobal) {
                $arrayIndex = count($return['challenges']);

                $return['challenges'][$arrayIndex] = $this->buildChallengeArray($dbRpgChallengeGlobal);
                if (is_null($return['challenges'][$arrayIndex])) {
                    unset($return['challenges'][$arrayIndex]);
                }
            }
        }

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/feed/pve/challenges", name="index_global_challenge_in")
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index_global_challenge_in()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        /** @var RpgChallengeGlobalPatient[] $dbRpgChallengeGlobalIn */
        $dbRpgChallengeGlobalIn = $this->getDoctrine()
            ->getRepository(RpgChallengeGlobalPatient::class)
            ->findBy(['patient' => $this->patient], ['startDateTime' => 'asc', 'challenge' => 'asc']);

        if ($dbRpgChallengeGlobalIn) {
            $return['challenges'] = [];
            foreach ($dbRpgChallengeGlobalIn as $dbRpgChallengeGlobal) {
                $this->feed_storage = 0;
                if (is_null($dbRpgChallengeGlobal->getChallenge()->getChildOf())) {
                    $arrayIndex = count($return['challenges']);
                    $return['challenges'][$arrayIndex] = $this->buildChallengeArray($dbRpgChallengeGlobal->getChallenge(),
                        $dbRpgChallengeGlobal);
                    if (is_null($return['challenges'][$arrayIndex])) {
                        unset($return['challenges'][$arrayIndex]);
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
     * @Route("/forms/challenges/new", name="index_pvp_challenge_options")
     * @throws Exception
     */
    public function index_pvp_challenge_options()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $return['status'] = "okay";
        $return['code'] = "200";

        $return['friends'] = $this->getPatientFriends(true);
        $return['criteria'] = [
            "steps",
        ];
        $return['targets'] = [
            10000,
            30000,
            50000,
            70000,
            100000,
        ];
        $return['durations'] = [
            1,
            2,
            3,
            5,
            7,
            10,
            14,
            31,
        ];

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/feed/pvp/challenges", name="ux_aggregator_index_pvp_challenges")
     */
    public function index_pvp_challenges()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $return['status'] = "okay";
        $return['code'] = 200;

        $return['rpg_challenge_friends'] = $this->getPatientChallengesFriends();

        $b = microtime(true);
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
        $a = microtime(true);

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
            throw $this->createAccessDeniedException("You weren't part of this challenge");
        }

        if ($dbChallenger->getChallenger()->getId() == $this->getUser()->getId()) {
            $wasChallenger = true;
            $opponent = $dbChallenger->getChallenged();
        } else {
            $wasChallenger = false;
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

        if ($outcome == "incomplete") {
            $return['timeLeft'] = $dbChallenger->getDuration() - $return['timeElapsed'];
        } else {
            $return['timeLeft'] = 0;
        }

        foreach ($dbChallenger->getChallengerDetails() as $key => $value) {
            $return['widget']['labels'][] = date("D", strtotime($key));
            $return['challenge']['labels'][] = date("D", strtotime($key));
        }

        $userSum = 0;
        $opponentSum = 0;

        $pacesetterDaily = round($dbChallenger->getTarget() / $dbChallenger->getDuration(), 0, PHP_ROUND_HALF_UP);
        $pacesetterHourly = $pacesetterDaily / 24;
        $return['pacesetterDetail']['raw'] = round(($runForInSeconds / 60 / 60) * $pacesetterHourly, 0,
            PHP_ROUND_HALF_UP);
        $return['pacesetterDetail']['sum'] = number_format($return['pacesetterDetail']['raw'], 0);

        if ($wasChallenger) {
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

            $return['userDetail']['progress'] = 100 - round(($dbChallenger->getChallengerSum() / $dbChallenger->getTarget()) * 100,
                    0);
            $return['opponentDetail']['progress'] = 100 - round(($dbChallenger->getChallengedSum() / $dbChallenger->getTarget()) * 100,
                    0);

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

            $return['userDetail']['progress'] = 100 - round(($dbChallenger->getChallengedSum() / $dbChallenger->getTarget()) * 100,
                    0);
            $return['opponentDetail']['progress'] = 100 - round(($dbChallenger->getChallengerSum() / $dbChallenger->getTarget()) * 100,
                    0);

            foreach ($dbChallenger->getChallengerDetails() as $key => $value) {
                $return['widget']['data'][0]['data'][] = round($dbChallenger->getTarget() / $dbChallenger->getDuration(),
                    0, PHP_ROUND_HALF_UP);
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

        if ($return['userDetail']['progress'] > 100) {
            $return['userDetail']['progress'] = 100;
        }
        if ($return['userDetail']['progress'] < 0) {
            $return['userDetail']['progress'] = 0;
        }

        if ($return['opponentDetail']['progress'] > 100) {
            $return['opponentDetail']['progress'] = 100;
        }
        if ($return['opponentDetail']['progress'] < 0) {
            $return['opponentDetail']['progress'] = 0;
        }

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

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/feed/pvp/leaderboard", name="ux_aggregator_index_pvp_leaderboard")
     * @throws Exception
     */
    public function index_pvp_leaderboard()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

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

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }
}
