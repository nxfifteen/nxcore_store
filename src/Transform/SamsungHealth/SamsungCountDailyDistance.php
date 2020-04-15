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
use App\Entity\FitDistanceDailySummary;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class SamsungCountDailyDistance
 *
 * @package App\Transform\SamsungHealth
 */
class SamsungCountDailyDistance extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @param ChallengePve    $challengePve
     *
     * @return FitDistanceDailySummary|null
     * @throws Exception
     */
    public static function translate(
        ManagerRegistry $doctrine,
        string $getContent,
        AwardManager $awardManager,
        ChallengePve $challengePve
    ) {
        $jsonContent = self::decodeJson($getContent);

        if (property_exists($jsonContent, "uuid") && $jsonContent->device == 'VfS0qUERdZ') {
            //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

            if ($jsonContent->goal == 0) {
                $jsonContent->goal = 3000;
            }

            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent->uuid);
            if (is_null($patient)) {
                return null;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::SAMSUNGHEALTHSERVICE);
            if (is_null($thirdPartyService)) {
                return null;
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $jsonContent->device);
            if (is_null($deviceTracking)) {
                return null;
            }

            /** @var UnitOfMeasurement $unitOfMeasurement */
            $unitOfMeasurement = self::getUnitOfMeasurement($doctrine, $jsonContent->units);
            if (is_null($unitOfMeasurement)) {
                return null;
            }

            /** @var PatientGoals $patientGoal */
            $patientGoal = self::getPatientGoal($doctrine, "FitDistanceDailySummary", $jsonContent->goal,
                $unitOfMeasurement, $patient);
            if (is_null($patientGoal)) {
                return null;
            }

            /** @var FitDistanceDailySummary $dataEntry */
            $dataEntry = $doctrine->getRepository(FitDistanceDailySummary::class)->findOneBy([
                'RemoteId' => $jsonContent->remoteId,
                'patient' => $patient,
                'trackingDevice' => $deviceTracking,
            ]);
            if (!$dataEntry) {
                $dataEntry = new FitDistanceDailySummary();
                $safeGuid = false;
                $i = 0;
                do {
                    $i++;
                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': Added a GUID (' . $i . ')');
                    $dataEntry->createGuid(true);
                    $dataEntryGuidCheck = $doctrine
                        ->getRepository(FitDistanceDailySummary::class)
                        ->findByGuid($dataEntry->getGuid());
                    if (empty($dataEntryGuidCheck)) {
                        $safeGuid = true;
                    }

                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': ' . gettype($dataEntryGuidCheck));
                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': ' . count($dataEntryGuidCheck));
                } while (!$safeGuid);
            }

            $dataEntry->setPatient($patient);
            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent->remoteId);
            $dataEntry->setValue($jsonContent->value);
            $dataEntry->setGoal($patientGoal);
            $dataEntry->setUnitOfMeasurement($unitOfMeasurement);

            $dayStartTime = strtotime($jsonContent->dateTimeDayTime);
            $dayEndTime = strtotime(date("Y-m-d 23:59:59", $dayStartTime));
            $updateTime = strtotime($jsonContent->dateTimeUpdated) + (60 * 60);
            if ($updateTime > $dayEndTime) {
                $updateTime = $dayEndTime;
            }

            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") < $updateTime) {
                try {
                    $dataEntry->setDateTime(new DateTime(date("Y-m-d H:i:s", $updateTime)));
                } catch (Exception $e) {
                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '' . __LINE__ . ' = ' . $e->getMessage());
                }
            }

            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            if ($dataEntry->getTrackingDevice()->getId() == 3) {
                $awardManager->checkForAwards($dataEntry, "goal");
            }
            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntry)), $patient,
                $thirdPartyService, $dataEntry->getDateTime());
            try {
                $challengePve->checkAnyRunning($dataEntry);
            } catch (Exception $e) {
                AppConstants::writeToLog('debug_transform.txt', __FILE__ . '' . __LINE__ . ' = ' . $e->getMessage());
            }

            return $dataEntry;

        }

        return null;
    }
}
