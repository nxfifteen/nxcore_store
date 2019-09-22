<?php

namespace App\Transform\Fitbit;

use App\AppConstants;
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

    public function transform(String $data_set, $getContent, ManagerRegistry $doctrine)
    {
        $translateEntity = NULL;

        switch ($data_set) {
            case Constants::FITBITHEPDAILYSTEPS:
                $translateEntity = [];
                $translateEntity[] = FitbitCountDailySteps::translate($doctrine, $getContent);
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