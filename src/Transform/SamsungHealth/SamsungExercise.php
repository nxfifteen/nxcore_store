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
use App\Entity\Exercise;
use App\Entity\ExerciseSummary;
use App\Entity\ExerciseType;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\SiteNews;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Service\AwardManager;
use App\Service\CommsManager;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class SamsungExercise
 *
 * @package App\Transform\SamsungHealth
 */
class SamsungExercise extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @param CommsManager    $commsManager
     *
     * @return Exercise|null
     * @throws Exception
     */
    public static function translate(
        ManagerRegistry $doctrine,
        string $getContent,
        AwardManager $awardManager,
        CommsManager $commsManager
    ) {
        $jsonContent = self::decodeJson($getContent);
        // AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {
            try {
                $jsonContent->dateTime = new DateTime($jsonContent->dateTime);
                $jsonContent->dateTimeEnd = new DateTime($jsonContent->dateTimeEnd);
                $jsonContent->dateTimeOffset = new DateTime($jsonContent->dateTimeOffset);
                $jsonContent->dateRaw = $jsonContent->dateTime;
            } catch (Exception $e) {
                return null;
            }

            $timeDiff = $jsonContent->dateTimeOffset->format("G");
            if ($timeDiff > 0) {
                $jsonContent->dateTime->modify('+ ' . $timeDiff . ' hour');
                $jsonContent->dateTimeEnd->modify('+ ' . $timeDiff . ' hour');
            } else {
                if ($timeDiff < 0) {
                    $jsonContent->dateTime->modify('- ' . $timeDiff . ' hour');
                    $jsonContent->dateTimeEnd->modify('- ' . $timeDiff . ' hour');
                }
            }

            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent->uuid);
            if (is_null($patient)) {
                return null;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::SAMSUNGHEALTHSERVICE);
            if (is_null($thirdPartyService)) {
                return null;
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $jsonContent->device);
            if (is_null($deviceTracking)) {
                return null;
            }
            if ($deviceTracking->getId() == 7) {
                return null;
            }
//            AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - device " . $deviceTracking->getName());

            /** @var PartOfDay $partOfDay */
            $partOfDay = self::getPartOfDay($doctrine, $jsonContent->dateTime);
            if (is_null($partOfDay)) {
                return null;
            }

            /** @var ExerciseType $exerciseType */
            $exerciseType = self::getExerciseType($doctrine, self::convertExerciseType($jsonContent->exerciseType));
            if (is_null($exerciseType)) {
                return null;
            }
