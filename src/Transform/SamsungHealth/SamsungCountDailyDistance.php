<?php

namespace App\Transform\SamsungHealth;

use App\AppConstants;
use App\Entity\FitDistanceDailySummary;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use App\Service\AwardManager;
use Doctrine\Common\Persistence\ManagerRegistry;

class SamsungCountDailyDistance extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return FitDistanceDailySummary|null
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
//        AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid") && $jsonContent->device == 'VfS0qUERdZ') {
            ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - New call too FitDistanceDailySummary for " . $jsonContent->remoteId);

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
            $patientGoal = self::getPatientGoal($doctrine, "FitDistanceDailySummary", $jsonContent->goal, NULL, $patient);
            if (is_null($patientGoal)) {
                return NULL;
            }

            /** @var UnitOfMeasurement $unitOfMeasurement */
            $unitOfMeasurement = self::getUnitOfMeasurement($doctrine, $jsonContent->units);
            if (is_null($unitOfMeasurement)) {
                return NULL;
            }

            /** @var FitDistanceDailySummary $dataEntry */
            $dataEntry = $doctrine->getRepository(FitDistanceDailySummary::class)->findOneBy(['RemoteId' => $jsonContent->remoteId, 'patient' => $patient, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntry) {
                $dataEntry = new FitDistanceDailySummary();
            }

            $dataEntry->setPatient($patient);
            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent->remoteId);
            $dataEntry->setValue($jsonContent->value);
            $dataEntry->setGoal($patientGoal);
            $dataEntry->setUnitOfMeasurement($unitOfMeasurement);
            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> (new \DateTime($jsonContent->dateTime))->format("U")) {
                $dataEntry->setDateTime(new \DateTime($jsonContent->dateTime));
            }

            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            if ($dataEntry->getTrackingDevice()->getId() == 3) {
                if ($dataEntry->getValue() >= $dataEntry->getGoal()->getGoal()) {
                    $patient = $awardManager->giveBadge(
                        $patient,
                        [
                            'patients_name' => $patient->getFirstName(),
                            'html_title' => "Awarded the Distance Target badge",
                            'header_image' => '../badges/trg_distance_achieved_header.png',
                            "dateTime" => $dataEntry->getDateTime(),
                            'relevant_date' => $dataEntry->getDateTime()->format("F jS, Y"),
                            "name" => "Distance Target Achieved",
                            "repeat" => FALSE,
                            'badge_name' => 'Distance Target Achieved',
                            'badge_xp' => 5,
                            'badge_image' => 'trg_distance_achieved',
                            'badge_text' => "Reached your distance goal today",
                            'badge_longtext' => "Today you did it! Walked the full way",
                            'badge_citation' => "Today you did it! Walked the full way",
                        ]
                    );
                }
            }

            try {
                $savedClassType = get_class($dataEntry);
                $savedClassType = str_ireplace("App\\Entity\\", "", $savedClassType);
                $updatedApi = self::updateApi($doctrine, $savedClassType, $patient, $thirdPartyService, $dataEntry->getDateTime());

                $entityManager = $doctrine->getManager();
                $entityManager->persist($updatedApi);
                $entityManager->flush();
            } catch (\Exception $e) {
                ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $e->getMessage());
            }

            return $dataEntry;

        }

        return NULL;
    }
}