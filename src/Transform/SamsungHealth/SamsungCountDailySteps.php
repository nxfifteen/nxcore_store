<?php

namespace App\Transform\SamsungHealth;

use App\AppConstants;
use App\Entity\FitStepsDailySummary;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\RpgXP;
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

        if (property_exists($jsonContent, "uuid") && $jsonContent->device == 'VfS0qUERdZ') {
            //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

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
            $patientGoal = self::getPatientGoal($doctrine, "FitStepsDailySummary", $jsonContent->goal, NULL, $patient, false);
            if (is_null($patientGoal)) {
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
            $dataEntry->setGoal($patientGoal);

            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") < strtotime($jsonContent->dateTime)) {
                $dataEntry->setDateTime(new \DateTime($jsonContent->dateTime));
            }

            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            if ($dataEntry->getTrackingDevice()->getId() == 3) {
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