//            AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - exerciseType " . $exerciseType->getName());

            $newItem = false;
            /** @var Exercise $dataEntryExercise */
            $dataEntryExercise = $doctrine->getRepository(Exercise::class)->findOneBy([
                'RemoteId' => $jsonContent->remoteId,
                'trackingDevice' => $deviceTracking,
            ]);
            if (!$dataEntryExercise) {
                $newItem = true;
                $dataEntryExercise = new Exercise();
                $safeGuid = false;
                $i = 0;
                do {
                    $i++;
                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': Added a GUID (' . $i . ')');
                    $dataEntryExercise->createGuid(true);
                    $dataEntryGuidCheck = $doctrine
                        ->getRepository(Exercise::class)
                        ->findByGuid($dataEntryExercise->getGuid());
                    if (empty($dataEntryGuidCheck)) {
                        $safeGuid = true;
                    }

                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': ' . gettype($dataEntryGuidCheck));
                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': ' . count($dataEntryGuidCheck));
                } while (!$safeGuid);
            }

            $dataEntryExercise->setPatient($patient);
            $dataEntryExercise->setTrackingDevice($deviceTracking);
            $dataEntryExercise->setPartOfDay($partOfDay);
            $dataEntryExercise->setRemoteId($jsonContent->remoteId);

            if (is_null($dataEntryExercise->getDateTimeStart()) || $dataEntryExercise->getDateTimeStart()->format("U") <> $jsonContent->dateTime->format("U")) {
                $dataEntryExercise->setDateTimeStart($jsonContent->dateTime);
            }
            if (is_null($dataEntryExercise->getDateTimeEnd()) || $dataEntryExercise->getDateTimeEnd()->format("U") <> $jsonContent->dateTimeEnd->format("U")) {
                $dataEntryExercise->setDateTimeEnd($jsonContent->dateTimeEnd);
            }
            $dataEntryExercise->setDuration($dataEntryExercise->getDateTimeEnd()->format("U") - $dataEntryExercise->getDateTimeStart()->format("U"));
            $dataEntryExercise->setExerciseType($exerciseType);

            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntryExercise->getDateTimeEnd()->format("U")) {
                $deviceTracking->setLastSynced($dataEntryExercise->getDateTimeEnd());
            }

            if (property_exists($jsonContent, "liveData")) {
                $dataEntryExercise->setLiveDataBlob(AppConstants::compressString($jsonContent->liveData));
            }
            if (property_exists($jsonContent, "locationData")) {
                $dataEntryExercise->setLocationDataBlob(AppConstants::compressString($jsonContent->locationData));
            }

            $dataEntryExerciseSummary = $dataEntryExercise->getExerciseSummary();
            if (is_null($dataEntryExerciseSummary)) {
                $dataEntryExerciseSummary = new ExerciseSummary();
            }

            if (property_exists($jsonContent, "altitudeGain") && $jsonContent->altitudeGain > 0) {
                $dataEntryExerciseSummary->setAltitudeGain($jsonContent->altitudeGain);
            }
            if (property_exists($jsonContent, "altitudeLoss") && $jsonContent->altitudeLoss > 0) {
                $dataEntryExerciseSummary->setAltitudeLoss($jsonContent->altitudeLoss);
            }
            if (property_exists($jsonContent, "altitudeMax") && $jsonContent->altitudeMax > 0) {
                $dataEntryExerciseSummary->setAltitudeMax($jsonContent->altitudeMax);
            }
            if (property_exists($jsonContent, "altitudeMin") && $jsonContent->altitudeMin > 0) {
                $dataEntryExerciseSummary->setAltitudeMin($jsonContent->altitudeMin);
            }
            if (property_exists($jsonContent, "cadenceMax") && $jsonContent->cadenceMax > 0) {
                $dataEntryExerciseSummary->setCadenceMax($jsonContent->cadenceMax);
            }
            if (property_exists($jsonContent, "cadenceMean") && $jsonContent->cadenceMean > 0) {
                $dataEntryExerciseSummary->setCadenceMean($jsonContent->cadenceMean);
            }
            if (property_exists($jsonContent, "cadenceMin") && $jsonContent->cadenceMin > 0) {
                $dataEntryExerciseSummary->setCadenceMin($jsonContent->cadenceMin);
            }
            if (property_exists($jsonContent, "calorie") && $jsonContent->calorie > 0) {
                $dataEntryExerciseSummary->setCalorie($jsonContent->calorie);
            }
            if (property_exists($jsonContent, "declineDistance") && $jsonContent->declineDistance > 0) {
                $dataEntryExerciseSummary->setDistanceDecline($jsonContent->declineDistance);
            }
            if (property_exists($jsonContent, "distance") && $jsonContent->distance > 0) {
                $dataEntryExerciseSummary->setDistance($jsonContent->distance);
            }
            if (property_exists($jsonContent, "heartRateMax") && $jsonContent->heartRateMax > 0) {
                $dataEntryExerciseSummary->setHeartRateMax($jsonContent->heartRateMax);
            }
            if (property_exists($jsonContent, "heartRateMean") && $jsonContent->heartRateMean > 0) {
                $dataEntryExerciseSummary->setHeartRateMean($jsonContent->heartRateMean);
            }
            if (property_exists($jsonContent, "heartRateMin") && $jsonContent->heartRateMin > 0) {
                $dataEntryExerciseSummary->setHeartRateMin($jsonContent->heartRateMin);
            }
            if (property_exists($jsonContent, "inclineDistance") && $jsonContent->inclineDistance > 0) {
                $dataEntryExerciseSummary->setDistanceIncline($jsonContent->inclineDistance);
            }
            if (property_exists($jsonContent, "speedMax") && $jsonContent->speedMax > 0) {
                $dataEntryExerciseSummary->setSpeedMax($jsonContent->speedMax);
            }
            if (property_exists($jsonContent, "speedMean") && $jsonContent->speedMean > 0) {
                $dataEntryExerciseSummary->setSpeedMean($jsonContent->speedMean);
            }

            $dataEntryExercise->setExerciseSummary($dataEntryExerciseSummary);

            if ($newItem) {
                $commsManager->sendNotification(
                    "@" . $patient->getUuid() . " just #recorded a new " . round($dataEntryExercise->getDuration() / 60,
                        0) . " minute #" . strtolower($dataEntryExercise->getExerciseType()->getTag()) . " :dog_14:",
                    null,
                    $patient,
                    false
                );

                $notification = new SiteNews();
                $notification->setPatient($patient);
                $notification->setPublished(new DateTime());
                $notification->setTitle("New Exercise Recorded");
                $notification->setText("You've just recorded a new " . strtolower($dataEntryExercise->getExerciseType()->getTag()));
                $notification->setAccent('success');
                $notification->setImage("recorded_exercise");
                $notification->setExpires(new DateTime(date("Y-m-d 23:59:59")));
                $notification->setLink('/activities/log');
                $notification->setPriority(3);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($notification);
                $entityManager->flush();
            }

            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntryExercise)), $patient,
                $thirdPartyService, $dataEntryExercise->getDateTimeStart());

            if ($newItem) {
                $awardManager->checkForAwards($dataEntryExercise, "exercise");
            }

            return $dataEntryExercise;

        }

        return null;
    }
}
