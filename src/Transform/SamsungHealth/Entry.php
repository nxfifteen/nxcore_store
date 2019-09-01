<?php

namespace App\Transform\SamsungHealth;


use App\Entity\FitFloorsIntraDay;
use App\Entity\FitStepsDailySummary;
use App\Entity\FitStepsIntraDay;
use App\Entity\TrackingDevice;
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
                /** @var TrackingDevice $translateEntity */
                $translateEntity = SamsungDevices::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPDAILYSTEPS:
                /** @var FitStepsDailySummary $translateEntity */
                $translateEntity = SamsungCountDailySteps::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPINTRADAYFLOORS:
                /** @var FitFloorsIntraDay $translateEntity */
                $translateEntity = SamsungIntraDayFloors::translate($doctrine, $getContent);
                break;
            case Constants::SAMSUNGHEALTHEPINTRADAYSTEPS:
                /** @var FitStepsIntraDay $translateEntity */
                $translateEntity = SamsungIntraDaySteps::translate($doctrine, $getContent);
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