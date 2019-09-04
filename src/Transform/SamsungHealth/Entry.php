<?php

namespace App\Transform\SamsungHealth;

use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

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

    public function transform(String $data_set, String $getContent, ManagerRegistry $doctrine)
    {
        $translateEntity = NULL;

        switch ($data_set) {
            case Constants::SAMSUNGHEALTHEPDEVICES:
                $translateEntity = SamsungDevices::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPDAILYSTEPS:
                $translateEntity = SamsungCountDailySteps::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPINTRADAYFLOORS:
                $translateEntity = SamsungIntraDayFloors::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPINTRADAYSTEPS:
                $translateEntity = SamsungIntraDaySteps::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPCONSUMWATER:
                $translateEntity = SamsungConsumeWater::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPCONSUMCAFFINE:
                $translateEntity = SamsungConsumeCaffeine::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPEXERCISE:
                $translateEntity = SamsungExercise::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHCALORIES:
                $translateEntity = SamsungCountDailyCalories::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHDISTNACE:
                $translateEntity = SamsungCountDailyDistance::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHFOOD:
                $translateEntity = SamsungFood::translateFood($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHFOODLOG:
                $translateEntity = SamsungFood::translateFoodIntake($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHFOODDATABASE:
                $translateEntity = SamsungFood::translateFoodInfo($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPBODYWEIGHT:
                $translateEntity = [];
                array_push($translateEntity, SamsungBodyWeight::translate($doctrine, $getContent));
                array_push($translateEntity, SamsungBodyFat::translate($doctrine, $getContent));
                array_push($translateEntity, SamsungBodyComposition::translate($doctrine, $getContent));
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