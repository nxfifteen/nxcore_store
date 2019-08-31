<?php

namespace App\Transform\SamsungHealth;

use App\AppConstants;
use App\Entity\FitStepsDailySummary;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use Doctrine\Common\Persistence\ManagerRegistry;

class SamsungCountDailySteps extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @return FitStepsDailySummary|null
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent)
    {
        $jsonContent = self::decodeJson($getContent);
        AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {
            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent->uuid);
            if (is_null($patient)) {
                return NULL;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::SAMSUNGHEALTHSERVICE);
            if (is_null($thirdPartyService)) {
                return NULL;
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $jsonContent->device);
            if (is_null($deviceTracking)) {
                return NULL;
            }

            /** @var PartOfDay $dataEntry */
            $partOfDay = self::getPartOfDay($doctrine, new \DateTime($jsonContent->dateTime));
            if (is_null($partOfDay)) {
                return NULL;
            }

            /** @var FitStepsDailySummary $dataEntry */
            $dataEntry = $doctrine->getRepository(FitStepsDailySummary::class)->findOneBy(['RemoteId' => $jsonContent->remoteId, 'patient' => $patient, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntry) {
                $dataEntry = new FitStepsDailySummary();
            }

            $dataEntry->setPatient($patient);
            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent->remoteId);
            $dataEntry->setValue($jsonContent->value);
            $dataEntry->setGoal($jsonContent->goal);
            $dataEntry->setDateTime(new \DateTime($jsonContent->dateTime));
            $dataEntry->setPartOfDay($partOfDay);
            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            return $dataEntry;

        }

        return NULL;
    }
}