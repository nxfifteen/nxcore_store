<?php

namespace App\Transform\Fitbit;

use App\Entity\FitStepsDailySummary;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use Doctrine\Common\Persistence\ManagerRegistry;

class FitbitCountDailySteps extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param                 $jsonContent
     *
     * @return FitStepsDailySummary|FitStepsDailySummary[]|null
     */
    public static function translate(ManagerRegistry $doctrine, $jsonContent)
    {
        if (property_exists($jsonContent[0], "uuid")) {
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
            $jsonContent[0]->dateTime = new \DateTime();
            foreach ($jsonContent[1] as $trackingDevice) {
                if ($trackingDevice->type == "TRACKER") {
                    $trackingDeviceRemoteId = $trackingDevice->id;
                    $jsonContent[0]->dateTime = new \DateTime($trackingDevice->lastSyncTime);
                }
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $trackingDeviceRemoteId);
            if (is_null($deviceTracking)) {
                return NULL;
            }

            if (count($jsonContent) >= 2 && is_object($jsonContent[2]) && property_exists($jsonContent[2], "goals") && property_exists($jsonContent[2]->goals, "steps")) {
                /** @var PatientGoals $patientGoal */
                $patientGoal = self::getPatientGoal($doctrine, "FitStepsDailySummary", $jsonContent[2]->goals->steps, NULL, $patient, FALSE);
                if (is_null($patientGoal)) {
                    return NULL;
                }
            } else {
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

            if ($dataEntry->getValue() >= $dataEntry->getGoal()->getGoal()) {
                $patient = self::awardPatientReward(
                    $doctrine,
                    $patient,
                    $dataEntry->getDateTime(),
                    "Step Target Achieved",
                    0.143,
                    "trg_steps_achieved",
                    "Reached your step goal today",
                    "Today you did it! You reached your step goal"
                );
                $percentageOver = ($dataEntry->getValue() / $dataEntry->getGoal()->getGoal()) * 100;
                $percentageOver = $percentageOver - 100;
                if ($percentageOver > 100) {
                    $patient = self::awardPatientReward(
                        $doctrine,
                        $patient,
                        $dataEntry->getDateTime(),
                        "Step Target Smashed",
                        0.143,
                        "trg_steps_smashed",
                        "You walked twice your step goal",
                        "Wow! I mean, WOW! You walked twice your step goal today"
                    );
                }
            }

            $entityManager = $doctrine->getManager();
            try {
                $savedClassType = get_class($dataEntry);
                $savedClassType = str_ireplace("App\\Entity\\", "", $savedClassType);
                $updatedApi = self::updateApi($doctrine, $savedClassType, $patient, $thirdPartyService, $dataEntry->getDateTime());

                $entityManager->persist($updatedApi);
            } catch (\Exception $e) {
                ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $e->getMessage());
            }

            $entityManager->flush();

            return $dataEntry;

        }

        return NULL;
    }
}