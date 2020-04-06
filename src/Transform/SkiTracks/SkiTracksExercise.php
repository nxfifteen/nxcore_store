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

namespace App\Transform\SkiTracks;


use App\AppConstants;
use App\Entity\Exercise;
use App\Entity\ExerciseSummary;
use App\Entity\ExerciseType;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Service\AwardManager;
use App\Service\TweetManager;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class SkiTracksExercise
 *
 * @package App\Transform\SkiTracks
 */
class SkiTracksExercise extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @param TweetManager    $tweetManager
     *
     * @param Patient         $patient
     *
     * @return Exercise|null
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent, AwardManager $awardManager, TweetManager $tweetManager, Patient $patient)
    {
        $jsonContent = json_decode($getContent, FALSE);
//        AppConstants::writeToLog('debug_transform.txt', print_r($jsonContent, true));

        $remoteId = sha1(json_encode($jsonContent));
        $skiRun = json_decode($jsonContent->skiRun, TRUE);
        $tracksFile = json_decode($jsonContent->tracksFile, TRUE);
        $skiRunAttributes = $tracksFile['@attributes'];
//        $skiRunMetrics = $tracksFile['metrics'];
        $gps = $jsonContent->gps;
        $totalDistance = $jsonContent->totalDistance;

//        AppConstants::writeToLog('debug_transform.txt', $remoteId);
//        AppConstants::writeToLog('debug_transform.txt', print_r($skiRun, true));
//        AppConstants::writeToLog('debug_transform.txt', print_r($totalDistance, true));
//        AppConstants::writeToLog('debug_transform.txt', print_r($gps, true));

        try {
            $skiRun['START_ZONE'] = new \DateTime($skiRun['START_ZONE']);
            $skiRun['END_ZONE'] = new \DateTime($skiRun['END_ZONE']);
        } catch (\Exception $e) {
            return NULL;
        }

        /** @var ThirdPartyService $thirdPartyService */
        $thirdPartyService = self::getThirdPartyService($doctrine, self::SKITRACKSSERVICE);
        if (is_null($thirdPartyService)) {
            return NULL;
        }

        $syncDevice = explode("/", $skiRunAttributes['device']);
        /** @var TrackingDevice $deviceTracking */
        $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $skiRunAttributes['device'], [
            "name" => "SkiTracks",
            "type" => "app",
            "manufacturer" => "CoreCoders",
            "model" => $syncDevice[2] . " for " . $syncDevice[1],
        ]);
        if (is_null($deviceTracking)) {
            return NULL;
        }

        /** @var PartOfDay $partOfDay */
        $partOfDay = self::getPartOfDay($doctrine, $skiRun['START_ZONE']);
        if (is_null($partOfDay)) {
            return NULL;
        }

        /** @var ExerciseType $exerciseType */
        $exerciseType = self::getExerciseType($doctrine, self::convertExerciseType($skiRun['ACTIVITY']));
        if (is_null($exerciseType)) {
            return NULL;
        }

        $newItem = FALSE;
        /** @var Exercise $dataEntryExercise */
        $dataEntryExercise = $doctrine->getRepository(Exercise::class)->findOneBy(['RemoteId' => $remoteId, 'trackingDevice' => $deviceTracking]);
        if (!$dataEntryExercise) {
            $dataEntryExercise = new Exercise();
            $newItem = TRUE;
        }

        if ($newItem) {
            AppConstants::writeToLog('debug_transform.txt', "New exercise created");
        } else {
            AppConstants::writeToLog('debug_transform.txt', "Exercise Updated");
        }

        $dataEntryExercise->setPatient($patient);
        $dataEntryExercise->setTrackingDevice($deviceTracking);
        $dataEntryExercise->setPartOfDay($partOfDay);
        $dataEntryExercise->setRemoteId($remoteId);

        if (is_null($dataEntryExercise->getDateTimeStart()) || $dataEntryExercise->getDateTimeStart()->format("U") <> $skiRun['START_ZONE']->format("U")) {
            $dataEntryExercise->setDateTimeStart($skiRun['START_ZONE']);
        }
        if (is_null($dataEntryExercise->getDateTimeEnd()) || $dataEntryExercise->getDateTimeEnd()->format("U") <> $skiRun['END_ZONE']->format("U")) {
            $dataEntryExercise->setDateTimeEnd($skiRun['END_ZONE']);
        }
        $dataEntryExercise->setDuration($dataEntryExercise->getDateTimeEnd()->format("U") - $dataEntryExercise->getDateTimeStart()->format("U"));
        $dataEntryExercise->setExerciseType($exerciseType);
        $dataEntryExercise->setSteps(0);

        if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntryExercise->getDateTimeEnd()->format("U")) {
            $deviceTracking->setLastSynced($dataEntryExercise->getDateTimeEnd());
        }

        $locationData = [];
        $liveData = [];
        $speedData = [];
        $altitudeStart = NULL;
        $altitudeFinish = NULL;
        $altitudeMax = NULL;
        $altitudeMin = NULL;

        foreach ($gps as $trackPoint) {
            $pointRef = round($trackPoint->UTC, 0, PHP_ROUND_HALF_UP);

            $locationDataItem = [];
            $liveDataItem = [];

            if (property_exists($trackPoint, "DISTANCE") && $trackPoint->DISTANCE > 0) {
                if (!array_key_exists("start_time", $liveDataItem)) $liveDataItem["start_time"] = $pointRef;
                $liveDataItem["distance"] = $trackPoint->DISTANCE;
            }

            /*if (property_exists($trackPoint, "HeartRate")) {
                if (!array_key_exists("start_time", $liveDataItem)) $liveDataItem["start_time"] = $pointRef;
                $liveDataItem["heart_rate"] = $trackPoint->HeartRate;
            }*/

            if (property_exists($trackPoint, "VELOCITY") && $trackPoint->VELOCITY > 0) {
                if (!array_key_exists("start_time", $liveDataItem)) $liveDataItem["start_time"] = $pointRef;
                $liveDataItem["speed"] = $trackPoint->VELOCITY;
                $speedData[] = $trackPoint->VELOCITY;
            }

            if (property_exists($trackPoint, "LAT")) {
                if (!array_key_exists("start_time", $locationDataItem)) $locationDataItem["start_time"] = $pointRef;
                $locationDataItem['latitude'] = $trackPoint->LAT;
            }
            if (property_exists($trackPoint, "LON")) {
                if (!array_key_exists("start_time", $locationDataItem)) $locationDataItem["start_time"] = $pointRef;
                $locationDataItem['longitude'] = $trackPoint->LON;
            }
            if (property_exists($trackPoint, "ALT")) {
                if (!array_key_exists("start_time", $locationDataItem)) $locationDataItem["start_time"] = $pointRef;
                $locationDataItem['altitude'] = $trackPoint->ALT;
                if (is_null($altitudeStart)) {
                    $altitudeStart = $trackPoint->ALT;
                }
                $altitudeFinish = $trackPoint->ALT;

                if (is_null($altitudeMin) || $trackPoint->ALT < $altitudeMin) {
                    $altitudeMin = $trackPoint->ALT;
                }

                if (is_null($altitudeMax) || $trackPoint->ALT > $altitudeMax) {
                    $altitudeMax = $trackPoint->ALT;
                }
            }

            if (count($locationDataItem) > 1) {
                $locationData[] = $locationDataItem;
            }

            if (count($liveDataItem) > 1) {
                $liveData[] = $liveDataItem;
            }
        }

        if (count($locationData) > 1) {
            $dataEntryExercise->setLocationDataBlob(AppConstants::compressString(json_encode($locationData)));
        }

        if (count($liveData) > 1) {
            $dataEntryExercise->setLiveDataBlob(AppConstants::compressString(json_encode($liveData)));
        }

        AppConstants::writeToLog('debug_transform.txt', $altitudeMin);
        AppConstants::writeToLog('debug_transform.txt', $altitudeMax);

        $dataEntryExerciseSummary = $dataEntryExercise->getExerciseSummary();
        if (is_null($dataEntryExerciseSummary)) {
            $dataEntryExerciseSummary = new ExerciseSummary();
        }

        if ($altitudeStart > $altitudeFinish) {
            $dataEntryExerciseSummary->setAltitudeGain(0);
            $dataEntryExerciseSummary->setAltitudeLoss($altitudeStart - $altitudeFinish);
        } else if ($altitudeStart < $altitudeFinish) {
            $dataEntryExerciseSummary->setAltitudeGain($altitudeFinish - $altitudeStart);
            $dataEntryExerciseSummary->setAltitudeLoss(0);
        } else {
            $dataEntryExerciseSummary->setAltitudeGain(0);
            $dataEntryExerciseSummary->setAltitudeLoss(0);
        }

        if (!is_null($altitudeMax)) $dataEntryExerciseSummary->setAltitudeMax($altitudeMax);
        if (!is_null($altitudeMin)) $dataEntryExerciseSummary->setAltitudeMin($altitudeMin);
