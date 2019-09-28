<?php

namespace App\Transform\Fitbit;

use App\AppConstants;
use App\Entity\FitStepsDailySummary;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Service\AwardManager;
use Doctrine\Common\Persistence\ManagerRegistry;

class FitbitCountDailySteps extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param                 $jsonContent
     *
     * @param AwardManager    $awardManager
     *
     * @return FitStepsDailySummary|FitStepsDailySummary[]|null
     */
    public static function translate(ManagerRegistry $doctrine, $jsonContent, AwardManager $awardManager)
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
                $patient = $awardManager->giveBadge(
                    $patient,
                    [
                        'patients_name' => $patient->getFirstName(),
                        'html_title' => "Awarded the Step Target badge",
                        'header_image' => '../badges/trg_steps_achieved_header.png',
                        "dateTime" => $dataEntry->getDateTime(),
                        'relevant_date' => $dataEntry->getDateTime()->format("F jS, Y"),
                        "name" => "Step Target",
                        "repeat" => FALSE,
                        'badge_name' => 'Step Target',
                        'badge_xp' => 5,
                        'badge_image' => 'trg_steps_achieved',
                        'badge_text' => "Reached your step goal today",
                        'badge_longtext' => "Today you did it! You reached your step goal",
                        'badge_citation' => "Today you did it! You reached your step goal",
                    ]
                );
                $percentageOver = ($dataEntry->getValue() / $dataEntry->getGoal()->getGoal()) * 100;
                $percentageOver = $percentageOver - 100;
                if ($percentageOver > 100) {
                    $patient = $awardManager->giveBadge(
                        $patient,
                        [
                            'patients_name' => $patient->getFirstName(),
                            'html_title' => "Awarded the Step Target Smashed badge",
                            'header_image' => '../badges/trg_steps_smashed_header.png',
                            "dateTime" => $dataEntry->getDateTime(),
                            'relevant_date' => $dataEntry->getDateTime()->format("F jS, Y"),
                            "name" => "Step Target Smashed",
                            "repeat" => FALSE,
                            'badge_name' => 'Step Target Smashed',
                            'badge_xp' => 10,
                            'badge_image' => 'trg_steps_smashed',
                            'badge_text' => "You walked twice your step goal",
                            'badge_longtext' => "Wow! I mean, WOW! You walked twice your step goal today",
                            'badge_citation' => "Wow! I mean, WOW! You walked twice your step goal today",
                        ]
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