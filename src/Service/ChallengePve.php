<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */
/** @noinspection DuplicatedCode */

namespace App\Service;

use App\AppConstants;
use App\Entity\FitDistanceDailySummary;
use App\Entity\FitStepsDailySummary;
use App\Entity\RpgChallengeGlobal;
use App\Entity\RpgChallengeGlobalPatient;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class ChallengePve
 *
 * @package App\Service
 */
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

    /**
     * @var CommsManager
     */
    private $commsManager;

    /**
     * ChallengePve constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param AwardManager    $awardManager
     * @param CommsManager    $commsManager
     */
    public function __construct(
        ManagerRegistry $doctrine,
        AwardManager $awardManager,
        CommsManager $commsManager
    )
    {
        $this->doctrine = $doctrine;
        $this->awardManager = $awardManager;
        $this->commsManager = $commsManager;
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
                $criteriaTakenSincePVEStartDateDB = NULL;
                switch ($challengeCriteria) {
                    case "FitStepsDailySummary":
                        /** @var FitStepsDailySummary[] $criteriaTakenSincePVEStartDateDB */
                        $criteriaTakenSincePVEStartDateDB = $this->doctrine->getRepository(FitStepsDailySummary::class)->findSince($dataEntry->getPatient()->getUuid(), $dbRpgChallengeGlobal->getStartDateTime());
                        break;
                    case "FitDistanceDailySummary":
                        /** @var FitDistanceDailySummary[] $criteriaTakenSincePVEStartDateDB */
                        $criteriaTakenSincePVEStartDateDB = $this->doctrine->getRepository(FitDistanceDailySummary::class)->findSince($dataEntry->getPatient()->getUuid(), $dbRpgChallengeGlobal->getStartDateTime());
                        break;
                }

                if ($criteriaTakenSincePVEStartDateDB) {
                    $criteriaTakenSincePVEStartDate = 0;
                    foreach ($criteriaTakenSincePVEStartDateDB as $item) {
                        $criteriaTakenSincePVEStartDate = $criteriaTakenSincePVEStartDate + $item->getValue();
                    }

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
                            ->orderBy('c.target', 'DESC')
                            ->getQuery()->getResult();

                        if ($previousStage) {
                            $return['participation']['debug']['prevStage'] = $previousStage[0]->getId();
                            foreach ($previousStage as $item) {
                                $baseRequired = $baseRequired + $item->getTarget();
                            }
                        }
                    }

                    $comparisonTarget = ($comparisonTarget + $baseRequired);

                    if ($criteriaTakenSincePVEStartDate < $baseRequired) {
                        $dbRpgChallengeGlobal->setProgress(0);
                        $entityManager->persist($dbRpgChallengeGlobal);
                    } else if ($criteriaTakenSincePVEStartDate > $comparisonTarget) {
                        $dbRpgChallengeGlobal->setProgress(100);
                        $dbRpgChallengeGlobal->setFinishDateTime(new DateTime());
                        $entityManager->persist($dbRpgChallengeGlobal);

                        if (is_null($dbRpgChallengeGlobal->getChallenge()->getProgression()) && is_null($dbRpgChallengeGlobal->getChallenge()->getChildOf())) {
                            $this->awardManager->checkForAwards(
                                $dbRpgChallengeGlobal->getChallenge(),
                                "challenge",
                                $dataEntry->getPatient(),
                                'Completed Global Challenge ' . $dbRpgChallengeGlobal->getChallenge()->getName(),
                                $dataEntry->getDateTime()
                            );
                        } else if (is_null($dbRpgChallengeGlobal->getChallenge()->getProgression()) && !is_null($dbRpgChallengeGlobal->getChallenge()->getChildOf())) {
                            $this->awardManager->checkForAwards(
                                $dbRpgChallengeGlobal->getChallenge(),
                                "challenge",
                                $dataEntry->getPatient(),
                                'Completed Global Challenge Leg ' . substr($dbRpgChallengeGlobal->getChallenge()->getDescripton(), 0, 223),
                                $dataEntry->getDateTime()
                            );
                        } else if (!is_null($dbRpgChallengeGlobal->getChallenge()->getProgression()) && !is_null($dbRpgChallengeGlobal->getChallenge()->getChildOf())) {
                            $this->awardManager->checkForAwards(
                                $dbRpgChallengeGlobal->getChallenge(),
                                "challenge",
                                $dataEntry->getPatient(),
                                'Completed Global Challenge Stage ' . $dbRpgChallengeGlobal->getChallenge()->getName(),
                                $dataEntry->getDateTime()
                            );
                        } else {
                            $this->awardManager->checkForAwards(
                                $dbRpgChallengeGlobal->getChallenge(),
                                "challenge",
                                $dataEntry->getPatient(),
                                'Completed PVE Challenge ' . $dbRpgChallengeGlobal->getChallenge()->getName(),
                                $dataEntry->getDateTime()
                            );
                        }
                    } else {
                        $dbRpgChallengeGlobal->setProgress(round(($criteriaTakenSincePVEStartDate / $comparisonTarget) * 100, 0, PHP_ROUND_HALF_DOWN));
                        $entityManager->persist($dbRpgChallengeGlobal);
                    }
                }
            }

            $entityManager->flush();
        }
    }

    /**
     * @param $value
     * @param $valueUnit
     * @param $targetUnit
     *
     * @return float|int
     */
    protected static function convertUnitOfMeasurement($value, $valueUnit, $targetUnit)
    {
        return AppConstants::convertUnitOfMeasurement($value, $valueUnit, $targetUnit);
    }

    /**
     * @param RpgChallengeGlobal $dbRpgChallengeGlobal
     *
     * @return RpgChallengeGlobal
     */
    private function getRootChallenge(RpgChallengeGlobal $dbRpgChallengeGlobal)
    {
        if (!is_null($dbRpgChallengeGlobal->getChildOf())) {
            return $this->getRootChallenge($dbRpgChallengeGlobal->getChildOf());
        } else {
            return $dbRpgChallengeGlobal;
        }
    }

}
