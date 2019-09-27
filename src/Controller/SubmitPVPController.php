<?php

namespace App\Controller;

use App\AppConstants;
use App\Entity\Patient;
use App\Entity\RpgChallengeFriends;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sentry;

class SubmitPVPController extends AbstractController
{
    /** @var Patient $patient */
    private $patient;

    /**
     * @Route("/submit/pvp/challenge", name="submit_pvp_challenge")
     * @param ManagerRegistry $doctrine
     * @param Request         $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_submit_pvp_challenge(ManagerRegistry $doctrine, Request $request)
    {
        if (is_null($this->patient)) $this->patient = $this->getUser();

        $requestBody = $request->getContent();
        $requestBody = str_replace("'", "\"", $requestBody);
        $requestJson = json_decode($requestBody, FALSE);

        Sentry\configureScope(function (Sentry\State\Scope $scope) use ($requestJson): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
                'challenged' => $requestJson->friend,
            ]);
        });

        $requestJson->criteria = $this->convertCriteria($requestJson->criteria);

        /** @var Patient $friend */
        $friend = $doctrine->getRepository(Patient::class)->findOneBy(['uuid' => $requestJson->friend]);
        if (!$friend) {
            return $this->json(["status" => 404]);
        }

        /** @var RpgChallengeFriends[] $dbChallenger */
        $dbChallenger = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findBy(["challenger" => $this->patient, "challenged" => $friend, "criteria" => $requestJson->criteria, "target" => $requestJson->target, "duration" => $requestJson->duration, "outcome" => NULL]);
        if (count($dbChallenger) > 0) {
            AppConstants::writeToLog('debug_transform.txt', $this->patient->getFirstName() . ' has already challenged ' . $friend->getFirstName() . ' to a ' . $requestJson->target . ' ' . $requestJson->criteria . ' match.');
            return $this->json(["status" => 401]);
        }

        /** @var RpgChallengeFriends[] $dbChallenged */
        $dbChallenged = $this->getDoctrine()
            ->getRepository(RpgChallengeFriends::class)
            ->findBy(["challenged" => $this->patient, "challenger" => $friend, "criteria" => $requestJson->criteria, "target" => $requestJson->target, "duration" => $requestJson->duration, "outcome" => NULL]);
        if (count($dbChallenged) > 0) {
            AppConstants::writeToLog('debug_transform.txt', $this->patient->getFirstName() . ' has already been challenged by ' . $friend->getFirstName() . ' to a ' . $requestJson->target . ' ' . $requestJson->criteria . ' match.');
            return $this->json(["status" => 401]);
        }

        AppConstants::writeToLog('debug_transform.txt', $this->patient->getFirstName() . ' is going to challenge ' . $friend->getFirstName() . ' to a ' . $requestJson->target . ' ' . $requestJson->criteria . ' match over ' . $requestJson->duration . ' days.');

        $newChallenge = new RpgChallengeFriends();
        $newChallenge->setChallenger($this->patient);
        $newChallenge->setChallenged($friend);
        $newChallenge->setCriteria($requestJson->criteria);
        $newChallenge->setTarget($requestJson->target);
        $newChallenge->setDuration($requestJson->duration);
        $newChallenge->setStartDate(new \DateTime());
        $newChallenge->setInviteDate(new \DateTime());
        $newChallenge->setEndDate($this->getEndDate($newChallenge));

        $entityManager = $doctrine->getManager();
        $entityManager->persist($newChallenge);
        $entityManager->flush();

        return $this->json(["status" => 200]);
    }

    private function convertCriteria($criteria)
    {
        switch ($criteria) {
            case "steps":
                return "FitStepsDailySummary";
                break;
            default:
                return $criteria;
                break;
        }
    }

    private function getEndDate(RpgChallengeFriends $challenge)
    {
        $endDate = new \DateTime($challenge->getStartDate()->format("Y-m-d 00:00:00"));
        try {
            $endDate->add(new \DateInterval("P" . $challenge->getDuration() . "D"));
        } catch (\Exception $e) {
        }

        return $endDate;
    }
}
