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
            case Constants::SAMSUNGHEALTHEPBODYWEIGHT:
                $translateEntity = SamsungBodyWeight::translate($doctrine, $getContent);
                break;
            default:
                return -3;
                break;
        }

        if (!is_null($translateEntity)) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($translateEntity);
            $entityManager->flush();

            return $translateEntity->getId();
        } else {
            return -1;
        }
    }
}