<?php

namespace App\Transform\SamsungHealth;

use App\AppConstants;
use App\Entity\Exercise;
use App\Entity\ExerciseSummary;
use App\Entity\ExerciseTrack;
use App\Entity\ExerciseType;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Service\AwardManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Array_;

class SamsungExercise extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return Exercise|null
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {
            ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - New call too Exercise for " . $jsonContent->remoteId);

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

            /** @var PartOfDay $partOfDay */
            $partOfDay = self::getPartOfDay($doctrine, new \DateTime($jsonContent->dateTime));
            if (is_null($partOfDay)) {
                return NULL;
            }

            /** @var ExerciseType $exerciseType */
            $exerciseType = self::getExerciseType($doctrine, self::convertExerciseType($jsonContent->exerciseType));
            if (is_null($exerciseType)) {
                return NULL;
            }

            /** @var Exercise $dataEntryExercise */
            $dataEntryExercise = $doctrine->getRepository(Exercise::class)->findOneBy(['RemoteId' => $jsonContent->remoteId, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntryExercise) {
                $dataEntryExercise = new Exercise();
            }

            $dataEntryExercise->setPatient($patient);
            $dataEntryExercise->setTrackingDevice($deviceTracking);
            $dataEntryExercise->setPartOfDay($partOfDay);
            $dataEntryExercise->setRemoteId($jsonContent->remoteId);

            if (is_null($dataEntryExercise->getDateTimeStart()) || $dataEntryExercise->getDateTimeStart()->format("U") <> (new \DateTime($jsonContent->dateTime))->format("U")) {
                $dataEntryExercise->setDateTimeStart(new \DateTime($jsonContent->dateTime));
            }
            if (is_null($dataEntryExercise->getDateTimeEnd()) || $dataEntryExercise->getDateTimeEnd()->format("U") <> (new \DateTime($jsonContent->dateTimeEnd))->format("U")) {
                $dataEntryExercise->setDateTimeEnd(new \DateTime($jsonContent->dateTimeEnd));
            }
            $dataEntryExercise->setDuration($dataEntryExercise->getDateTimeEnd()->format("U") - $dataEntryExercise->getDateTimeStart()->format("U"));
            $dataEntryExercise->setExerciseType($exerciseType);

            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntryExercise->getDateTimeEnd()->format("U")) {
                $deviceTracking->setLastSynced($dataEntryExercise->getDateTimeEnd());
            }

            $dataEntryExerciseSummary = $dataEntryExercise->getExerciseSummary();
            if (is_null($dataEntryExerciseSummary)) {
                $dataEntryExerciseSummary = new ExerciseSummary();
            }

            if (property_exists($jsonContent, "altitudeGain") && $jsonContent->altitudeGain > 0) $dataEntryExerciseSummary->setAltitudeGain($jsonContent->altitudeGain);
            if (property_exists($jsonContent, "altitudeLoss") && $jsonContent->altitudeLoss > 0) $dataEntryExerciseSummary->setAltitudeLoss($jsonContent->altitudeLoss);
            if (property_exists($jsonContent, "altitudeMax") && $jsonContent->altitudeMax > 0) $dataEntryExerciseSummary->setAltitudeMax($jsonContent->altitudeMax);
            if (property_exists($jsonContent, "altitudeMin") && $jsonContent->altitudeMin > 0) $dataEntryExerciseSummary->setAltitudeMin($jsonContent->altitudeMin);
            if (property_exists($jsonContent, "cadenceMax") && $jsonContent->cadenceMax > 0) $dataEntryExerciseSummary->setCadenceMax($jsonContent->cadenceMax);
            if (property_exists($jsonContent, "cadenceMean") && $jsonContent->cadenceMean > 0) $dataEntryExerciseSummary->setCadenceMean($jsonContent->cadenceMean);
            if (property_exists($jsonContent, "cadenceMin") && $jsonContent->cadenceMin > 0) $dataEntryExerciseSummary->setCadenceMin($jsonContent->cadenceMin);
            if (property_exists($jsonContent, "calorie") && $jsonContent->calorie > 0) $dataEntryExerciseSummary->setCalorie($jsonContent->calorie);
            if (property_exists($jsonContent, "declineDistance") && $jsonContent->declineDistance > 0) $dataEntryExerciseSummary->setDistanceDecline($jsonContent->declineDistance);
            if (property_exists($jsonContent, "distance") && $jsonContent->distance > 0) $dataEntryExerciseSummary->setDistance($jsonContent->distance);
            if (property_exists($jsonContent, "heartRateMax") && $jsonContent->heartRateMax > 0) $dataEntryExerciseSummary->setHeartRateMax($jsonContent->heartRateMax);
            if (property_exists($jsonContent, "heartRateMean") && $jsonContent->heartRateMean > 0) $dataEntryExerciseSummary->setHeartRateMean($jsonContent->heartRateMean);
            if (property_exists($jsonContent, "heartRateMin") && $jsonContent->heartRateMin > 0) $dataEntryExerciseSummary->setHeartRateMin($jsonContent->heartRateMin);
            if (property_exists($jsonContent, "inclineDistance") && $jsonContent->inclineDistance > 0) $dataEntryExerciseSummary->setDistanceIncline($jsonContent->inclineDistance);
            if (property_exists($jsonContent, "speedMax") && $jsonContent->speedMax > 0) $dataEntryExerciseSummary->setSpeedMax($jsonContent->speedMax);
            if (property_exists($jsonContent, "speedMean") && $jsonContent->speedMean > 0) $dataEntryExerciseSummary->setSpeedMean($jsonContent->speedMean);

            $dataEntryExercise->setExerciseSummary($dataEntryExerciseSummary);

            if (property_exists($jsonContent, "locationData")) {
                /** @var array $jsonLocationData */
                $jsonLocationData = self::decodeJson($jsonContent->locationData);
                //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonLocationData, TRUE));

                /** @var object $jsonLocationDatum */
                foreach ($jsonLocationData as $jsonLocationDatum) {
                    $dataEntryExerciseTrack = new ExerciseTrack();
                    $dataEntryExerciseTrack->setTimeStamp($jsonLocationDatum->start_time);
                    if (property_exists($jsonLocationDatum, "latitude")) $dataEntryExerciseTrack->setLatitude($jsonLocationDatum->latitude);
                    if (property_exists($jsonLocationDatum, "longitude")) $dataEntryExerciseTrack->setLongitude($jsonLocationDatum->longitude);
                    if (property_exists($jsonLocationDatum, "altitude")) $dataEntryExerciseTrack->setAltitude($jsonLocationDatum->altitude);

                    $dataEntryExercise->addExerciseTrack($dataEntryExerciseTrack);
                }

            }

            try {
                $savedClassType = get_class($dataEntryExercise);
                $savedClassType = str_ireplace("App\\Entity\\", "", $savedClassType);
                $updatedApi = self::updateApi($doctrine, $savedClassType, $patient, $thirdPartyService, $dataEntryExercise->getDateTimeStart());

                $entityManager = $doctrine->getManager();
                $entityManager->persist($updatedApi);
                $entityManager->flush();
            } catch (\Exception $e) {
                ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $e->getMessage());
            }

            return $dataEntryExercise;

        }

        return NULL;
    }
}