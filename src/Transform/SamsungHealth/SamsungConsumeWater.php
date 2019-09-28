<?php

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
use Doctrine\Common\Persistence\ManagerRegistry;

class SamsungConsumeWater extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return ConsumeWater|null
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {
            ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - New call too ConsumeWater for " . $jsonContent->remoteId);

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
            $partOfDay = self::getPartOfDay($doctrine, new \DateTime($jsonContent->dateTime));
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
            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> (new \DateTime($jsonContent->dateTime))->format("U")) {
                $dataEntry->setDateTime(new \DateTime($jsonContent->dateTime));
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