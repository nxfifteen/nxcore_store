<?php

namespace App\Transform\Fitbit;

use App\AppConstants;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sentry;

class Entry
{

    private $logger;

    /**
     * Entry constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function transform(String $data_set, $getContent, ManagerRegistry $doctrine, AwardManager $awardManager, ChallengePve $challengePve)
    {
        $translateEntity = NULL;

        Sentry\configureScope(function (Sentry\State\Scope $scope) use ($data_set, $getContent): void {
            $scope->setUser([
                'content' => $getContent,
                'service' => 'Fitbit',
                'data_set' => $data_set,
            ]);
        });

        switch ($data_set) {
            case Constants::FITBITHEPDAILYSTEPS:
                $translateEntity = [];
                try {
                    $translateEntity[] = FitbitCountDailySteps::translate($doctrine, $getContent, $awardManager, $challengePve);
                } catch (\Exception $e) {
                }
                foreach ($getContent[1] as $index => $item) {
                    $translateEntity[] = FitbitDevices::translate($doctrine, $getContent, $index);
                }

                break;
            case Constants::FITBITHEPPERIODSTEPS:
                /** @noinspection PhpUnhandledExceptionInspection */
                $translateEntity = FitbitCountPeriodSteps::translate($doctrine, $getContent);
                break;
            case Constants::FITBITEXERCISE:
                $translateEntity = [];
                try {
                    $translateEntity[] = FitbitCountDailySteps::translate($doctrine, $getContent, $awardManager, $challengePve);
                } catch (\Exception $e) {
                }

                foreach ($getContent[1] as $index => $item) {
                    $translateEntity[] = FitbitDevices::translate($doctrine, $getContent, $index);
                }

                if (array_key_exists(3, $getContent) && property_exists($getContent[3], "activities") && $getContent[0]->uuid == "testfitbit") {
                    foreach ($getContent[3]->activities as $index => $item) {
                        try {
                            $translateEntity[] = FitbitExercise::translate($doctrine, $getContent, $index, $challengePve);
                        } catch (\Exception $e) {
                        }
                    }
                }
                break;
            case Constants::FITBITEPBODYWEIGHT:
                $translateEntity = [];
                /** @noinspection PhpUnhandledExceptionInspection */
                $translateEntity[] = FitbitBodyWeight::translate($doctrine, $getContent, $awardManager);
                /** @noinspection PhpUnhandledExceptionInspection */
                $translateEntity[] = FitbitBodyFat::translate($doctrine, $getContent, $awardManager);
                foreach ($getContent[1] as $index => $item) {
                    $translateEntity[] = FitbitDevices::translate($doctrine, $getContent, $index);
                }
                break;
            default:
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' MISSING ' . $data_set . ' - ' . print_r($getContent, TRUE));
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
                        } else if (is_object($item)) {
                            $entityManager->persist($item);
                            array_push($returnId, $item->getId());
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