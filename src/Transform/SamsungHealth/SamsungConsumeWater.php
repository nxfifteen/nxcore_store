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
use App\Entity\ConsumeWater;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use App\Service\AwardManager;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class SamsungConsumeWater
 *
 * @package App\Transform\SamsungHealth
 */
class SamsungConsumeWater extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return ConsumeWater|null
     * @throws \Exception
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {
            ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - New call too ConsumeWater for " . $jsonContent->remoteId);

            try {
                $jsonContent->dateTime = new DateTime($jsonContent->dateTime);
                $jsonContent->dateRaw = $jsonContent->dateTime;
            } catch (Exception $e) {
                return NULL;
            }

            $timeDiff = 1;
            if ($timeDiff > 0) {
                $jsonContent->dateTime->modify('+ ' . $timeDiff . ' hour');
            } else if ($timeDiff < 0) {
                $jsonContent->dateTime->modify('- ' . $timeDiff . ' hour');
            }

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
            $partOfDay = self::getPartOfDay($doctrine, $jsonContent->dateTime);
            if (is_null($partOfDay)) {
                return NULL;
            }

            /** @var UnitOfMeasurement $unitOfMeasurement */
            $unitOfMeasurement = self::getUnitOfMeasurement($doctrine, $jsonContent->units);
            if (is_null($unitOfMeasurement)) {
                return NULL;
            }

            /** @var PatientGoals $patientGoal */
            $patientGoal = self::getPatientGoal($doctrine, "Water", '2000', $unitOfMeasurement, $patient);
            if (is_null($patientGoal)) {
                return NULL;
            }

            /** @var ConsumeWater $dataEntry */
            $dataEntry = $doctrine->getRepository(ConsumeWater::class)->findOneBy(['RemoteId' => $jsonContent->remoteId, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntry) {
                $dataEntry = new ConsumeWater();
            }

            $dataEntry->setPatient($patient);
            $dataEntry->setPartOfDay($partOfDay);
            $dataEntry->setRemoteId($jsonContent->remoteId);
            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> $jsonContent->dateTime->format("U")) {
                $dataEntry->setDateTime($jsonContent->dateTime);
            }
            if (property_exists($jsonContent, "comment")) $dataEntry->setComment($jsonContent->comment);
            $dataEntry->setMeasurement($jsonContent->measurement);
            $dataEntry->setUnitOfMeasurement($unitOfMeasurement);
            $dataEntry->setService($thirdPartyService);
            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setPatientGoal($patientGoal);
            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

//                if ($dataEntry->getMeasurement() >= $dataEntry->getPatientGoal()->getGoal()) {
//                    $patient = self::awardPatientReward(
//                        $doctrine,
//                        $patient,
//                        $dataEntry->getDateTime(),
//                        "Water Target Achieved",
//                        0.07,
//                        "trg_water_achieved",
//                        "You drank it all",
//                        "Today you did it! You drank everything you wanted too"
//                    );
//                }

            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntry)), $patient, $thirdPartyService, $dataEntry->getDateTime());

            $awardManager->checkForAwards($dataEntry, "water");

            return $dataEntry;

        }

        return NULL;
    }
}
