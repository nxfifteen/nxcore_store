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


use App\Service\AwardManager;
use App\Service\CommsManager;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @noinspection PhpUnused */

class DashboardRoute extends CommonFeeds
{
    /**
     * @Route("/feed/dashboard", name="ux_aggregator")
     *
     * @param AwardManager $awardManager
     *
     * @param CommsManager $commsManager
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(AwardManager $awardManager, CommsManager $commsManager)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $return['status'] = "okay";
        $return['code'] = "200";

        $return['milestones'] = $this->getPatientMilestones();
        $return['steps'] = $this->getPatientSteps();
        $return['floors'] = $this->getPatientFloors();
        $return['distance'] = $this->getPatientDistance();
        $return['rpg_friends'] = $this->getPatientFriends();
        $return['rpg_challenge_friends'] = $this->getPatientChallengesFriends(true);
        $return['awards'] = $this->getPatientAwards();
        $return['weight'] = $this->getPatientWeight();

        if (
            is_null($this->patient->getLastLoggedIn()) ||
            $this->patient->getLastLoggedIn()->format("Y-m-d") <> date("Y-m-d")
        ) {
            if (is_null($this->patient->getLastLoggedIn())) {
                $lastRecordedLogin = 0;
            } else {
                $lastRecordedLogin = $this->patient->getLastLoggedIn()->format("U");
            }
            $currentTime = date("U");
            $lastLoggedInWas = $currentTime - $lastRecordedLogin;

            if ($lastLoggedInWas > ((60 * 60 * 24) - 120)) {
                $this->patient->setLoginStreak(0);
            }

            $this->patient->setLastLoggedIn(new DateTime());
            $this->patient->setLoginStreak($this->patient->getLoginStreak() + 1);
            // checkForAwards($dataEntry, string $criteria = NULL, Patient $patient = NULL, string $citation = NULL, DateTimeInterface $dateTime = NULL)
            $awardManager->checkForAwards(["reason" => "first", "length" => 0], "login", $this->patient);

            $commsManager->sendNotification(
                "First login for " . date("l jS F, Y"),
                "You've #logged in " . $this->patient->getLoginStreak() . " days in a row! :clock1:",
                $this->patient,
                true
            );

            if ($this->patient->getLoginStreak() % 5 == 0) {
                $awardManager->checkForAwards(["reason" => "streak", "length" => $this->patient->getLoginStreak()],
                    "login", $this->patient);
            }

            if ($this->patient->getLoginStreak() % 182 == 0) {
                $awardManager->checkForAwards(["reason" => "streak", "length" => $this->patient->getLoginStreak()],
                    "login", $this->patient);

                $commsManager->sendNotification(
                    "@" . $this->patient->getUuid() . " #logged in " . $this->patient->getLoginStreak() . " days in a row! :clock1:",
                    null,
                    $this->patient,
                    false
                );
            } else {
                if ($this->patient->getLoginStreak() % 30 == 0) {
                    $awardManager->checkForAwards(["reason" => "streak", "length" => $this->patient->getLoginStreak()],
                        "login", $this->patient);

                    $commsManager->sendNotification(
                        "@" . $this->patient->getUuid() . " #logged in " . $this->patient->getLoginStreak() . " days in a row! :clock1:",
                        null,
                        $this->patient,
                        false
                    );
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($this->patient);
            $entityManager->flush();
        }

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }
}
