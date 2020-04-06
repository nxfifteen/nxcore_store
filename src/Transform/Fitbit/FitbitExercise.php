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

namespace App\Transform\Fitbit;


use App\AppConstants;
use App\Entity\Exercise;
use App\Entity\ExerciseSummary;
use App\Entity\ExerciseType;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\PatientCredentials;
use App\Entity\SiteNews;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Service\TweetManager;
use DateTime;
use djchen\OAuth2\Client\Provider\Fitbit;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use SimpleXMLElement;

/**
 * Class FitbitExercise
 *
 * @package App\Transform\Fitbit
 */
class FitbitExercise extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param                 $getContent
     * @param int             $deviceArrayIndex
     *
     * @param TweetManager    $tweetManager
     *
     * @return Exercise|null
     * @throws \Exception
     */
    public static function translate(ManagerRegistry $doctrine, TweetManager $tweetManager, $getContent, int $deviceArrayIndex = 0)
    {
        if (property_exists($getContent[3], "activities") && property_exists($getContent[3]->activities[$deviceArrayIndex], "logId")) {
            $activity = $getContent[3]->activities[$deviceArrayIndex];

            try {
                $activity->lastModified = new DateTime($activity->lastModified);
                $activity->startTime = new DateTime($activity->startTime);
                $activity->originalStartTime = new DateTime($activity->originalStartTime);
                $activity->finishTime = clone $activity->startTime;
                $activity->finishTime->modify('+ ' . ($activity->originalDuration / 1000) . ' seconds');
            } catch (Exception $e) {
                AppConstants::writeToLog(
                    'debug_transform.txt',
                    $e->getMessage()
                );
                return NULL;
            }

            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $getContent[0]->uuid);
            if (is_null($patient)) {
                return NULL;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::FITBITSERVICE);
            if (is_null($thirdPartyService)) {
                return NULL;
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $activity->source->id, [
                "name" => $activity->source->name,
                "type" => $activity->source->type,
                "manufacturer" => 'Fitbit',
                "model" => $activity->source->name,
            ]);
            if (is_null($deviceTracking)) {
                return NULL;
            }

            /** @var PartOfDay $partOfDay */
            $partOfDay = self::getPartOfDay($doctrine, $activity->startTime);
            if (is_null($partOfDay)) {
                return NULL;
            }

            /** @var ExerciseType $exerciseType */
            $exerciseType = self::getExerciseType($doctrine, self::convertExerciseType($activity->activityTypeId));
            if (is_null($exerciseType)) {
                return NULL;
            }

            $newItem = FALSE;
            /** @var Exercise $dataEntryExercise */
            $dataEntryExercise = $doctrine->getRepository(Exercise::class)->findOneBy(['RemoteId' => $activity->logId, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntryExercise) {
                $dataEntryExercise = new Exercise();
                $newItem = TRUE;
            }

            $dataEntryExercise->setPatient($patient);
            $dataEntryExercise->setTrackingDevice($deviceTracking);
            $dataEntryExercise->setPartOfDay($partOfDay);
            $dataEntryExercise->setRemoteId($activity->logId);

            if (is_null($dataEntryExercise->getDateTimeStart()) || $dataEntryExercise->getDateTimeStart()->format("U") <> $activity->startTime->format("U")) {
                $dataEntryExercise->setDateTimeStart($activity->startTime);
            }
            if (is_null($dataEntryExercise->getDateTimeEnd()) || $dataEntryExercise->getDateTimeEnd()->format("U") <> $activity->finishTime->format("U")) {
                $dataEntryExercise->setDateTimeEnd($activity->finishTime);
            }
            $dataEntryExercise->setDuration($dataEntryExercise->getDateTimeEnd()->format("U") - $dataEntryExercise->getDateTimeStart()->format("U"));
            $dataEntryExercise->setExerciseType($exerciseType);

            $dataEntryExercise->setSteps($activity->steps);

            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntryExercise->getDateTimeEnd()->format("U")) {
                $deviceTracking->setLastSynced($dataEntryExercise->getDateTimeEnd());
            }

            $altitudeMax = NULL;
            $altitudeMin = NULL;
            if (property_exists($activity, "tcxLink")) {
                $locationApiData = self::pullTCXData($doctrine, $patient, $thirdPartyService, $activity->tcxLink);
                if (
                    !is_null($locationApiData) &&
                    property_exists($locationApiData, "Activities") &&
                    property_exists($locationApiData->Activities, "Activity") &&
                    property_exists($locationApiData->Activities->Activity, "Lap") &&
                    property_exists($locationApiData->Activities->Activity->Lap, "Track") &&
                    property_exists($locationApiData->Activities->Activity->Lap->Track, "Trackpoint")
                ) {
                    $locationData = [];
                    $liveData = [];

                    $trackPoints = $locationApiData->Activities->Activity->Lap->Track->Trackpoint;
                    foreach ($trackPoints as $trackPoint) {
                        $trackPoint = json_decode(json_encode($trackPoint), FALSE);

                        $pointDateTime = new DateTime($trackPoint->Time);
                        $pointRef = $pointDateTime->format("U") / 1000;

                        $locationDataItem = [];

                        /*if (property_exists($trackPoint, "DistanceMeters")) {
                            $liveData[] = [
                                "start_time" => $pointRef,
                                "distance" => $trackPoint->DistanceMeters,
                            ];
                        }
                        if (property_exists($trackPoint, "HeartRate")) {
                            $liveData[] = [
                                "start_time" => $pointRef,
                                "heart_rate" => $trackPoint->HeartRate,
                            ];
                        }
                        if (property_exists($trackPoint, "Speed")) {
                            $liveData[] = [
                                "start_time" => $pointRef,
                                "speed" => $trackPoint->Speed,
                            ];
                        }*/

                        /*AppConstants::writeToLog(
                            'debug_transform.txt',
                            print_r($trackPoint, true)
                        );*/

                        if (property_exists($trackPoint, "Position")) {
                            $locationDataItem['start_time'] = $pointRef;
                            if (property_exists($trackPoint->Position, "LatitudeDegrees")) {
                                $locationDataItem['latitude'] = $trackPoint->Position->LatitudeDegrees;
                            }
                            if (property_exists($trackPoint->Position, "LongitudeDegrees")) {
                                $locationDataItem['longitude'] = $trackPoint->Position->LongitudeDegrees;
                            }
                            if (property_exists($trackPoint, "AltitudeMeters")) {
                                $locationDataItem['altitude'] = $trackPoint->AltitudeMeters;
                                if (is_null($altitudeMin) || $trackPoint->AltitudeMeters < $altitudeMin) {
                                    $altitudeMin = $trackPoint->AltitudeMeters;
                                }

                                if (is_null($altitudeMax) || $trackPoint->AltitudeMeters > $altitudeMax) {
                                    $altitudeMax = $trackPoint->AltitudeMeters;
                                }
                            }
                        }

                        if (count($locationDataItem) > 1) {
                            $locationData[] = $locationDataItem;
                        }
                    }

                    if (count($locationData) > 1) {
                        $dataEntryExercise->setLocationDataBlob(AppConstants::compressString(json_encode($locationData)));
                    }

                    if (count($liveData) > 1) {
                        $dataEntryExercise->setLiveDataBlob(AppConstants::compressString(json_encode($liveData)));
                    }
                }
            }

            $dataEntryExerciseSummary = $dataEntryExercise->getExerciseSummary();
            if (is_null($dataEntryExerciseSummary)) {
                $dataEntryExerciseSummary = new ExerciseSummary();
            }


            if (!is_null($altitudeMax)) $dataEntryExerciseSummary->setAltitudeMax($altitudeMax);
            if (!is_null($altitudeMin)) $dataEntryExerciseSummary->setAltitudeMin($altitudeMin);
            if (!is_null($altitudeMax) && !is_null($altitudeMax)) $dataEntryExerciseSummary->setAltitudeGain($altitudeMax - $altitudeMin);

            if (property_exists($activity, "calories") && $activity->calories > 0) $dataEntryExerciseSummary->setCalorie($activity->calories);
            if (property_exists($activity, "distance") && $activity->distance > 0) $dataEntryExerciseSummary->setDistance($activity->distance * 1000);
            if (property_exists($activity, "speed") && $activity->speed > 0) $dataEntryExerciseSummary->setSpeedMean($activity->speed);

            $dataEntryExercise->setExerciseSummary($dataEntryExerciseSummary);

            if ($newItem) {
                $tweetManager->sendNotification(
                    "@" . $patient->getUuid() . " just #recorded a new " . round($dataEntryExercise->getDuration() / 60, 0) . " minute #" . strtolower($dataEntryExercise->getExerciseType()->getTag()) . " :dog_14:",
                    NULL,
                    $patient,
                    FALSE
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

            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntryExercise)), $patient, $thirdPartyService, $dataEntryExercise->getDateTimeStart());

            return $dataEntryExercise;

        }

        return NULL;
    }

    /**
     * @param ManagerRegistry   $doctrine
     * @param Patient           $patient
     * @param ThirdPartyService $service
     * @param                   $path
     *
     * @return SimpleXMLElement|null
     */
    private static function pullTCXData(ManagerRegistry $doctrine, Patient $patient, ThirdPartyService $service, $path)
    {
        /** @var PatientCredentials $accessToken */
        $accessToken = $doctrine
            ->getRepository(PatientCredentials::class)
            ->findOneBy(['patient' => $patient, 'service' => $service]);

        $accessToken = new AccessToken([
            'access_token' => $accessToken->getToken(),
            'refresh_token' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires()->format("U"),
        ]);

        if (!$accessToken->hasExpired()) {
            try {
                $fitbitLibrary = self::getLibrary();

                $request = $fitbitLibrary->getAuthenticatedRequest('GET', $path, $accessToken);
                $response = simplexml_load_string($fitbitLibrary->getParsedResponse($request));

                return $response;
            } catch (IdentityProviderException $e) {
                AppConstants::writeToLog('debug_transform.txt', "[] - " . ' ' . $e->getMessage());
            }
        }

        return NULL;
    }

    /**
     * @return Fitbit
     */
    private static function getLibrary()
    {
        return new Fitbit([
            'clientId' => $_ENV['FITBIT_ID'],
            'clientSecret' => $_ENV['FITBIT_SECRET'],
            'redirectUri' => $_ENV['INSTALL_URL'] . '/auth/refresh/fitbit',
        ]);
    }
}
