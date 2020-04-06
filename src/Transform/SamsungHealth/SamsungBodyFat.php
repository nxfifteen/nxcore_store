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
use App\Entity\BodyFat;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use App\Service\AwardManager;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class SamsungBodyFat
 *
 * @package App\Transform\SamsungHealth
 */
class SamsungBodyFat extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return BodyFat|null
     * @throws \Exception
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {
            ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - New call too BodyFat for " . $jsonContent->remoteId);

            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent->uuid);
            if (is_null($patient)) {
                return NULL;
            }

            if (self::startsWith($jsonContent->comment,"Fitbit: ") && $jsonContent->device == "yJdRrYpC43") {
                /** @var ThirdPartyService $thirdPartyService */
                $thirdPartyService = self::getThirdPartyService($doctrine, self::FITBITSERVICE);
            } else {
                /** @var ThirdPartyService $thirdPartyService */
                $thirdPartyService = self::getThirdPartyService($doctrine, self::SAMSUNGHEALTHSERVICE);
            }
            if (is_null($thirdPartyService)) {
                return NULL;
            }

            if (self::startsWith($jsonContent->comment,"Fitbit: ") && $jsonContent->device == "yJdRrYpC43") {
                /** @var TrackingDevice $deviceTracking */
                $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, "Aria");
            } else {
                /** @var TrackingDevice $deviceTracking */
                $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $jsonContent->device);
            }
            if (is_null($deviceTracking)) {
                return NULL;
            }

            /** @var PartOfDay $partOfDay */
            $partOfDay = self::getPartOfDay($doctrine, new \DateTime($jsonContent->dateTime));
            if (is_null($partOfDay)) {
                return NULL;
            }

            /** @var UnitOfMeasurement $unitOfMeasurement */
            $unitOfMeasurement = self::getUnitOfMeasurement($doctrine, $jsonContent->fatUnitOfMeasurement);
            if (is_null($unitOfMeasurement)) {
                return NULL;
            }

            /** @var PatientGoals $patientGoal */
            $patientGoal = self::getPatientGoal($doctrine, "BodyFat", $jsonContent->fatGoal, $unitOfMeasurement, $patient);
            if (is_null($patientGoal)) {
                return NULL;
            }

            /** @var BodyFat $dataEntry */
            $dataEntry = $doctrine->getRepository(BodyFat::class)->findOneBy(['RemoteId' => $jsonContent->remoteId, 'patient' => $patient, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntry) {
                $dataEntry = new BodyFat();
            }

            $dataEntry->setPatient($patient);

            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent->remoteId);
            $dataEntry->setMeasurement($jsonContent->fatMeasurement);
            $dataEntry->setUnitOfMeasurement($unitOfMeasurement);
            if ($jsonContent->fat_free > 0) $dataEntry->setFatFree($jsonContent->fat_free);
            if ($jsonContent->fat_free_mass > 0) $dataEntry->setFatFreeMass($jsonContent->fat_free_mass);
            if ($jsonContent->body_fat_mass > 0) $dataEntry->setBodyFatMass($jsonContent->body_fat_mass);
            $dataEntry->setPatientGoal($patientGoal);
            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> (new \DateTime($jsonContent->dateTime))->format("U")) {
                $dataEntry->setDateTime(new \DateTime($jsonContent->dateTime));
            }
            $dataEntry->setPartOfDay($partOfDay);
            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntry)), $patient, $thirdPartyService, $dataEntry->getDateTime());

            return $dataEntry;

        }

        return NULL;
    }
}
