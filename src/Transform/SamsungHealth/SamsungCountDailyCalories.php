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
use App\Entity\FitCaloriesDailySummary;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Service\AwardManager;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class SamsungCountDailyCalories
 *
 * @package App\Transform\SamsungHealth
 */
class SamsungCountDailyCalories extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return FitCaloriesDailySummary|null
     * @throws \Exception
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid") && $jsonContent->device == 'VfS0qUERdZ') {
            ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - New call too FitCaloriesDailySummary for " . $jsonContent->remoteId);

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

            /** @var PatientGoals $patientGoal */
            $patientGoal = self::getPatientGoal($doctrine, "FitCaloriesDailySummary", $jsonContent->goal, NULL, $patient);
            if (is_null($patientGoal)) {
                return NULL;
            }

            /** @var FitCaloriesDailySummary $dataEntry */
            $dataEntry = $doctrine->getRepository(FitCaloriesDailySummary::class)->findOneBy(['RemoteId' => $jsonContent->remoteId, 'patient' => $patient, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntry) {
                $dataEntry = new FitCaloriesDailySummary();
            }

            $dataEntry->setPatient($patient);
            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent->remoteId);
            $dataEntry->setValue($jsonContent->value);
            $dataEntry->setGoal($patientGoal);

            $dayStartTime = strtotime($jsonContent->dateTimeDayTime);
            $dayEndTime = strtotime(date("Y-m-d 23:59:59", $dayStartTime));
            $updateTime = strtotime($jsonContent->dateTimeUpdated) + (60*60);
            if ($updateTime > $dayEndTime) {
                $updateTime = $dayEndTime;
            }

            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") < $updateTime) {
                $dataEntry->setDateTime(new \DateTime(date("Y-m-d H:i:s", $updateTime)));
            }

            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntry)), $patient, $thirdPartyService, $dataEntry->getDateTime());

            return $dataEntry;

        }

        return NULL;
    }
}
