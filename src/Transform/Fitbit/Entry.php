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

namespace App\Transform\Fitbit;

use App\AppConstants;
use App\Entity\Patient;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\CommsManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use phpDocumentor\Reflection\Types\Object_;
use Psr\Log\LoggerInterface;
use Sentry;

/**
 * Class Entry
 *
 * @package App\Transform\Fitbit
 */
class Entry
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var Patient $patient */
    private $patient;

    /**
     * Entry constructor.
     *
     * @param LoggerInterface $logger
     * @param Patient         $patient
     */
    public function __construct(LoggerInterface $logger, Patient $patient = null)
    {
        $this->logger = $logger;
        $this->patient = $patient;
    }

    /**
     * @param String $data_set
     * @param                 $getContent
     * @param ManagerRegistry $doctrine
     * @param AwardManager $awardManager
     * @param ChallengePve $challengePve
     * @param CommsManager $commsManager
     *
     * @return array|int|null
     * @throws Exception
     */
    public function transform(
        string $data_set,
        $getContent,
        ManagerRegistry $doctrine,
        AwardManager $awardManager,
        ChallengePve $challengePve,
        CommsManager $commsManager
    ) {
        $translateEntity = null;

        if (!is_null($this->patient)) {
            Sentry\configureScope(function (Sentry\State\Scope $scope) use ($data_set): void {
                $scope->setUser([
                    'id' => $this->patient->getId(),
                    'username' => $this->patient->getUsername(),
                    'email' => $this->patient->getEmail(),
                    'service' => 'Fitbit',
                    'data_set' => $data_set,
                ]);
            });
        } else {
            Sentry\configureScope(function (Sentry\State\Scope $scope) use ($data_set): void {
                $scope->setUser([
                    'service' => 'Fitbit',
                    'data_set' => $data_set,
                ]);
            });
        }

        Sentry\configureScope(function (Sentry\State\Scope $scope) use ($data_set, $getContent): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
                'content' => $getContent,
                'service' => 'Fitbit',
                'data_set' => $data_set,
            ]);
        });

        switch ($data_set) {
            case Constants::FITBITHEPDAILYSTEPS:
                $translateEntity = [];
                try {
                    $translateEntity[] = FitbitCountDailySteps::translate($doctrine, $getContent, $awardManager,
                        $challengePve);
                } catch (Exception $e) {
                }
                foreach ($getContent[1] as $index => $item) {
                    $translateEntity[] = FitbitDevices::translate($doctrine, $getContent, $index);
                }

                break;
            case Constants::FITBITHEPPERIODSTEPS:
                /** @noinspection PhpUnhandledExceptionInspection */
                $translateEntity = FitbitCountPeriodSteps::translate($doctrine, $getContent);
                break;
            case Constants::FITBITHEPDAILYSTEPSEXERCISE:
                $translateEntity = [];
                try {
                    $translateEntity[] = FitbitCountDailySteps::translate($doctrine, $getContent, $awardManager,
                        $challengePve);
                } catch (Exception $e) {
                }

                foreach ($getContent[1] as $index => $item) {
                    $translateEntity[] = FitbitDevices::translate($doctrine, $getContent, $index);
                }

                if (array_key_exists(3, $getContent) && property_exists($getContent[3],
                        "activities") && $getContent[0]->uuid == "testfitbit") {
                    foreach ($getContent[3]->activities as $index => $item) {
                        try {
                            //translate(ManagerRegistry $doctrine, CommsManager $commsManager, $getContent, int $deviceArrayIndex = 0)
                            $translateEntity[] = FitbitExercise::translate($doctrine, $commsManager, $getContent,
                                $index);
                        } catch (Exception $e) {
                        }
                    }
                }
                break;
            case Constants::FITBITEXERCISE:
                $translateEntity = [];
                $getContent[3] = $getContent[2];
                unset($getContent[2]);

                foreach ($getContent[1] as $index => $item) {
                    $translateEntity[] = FitbitDevices::translate($doctrine, $getContent, $index);
                }

                if (array_key_exists(3, $getContent) && property_exists($getContent[3],
                        "activities") && $getContent[0]->uuid == "testfitbit") {
                    foreach ($getContent[3]->activities as $index => $item) {
                        try {
                            //translate(ManagerRegistry $doctrine, CommsManager $commsManager, $getContent, int $deviceArrayIndex = 0)
                            $translateEntity[] = FitbitExercise::translate($doctrine, $commsManager, $getContent,
                                $index);
                        } catch (Exception $e) {
                        }
                    }
                }
                break;
            case Constants::FITBITEPBODYWEIGHT:

                $translateEntity = [];
                if (is_array($getContent[2]->weight)) {
                    foreach ($getContent[2]->weight as $weightItem) {
                        $jsonItem = [];
                        $jsonItem[0] = $getContent[0];
                        $jsonItem[1] = $getContent[1];
                        $jsonItem[2] = $weightItem;

                        $jsonItem[0]->dateTime = $weightItem->date . " " . $weightItem->time;

                        /** @noinspection PhpUnhandledExceptionInspection */
                        $translateEntity[] = FitbitBodyWeight::translate($doctrine, $jsonItem, $awardManager,
                            $commsManager);
                        /** @noinspection PhpUnhandledExceptionInspection */
                        $translateEntity[] = FitbitBodyFat::translate($doctrine, $jsonItem, $awardManager);
                    }
                } else {
                    if (is_object($getContent)) {
                        /** @noinspection PhpUnhandledExceptionInspection */
                        $translateEntity[] = FitbitBodyWeight::translate($doctrine, $getContent, $awardManager,
                            $commsManager);
                        /** @noinspection PhpUnhandledExceptionInspection */
                        $translateEntity[] = FitbitBodyFat::translate($doctrine, $getContent, $awardManager);
                    } else {
                        AppConstants::writeToLog('debug_transform.txt', "[" . __LINE__ . "] - Something else passed");
                    }
                }

                foreach ($getContent[1] as $index => $item) {
                    $translateEntity[] = FitbitDevices::translate($doctrine, $getContent, $index);
                }
                break;
            default:
                AppConstants::writeToLog('debug_transform.txt',
                    __LINE__ . ' MISSING ' . $data_set . ' - ' . print_r($getContent, true));
                return -3;
                break;
        }

        if (!is_null($translateEntity)) {
            $entityManager = $doctrine->getManager();
            if (!is_array($translateEntity) && is_object($translateEntity)) {
                /** @var Object $translateEntity */
                $entityManager->persist($translateEntity);
                /** @noinspection PhpUndefinedMethodInspection */
                $returnId = $translateEntity->getId();
            } else {
                $returnId = [];
                foreach ($translateEntity as $item) {
                    if (!is_null($item)) {
                        if (is_array($item)) {
                            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' - Got Another Array');
                        } else {
                            if (is_object($item)) {
                                $entityManager->persist($item);
                                array_push($returnId, $item->getId());
                            }
                        }
                    }
                }
            }
            $entityManager->flush();

            return $returnId;
        } else {
            return -1;
        }
    }
}
