<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2019. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\AppConstants;
use App\Entity\FitDistanceDailySummary;
use App\Entity\FitStepsDailySummary;
use App\Entity\RpgChallengeGlobal;
use App\Entity\RpgChallengeGlobalPatient;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;

class ChallengePve
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var AwardManager
     */
    private $awardManager;

    public function __construct(
        ManagerRegistry $doctrine,
        AwardManager $awardManager
    )
    {
        $this->doctrine = $doctrine;
        $this->awardManager = $awardManager;
    }

    /**
     * @param FitStepsDailySummary|FitDistanceDailySummary $dataEntry
     *
     * @throws \Exception
     */
    public function checkAnyRunning($dataEntry)
    {
        $challengeCriteria = str_ireplace("App\\Entity\\", "", get_class($dataEntry));
        /** @var RpgChallengeGlobalPatient[] $dbRpgChallengeGlobals */
        $dbRpgChallengeGlobals = $this->doctrine
            ->getRepository(RpgChallengeGlobalPatient::class)
            ->findBy(['patient' => $dataEntry->getPatient(), 'criteria' => $challengeCriteria, 'finishDateTime' => NULL]);

        if ($dbRpgChallengeGlobals) {
            $entityManager = $this->doctrine->getManager();

            foreach ($dbRpgChallengeGlobals as $dbRpgChallengeGlobal) {
                $dataFindSince = NULL;
                switch ($challengeCriteria) {
                    case "FitStepsDailySummary":
                        /** @var FitStepsDailySummary[] $dataFindSince */
                        $dataFindSince = $this->doctrine->getRepository(FitStepsDailySummary::class)->findSince($dataEntry->getPatient()->getUuid(), $dbRpgChallengeGlobal->getStartDateTime());
                        break;
                    case "FitDistanceDailySummary":
                        /** @var FitDistanceDailySummary[] $dataFindSince */
                        $dataFindSince = $this->doctrine->getRepository(FitDistanceDailySummary::class)->findSince($dataEntry->getPatient()->getUuid(), $dbRpgChallengeGlobal->getStartDateTime());
                        break;
                }

                if ($dataFindSince) {
                    $comparisonTarget = $dbRpgChallengeGlobal->getChallenge()->getTarget();
                    if ($challengeCriteria == "FitDistanceDailySummary") {
                        if (
                            !is_null($dataEntry->getUnitOfMeasurement()) &&
                            !is_null($dbRpgChallengeGlobal->getChallenge()->getUnitOfMeasurement()) &&
                            $dataEntry->getUnitOfMeasurement()->getId() != $dbRpgChallengeGlobal->getChallenge()->getUnitOfMeasurement()->getId()
                        ) {
                            $comparisonTarget = $this->convertUnitOfMeasurement($dbRpgChallengeGlobal->getChallenge()->getTarget(), $dbRpgChallengeGlobal->getChallenge()->getUnitOfMeasurement()->getName(), $dataEntry->getUnitOfMeasurement()->getName());
                        }
                    }

                    $baseRequired = 0;
                    $challengeRoot = $this->getRootChallenge($dbRpgChallengeGlobal->getChallenge());
                    if ($challengeRoot->getProgression() == "stage") {
                        /** @var RpgChallengeGlobal[] $previousStage */
                        $previousStage = $this->doctrine
                            ->getRepository(RpgChallengeGlobal::class)
                            ->createQueryBuilder('c')
                            ->Where('c.target < :target')
                            ->setParameter('target', $dbRpgChallengeGlobal->getChallenge()->getTarget())
                            ->andWhere('c.childOf = :childOf')
                            ->setParameter('childOf', $dbRpgChallengeGlobal->getChallenge()->getChildOf())
                            ->setMaxResults(1)
                            ->orderBy('c.target', 'DESC')
                            ->getQuery()->getResult();

                        if ($previousStage) {
                            $return['participation']['debug']['prevStage'] = $previousStage[0]->getId();
                            $baseRequired = $previousStage[0]->getTarget();
                        }
                    }

                    $dataFindSinceValue = 0;
                    foreach ($dataFindSince as $item) {
                        $dataFindSinceValue = $dataFindSinceValue + $item->getValue();
                    }

                    $dataFindSinceValue = $dataFindSinceValue - $baseRequired;
                    if ($dataFindSinceValue < 0) $dataFindSinceValue = 0;

                    if ($dataFindSinceValue > $comparisonTarget) {
                        $dbRpgChallengeGlobal->setProgress(100);
                        $dbRpgChallengeGlobal->setFinishDateTime(new DateTime());
                        $entityManager->persist($dbRpgChallengeGlobal);

                        if (!is_null($dbRpgChallengeGlobal->getChallenge()->getReward())) {
                            $this->awardManager->giveReward($dataEntry->getPatient(), $dbRpgChallengeGlobal->getChallenge()->getReward(), $dataEntry->getDateTime());
                        }

                        if (!is_null($dbRpgChallengeGlobal->getChallenge()->getXp())) {
                            if (is_null($dbRpgChallengeGlobal->getChallenge()->getProgression()) && is_null($dbRpgChallengeGlobal->getChallenge()->getChildOf())) {
                                $this->awardManager->giveXp($dataEntry->getPatient(), $dbRpgChallengeGlobal->getChallenge()->getXp(), 'Completed Global Challenge ' . $dbRpgChallengeGlobal->getChallenge()->getName(), $dataEntry->getDateTime());
                            } else if (is_null($dbRpgChallengeGlobal->getChallenge()->getProgression()) && !is_null($dbRpgChallengeGlobal->getChallenge()->getChildOf())) {
                                $this->awardManager->giveXp($dataEntry->getPatient(), $dbRpgChallengeGlobal->getChallenge()->getXp(), 'Completed Global Challenge Leg ' . substr($dbRpgChallengeGlobal->getChallenge()->getDescripton(), 0, 223), $dataEntry->getDateTime());
                            } else if (!is_null($dbRpgChallengeGlobal->getChallenge()->getProgression()) && !is_null($dbRpgChallengeGlobal->getChallenge()->getChildOf())) {
                                $this->awardManager->giveXp($dataEntry->getPatient(), $dbRpgChallengeGlobal->getChallenge()->getXp(), 'Completed Global Challenge Stage ' . $dbRpgChallengeGlobal->getChallenge()->getName(), $dataEntry->getDateTime());
                            } else {
                                $this->awardManager->giveXp($dataEntry->getPatient(), $dbRpgChallengeGlobal->getChallenge()->getXp(), 'Completed PVE Challenge ' . $dbRpgChallengeGlobal->getChallenge()->getName(), $dataEntry->getDateTime());
                            }
                        }
                    } else {
                        $dbRpgChallengeGlobal->setProgress(round(($dataFindSinceValue / $comparisonTarget) * 100, 0, PHP_ROUND_HALF_DOWN));
                        $entityManager->persist($dbRpgChallengeGlobal);
                    }
                }
            }

            $entityManager->flush();
        }
    }

    private function getRootChallenge(RpgChallengeGlobal $dbRpgChallengeGlobal)
    {
        if (!is_null($dbRpgChallengeGlobal->getChildOf())) {
            return $this->getRootChallenge($dbRpgChallengeGlobal->getChildOf());
        } else {
            return $dbRpgChallengeGlobal;
        }
    }

    /**
     * @param $value
     * @param $targetUnit
     *
     * @return float|int
     */
    protected static function convertUnitOfMeasurement($value, $valueUnit, $targetUnit)
    {
        if ($valueUnit == "mile" && $targetUnit == "meter") {
            return $value * 1609.34;
        } else if ($valueUnit == "meter" && $targetUnit == "mile") {
            return $value / 1609.34;
        }

        return 0.5;
    }

}