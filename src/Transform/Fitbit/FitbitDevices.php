<?php

namespace App\Transform\Fitbit;


use App\AppConstants;
use App\Entity\Patient;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use Doctrine\Common\Persistence\ManagerRegistry;

class FitbitDevices extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param                 $jsonContent
     *
     * @param int             $deviceArrayIndex
     *
     * @return TrackingDevice|null
     */
    public static function translate(ManagerRegistry $doctrine, $jsonContent, int $deviceArrayIndex = 0)
    {
        if (property_exists($jsonContent[1][$deviceArrayIndex], "id")) {
            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent[0]->uuid);
            if (is_null($patient)) {
                return NULL;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::FITBITSERVICE);
            if (is_null($thirdPartyService)) {
                return NULL;
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $jsonContent[1][$deviceArrayIndex]->id);
            if (is_null($deviceTracking)) {
                return NULL;
            }

            $deviceTracking->setManufacturer(self::FITBITSERVICE);
            if (property_exists($jsonContent[1][$deviceArrayIndex], "deviceVersion")) {
                if (is_null($deviceTracking->getName()) || $deviceTracking->getName() == $deviceTracking->getRemoteId()) $deviceTracking->setName($jsonContent[1][$deviceArrayIndex]->deviceVersion);
                $deviceTracking->setModel($jsonContent[1][$deviceArrayIndex]->deviceVersion);
            }
            if (property_exists($jsonContent[1][$deviceArrayIndex], "type")) $deviceTracking->setType($jsonContent[1][$deviceArrayIndex]->type);
            if (property_exists($jsonContent[1][$deviceArrayIndex], "battery")) $deviceTracking->setBattery($jsonContent[1][$deviceArrayIndex]->batteryLevel);
            if (property_exists($jsonContent[1][$deviceArrayIndex], "lastSynced")) $deviceTracking->setLastSynced($jsonContent[1][$deviceArrayIndex]->lastSyncTime);

            return $deviceTracking;

        }

        return NULL;
    }
}