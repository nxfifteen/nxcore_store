<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2019. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Transform\Fitbit;

use App\AppConstants;
use App\Entity\FitStepsDailySummary;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use Doctrine\Common\Persistence\ManagerRegistry;

class FitbitCountPeriodSteps extends Constants
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
                /** @var PatientGoals $patientGoal */
                $patientGoal = self::getPatientGoal($doctrine, "FitStepsDailySummary", 10000, NULL, $patient, FALSE);
                if (is_null($patientGoal)) {
                    return NULL;
                }
            }

            $returnEntities = [];

            $entityManager = $doctrine->getManager();
            foreach ($jsonContent[2]->{'activities-steps'} as $item) {
                if ($item->dateTime != date("Y-m-d")) {
                    $item->remoteId = $jsonContent[0]->remoteId . 'FitStepsDailySummary' . $item->dateTime;

                    /** @var FitStepsDailySummary $dataEntry */
                    $dataEntry = $doctrine->getRepository(FitStepsDailySummary::class)->findOneBy([
                        'RemoteId' => $item->remoteId,
                        'patient' => $patient,
                        'trackingDevice' => $deviceTracking,
                    ]);
                    if (!$dataEntry) {
                        $dataEntry = new FitStepsDailySummary();
                        $dataEntry->setGoal($patientGoal);
                    }

                    $dataEntry->setPatient($patient);
                    $dataEntry->setTrackingDevice($deviceTracking);
                    $dataEntry->setRemoteId($item->remoteId);
                    $dataEntry->setValue($item->value);
                    if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> strtotime($item->dateTime . " 23:59:59")) {
                        $dataEntry->setDateTime(new \DateTime($item->dateTime . " 23:59:59"));
                    }

                    if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < strtotime($item->dateTime . " 23:59:59")) {
                        $deviceTracking->setLastSynced($dataEntry->getDateTime());
                    }

                    if ($dataEntry->getValue() >= $dataEntry->getGoal()->getGoal()) {
                        $patient = self::awardPatientReward(
                            $doctrine,
                            $patient,
                            $dataEntry->getDateTime(),
                            "Step Target Achieved",
                            5,
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
                                10,
                                "trg_steps_smashed",
                                "You walked twice your step goal",
                                "Wow! I mean, WOW! You walked twice your step goal today"
                            );
                        }
                    }

                    try {
                        $savedClassType = get_class($dataEntry);
                        $savedClassType = str_ireplace("App\\Entity\\", "", $savedClassType);
                        $updatedApi = self::updateApi($doctrine, $savedClassType, $patient, $thirdPartyService, $dataEntry->getDateTime());

                        $entityManager->persist($updatedApi);
                    } catch (\Exception $e) {
                        ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $e->getMessage());
                    }

                    $returnEntities[] = $dataEntry;
                }
            }


            $entityManager->flush();

            return $returnEntities;

        }

        return NULL;
    }
}