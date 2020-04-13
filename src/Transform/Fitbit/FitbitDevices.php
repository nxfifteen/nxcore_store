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
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class FitbitDevices
 *
 * @package App\Transform\Fitbit
 */
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
                return null;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::FITBITSERVICE);
            if (is_null($thirdPartyService)) {
                return null;
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService,
                $jsonContent[1][$deviceArrayIndex]->id);
            if (is_null($deviceTracking)) {
                return null;
            }

            $deviceTracking->setManufacturer(self::FITBITSERVICE);
            if (property_exists($jsonContent[1][$deviceArrayIndex], "deviceVersion")) {
                if (is_null($deviceTracking->getName()) || $deviceTracking->getName() == $deviceTracking->getRemoteId()) {
                    $deviceTracking->setName($jsonContent[1][$deviceArrayIndex]->deviceVersion);
                }
                $deviceTracking->setModel($jsonContent[1][$deviceArrayIndex]->deviceVersion);
            }
            if (property_exists($jsonContent[1][$deviceArrayIndex], "type")) {
                $deviceTracking->setType($jsonContent[1][$deviceArrayIndex]->type);
            }
            if (property_exists($jsonContent[1][$deviceArrayIndex], "battery")) {
                $deviceTracking->setBattery($jsonContent[1][$deviceArrayIndex]->batteryLevel);
            }
            if (property_exists($jsonContent[1][$deviceArrayIndex], "lastSynced")) {
                $deviceTracking->setLastSynced($jsonContent[1][$deviceArrayIndex]->lastSyncTime);
            }

            return $deviceTracking;

        }

        return null;
    }
}