//        if (property_exists($jsonContent, "cadenceMax") && $jsonContent->cadenceMax > 0) $dataEntryExerciseSummary->setCadenceMax($jsonContent->cadenceMax);
//        if (property_exists($jsonContent, "cadenceMean") && $jsonContent->cadenceMean > 0) $dataEntryExerciseSummary->setCadenceMean($jsonContent->cadenceMean);
//        if (property_exists($jsonContent, "cadenceMin") && $jsonContent->cadenceMin > 0) $dataEntryExerciseSummary->setCadenceMin($jsonContent->cadenceMin);
//        if (property_exists($jsonContent, "calorie") && $jsonContent->calorie > 0) $dataEntryExerciseSummary->setCalorie($jsonContent->calorie);
//        if (property_exists($jsonContent, "declineDistance") && $jsonContent->declineDistance > 0) $dataEntryExerciseSummary->setDistanceDecline($jsonContent->declineDistance);
        $dataEntryExerciseSummary->setDistance($totalDistance);
//        if (property_exists($jsonContent, "heartRateMax") && $jsonContent->heartRateMax > 0) $dataEntryExerciseSummary->setHeartRateMax($jsonContent->heartRateMax);
//        if (property_exists($jsonContent, "heartRateMean") && $jsonContent->heartRateMean > 0) $dataEntryExerciseSummary->setHeartRateMean($jsonContent->heartRateMean);
//        if (property_exists($jsonContent, "heartRateMin") && $jsonContent->heartRateMin > 0) $dataEntryExerciseSummary->setHeartRateMin($jsonContent->heartRateMin);
//        if (property_exists($jsonContent, "inclineDistance") && $jsonContent->inclineDistance > 0) $dataEntryExerciseSummary->setDistanceIncline($jsonContent->inclineDistance);
        if (count($speedData) > 0) {
            $speedData = array_filter($speedData);
            $dataEntryExerciseSummary->setSpeedMax(max($speedData));
            $average = array_sum($speedData)/count($speedData);
            $dataEntryExerciseSummary->setSpeedMean($average);
        }

        $dataEntryExercise->setExerciseSummary($dataEntryExerciseSummary);

        return $dataEntryExercise;

    }

}
