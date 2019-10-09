<?php

namespace App\Transform\Fitbit;

use App\Entity\BodyFat;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use App\Service\AwardManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

class FitbitBodyFat extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param                 $jsonContent
     *
     * @param AwardManager    $awardManager
     *
     * @return BodyFat|null
     * @throws Exception
     */
    public static function translate(ManagerRegistry $doctrine, $jsonContent, AwardManager $awardManager)
    {
        if (property_exists($jsonContent[0], "uuid") && $jsonContent[2]->body->fat > 0) {
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

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, self::FITBITSERVICE);
            if (is_null($deviceTracking)) {
                return NULL;
            }

            /** @var PartOfDay $partOfDay */
            $partOfDay = self::getPartOfDay($doctrine, new \DateTime($jsonContent[0]->dateTime));
            if (is_null($partOfDay)) {
                return NULL;
            }

            /** @var UnitOfMeasurement $unitOfMeasurement */
            $unitOfMeasurement = self::getUnitOfMeasurement($doctrine, "%");
            if (is_null($unitOfMeasurement)) {
                return NULL;
            }

            /** @var PatientGoals $patientGoal */
            $patientGoal = self::getPatientGoal($doctrine, "BodyFat", $jsonContent[2]->goals->fat, $unitOfMeasurement, $patient);
            if (is_null($patientGoal)) {
                return NULL;
            }

            $jsonContent[0]->remoteId = $jsonContent[0]->remoteId . 'FitbitBodyFat' . (new \DateTime($jsonContent[0]->dateTime))->format("Y-m-d");

            /** @var BodyFat $dataEntry */
            $dataEntry = $doctrine->getRepository(BodyFat::class)->findOneBy(['RemoteId' => $jsonContent[0]->remoteId, 'patient' => $patient, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntry) {
                $dataEntry = new BodyFat();
            }

            $dataEntry->setPatient($patient);

            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent[0]->remoteId);
            $dataEntry->setMeasurement($jsonContent[2]->body->fat);
            $dataEntry->setUnitOfMeasurement($unitOfMeasurement);
            $dataEntry->setPatientGoal($patientGoal);
            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> (new \DateTime($jsonContent[0]->dateTime))->format("U")) {
                $dataEntry->setDateTime(new \DateTime($jsonContent[0]->dateTime));
            }
            $dataEntry->setPartOfDay($partOfDay);
            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            try {
                $savedClassType = get_class($dataEntry);
                $savedClassType = str_ireplace("App\\Entity\\", "", $savedClassType);
                $updatedApi = self::updateApi($doctrine, $savedClassType, $patient, $thirdPartyService, $dataEntry->getDateTime());

                $entityManager = $doctrine->getManager();
                $entityManager->persist($updatedApi);
                $entityManager->flush();
            } catch (Exception $e) {
                ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $e->getMessage());
            }

            return $dataEntry;

        }

        return NULL;
    }
}