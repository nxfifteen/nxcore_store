<?php

namespace App\Transform\Fitbit;

use App\AppConstants;
use App\Entity\FitStepsDailySummary;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\RpgChallengeGlobalPatient;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

class FitbitCountDailySteps extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param                 $jsonContent
     *
     * @param AwardManager    $awardManager
     *
     * @param ChallengePve    $challengePve
     *
     * @return FitStepsDailySummary|FitStepsDailySummary[]|null
     * @throws Exception
     */
    public static function translate(ManagerRegistry $doctrine, $jsonContent, AwardManager $awardManager, ChallengePve $challengePve)
    {
        if (property_exists($jsonContent[0], "uuid")) {
            //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

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

            $trackingDeviceRemoteId = self::FITBITSERVICE;
            if ($jsonContent[0]->dateTime == date("Y-m-d 00:00:00")) {
                try {
                    $jsonContent[0]->dateTime = new DateTime(str_replace(" 00:00:00", "", $jsonContent[0]->dateTime) . ' ' . date("H:i:s"));
                } catch (Exception $e) {
                    AppConstants::writeToLog('debug_transform.txt', __FILE__ . '' . __LINE__ . ' = ' . $e->getMessage());
                }
            } else {
                try {
                    $jsonContent[0]->dateTime = new DateTime($jsonContent[0]->dateTime);
                } catch (Exception $e) {
                    AppConstants::writeToLog('debug_transform.txt', __FILE__ . '' . __LINE__ . ' = ' . $e->getMessage());
                }
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $trackingDeviceRemoteId);
            if (is_null($deviceTracking)) {
                return NULL;
            }

            if (count($jsonContent) >= 3 && array_key_exists(2, $jsonContent) && is_object($jsonContent[2]) && property_exists($jsonContent[2], "goals") && property_exists($jsonContent[2]->goals, "steps")) {
                /** @var PatientGoals $patientGoal */
                $patientGoal = self::getPatientGoal($doctrine, "FitStepsDailySummary", $jsonContent[2]->goals->steps, NULL, $patient, FALSE);
                if (is_null($patientGoal)) {
                    return NULL;
                }
            } else {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . print_r($jsonContent, TRUE));
                return NULL;
            }

            $jsonContent[0]->remoteId = $jsonContent[0]->remoteId . 'FitStepsDailySummary' . $jsonContent[0]->dateTime->format("Y-m-d");

            /** @var FitStepsDailySummary $dataEntry */
            $dataEntry = $doctrine->getRepository(FitStepsDailySummary::class)->findOneBy([
                'RemoteId' => $jsonContent[0]->remoteId,
                'patient' => $patient,
                'trackingDevice' => $deviceTracking,
            ]);
            if (!$dataEntry) {
                $dataEntry = new FitStepsDailySummary();
                $dataEntry->setGoal($patientGoal);
            }

            $dataEntry->setPatient($patient);
            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent[0]->remoteId);
            $dataEntry->setValue($jsonContent[2]->summary->steps);
            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> $jsonContent[0]->dateTime->format("U")) {
                $dataEntry->setDateTime($jsonContent[0]->dateTime);
            }

            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            $awardManager->checkForAwards($dataEntry, "goal");
            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntry)), $patient, $thirdPartyService, $dataEntry->getDateTime());
            $challengePve->checkAnyRunning($dataEntry);

            return $dataEntry;

        }

        return NULL;
    }
}
