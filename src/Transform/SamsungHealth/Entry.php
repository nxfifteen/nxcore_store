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

namespace App\Transform\SamsungHealth;

use App\AppConstants;
use App\Entity\Patient;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\TweetManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sentry;

/**
 * Class Entry
 *
 * @package App\Transform\SamsungHealth
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
     * @param String          $data_set
     * @param String          $getContent
     * @param ManagerRegistry $doctrine
     * @param AwardManager    $awardManager
     * @param ChallengePve    $challengePve
     * @param TweetManager    $tweetManager
     *
     * @return array|int|null
     */
    public function transform(String $data_set, String $getContent, ManagerRegistry $doctrine, AwardManager $awardManager, ChallengePve $challengePve, TweetManager $tweetManager)
    {
        $translateEntity = NULL;

        Sentry\configureScope(function (Sentry\State\Scope $scope) use ($data_set): void {
            $scope->setUser([
                'service' => 'Samsung Health',
                'data_set' => $data_set,
            ]);
        });

        switch ($data_set) {
            case Constants::SAMSUNGHEALTHEPDEVICES:
                $translateEntity = SamsungDevices::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPDAILYSTEPS:
                try {
                    $translateEntity = SamsungCountDailySteps::translate($doctrine, $getContent, $awardManager, $challengePve);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHEPINTRADAYFLOORS:
                try {
                    $translateEntity = SamsungIntraDayFloors::translate($doctrine, $getContent, $awardManager);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHEPINTRADAYSTEPS:
                try {
                    $translateEntity = SamsungIntraDaySteps::translate($doctrine, $getContent, $awardManager);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHEPCONSUMWATER:
                try {
                    $translateEntity = SamsungConsumeWater::translate($doctrine, $getContent, $awardManager);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHEPCONSUMCAFFINE:
                try {
                    $translateEntity = SamsungConsumeCaffeine::translate($doctrine, $getContent, $awardManager);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHEPEXERCISE:
                try {
                    $translateEntity = SamsungExercise::translate($doctrine, $getContent, $awardManager, $tweetManager);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHCALORIES:
                try {
                    $translateEntity = SamsungCountDailyCalories::translate($doctrine, $getContent, $awardManager);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHDISTNACE:
                try {
                    $translateEntity = SamsungCountDailyDistance::translate($doctrine, $getContent, $awardManager, $challengePve);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHFOOD:
                try {
                    $translateEntity = SamsungFood::translateFood($doctrine, $getContent, $awardManager);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHFOODLOG:
                try {
                    $translateEntity = SamsungFood::translateFoodIntake($doctrine, $getContent, $awardManager);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHFOODDATABASE:
                try {
                    $translateEntity = SamsungFood::translateFoodInfo($doctrine, $getContent, $awardManager);
                } catch (\Exception $e) {
                }
                break;
            case Constants::SAMSUNGHEALTHEPBODYWEIGHT:
//                $translateEntity = [];
//                array_push($translateEntity, SamsungBodyWeight::translate($doctrine, $getContent));
//                array_push($translateEntity, SamsungBodyFat::translate($doctrine, $getContent));
//                array_push($translateEntity, SamsungBodyComposition::translate($doctrine, $getContent));
                return -4;
                break;
            default:
                return -3;
                break;
        }

        if (!is_null($translateEntity)) {
            $entityManager = $doctrine->getManager();
            if (!is_array($translateEntity)) {
                $entityManager->persist($translateEntity);
                $returnId = $translateEntity->getId();
            } else {
                $returnId = [];
                foreach ($translateEntity as $item) {
                    if (!is_null($item)) {
                        $entityManager->persist($item);
                        array_push($returnId, $item->getId());
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
