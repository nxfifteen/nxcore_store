<?php
namespace App\Transform\SamsungHealth;


use App\AppConstants;
use App\Entity\Patient;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use Doctrine\Common\Persistence\ManagerRegistry;

class SamsungDevices extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @return TrackingDevice|null
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent)
    {
        $jsonContent = self::decodeJson($getContent);
        if (property_exists($jsonContent, "remoteId")) {
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - New call too TrackingDevice for " . $jsonContent->remoteId);

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
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $jsonContent->remoteId);
            if (is_null($deviceTracking)) {
                return NULL;
            }

            if (is_null($deviceTracking->getName()) && property_exists($jsonContent, "name")) $deviceTracking->setName($jsonContent->name);
            if (is_null($deviceTracking->getComment()) && property_exists($jsonContent, "comment")) $deviceTracking->setComment($jsonContent->comment);
            if (property_exists($jsonContent, "type")) $deviceTracking->setType($jsonContent->type);
            if (property_exists($jsonContent, "manufacturer")) $deviceTracking->setManufacturer($jsonContent->manufacturer);
            if (property_exists($jsonContent, "model")) $deviceTracking->setModel($jsonContent->model);
            if (property_exists($jsonContent, "battery")) $deviceTracking->setBattery($jsonContent->battery);
            if (property_exists($jsonContent, "lastSynced")) $deviceTracking->setLastSynced($jsonContent->lastSynced);

            return $deviceTracking;

        }

        return NULL;
    }
}