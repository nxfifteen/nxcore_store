<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nxcore/
 * @link      https://gitlab.com/nx-core/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

namespace App\Controller\Feeds;


use App\Entity\RpgRewards;
use App\Entity\RpgRewardsAwarded;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @noinspection PhpUnused */

class AchievementsRoute extends CommonFeeds
{

    /**
     * @Route("/feed/achievements/awards", name="index_achievements_badges")
     */
    public function index_achievements_badges()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

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

        $b = microtime(true);
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
        $a = microtime(true);

        $this->setupRoute();

        /** @var RpgRewards $rewardsAwarded */
        $rewardsAwarded = $this->getDoctrine()
            ->getRepository(RpgRewards::class)
            ->findOneBy(['id' => $badgeId]);
        if ($rewardsAwarded) {
            /** @var RpgRewardsAwarded[] $countAwards */
            $countAwards = $this->getDoctrine()
                ->getRepository(RpgRewardsAwarded::class)
                ->findBy(['reward' => $rewardsAwarded, 'patient' => $this->patient]);

            $payloadArray = json_decode($rewardsAwarded->getPayload());
            $return = [
                "id" => $rewardsAwarded->getId(),
                "name" => $payloadArray->name,
                "image" => $payloadArray->badge_image,
                "text" => $payloadArray->badge_text,
                "textLong" => $payloadArray->badge_longtext,
                "xp" => 0,
                "monday" => "",
                "sunday" => "",
                "awards" => [],
            ];

            if (date("w") == 0) { // Sunday
                $startDateRange = date("Y-m-d", strtotime('last monday'));
                $endDateRange = date("Y-m-d");
            } else {
                if (date("w") == 1) { // Monday
                    $startDateRange = date("Y-m-d");
                    $endDateRange = date("Y-m-d", strtotime('next sunday'));
                } else {
                    $startDateRange = date("Y-m-d", strtotime('last monday'));
                    $endDateRange = date("Y-m-d", strtotime('next sunday'));
                }
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
                    } else {
                        if ($period->format("Y-m-d") == date("Y-m-d")) {
                            $awardWeekReport[$period->format("Y-m-d")] = 2;
                        } else {
                            $awardWeekReport[$period->format("Y-m-d")] = 1;
                        }
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

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }
}
