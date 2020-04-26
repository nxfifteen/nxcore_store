<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nxcore/
 * @link      https://gitlab.com/nx-core/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

/** @noinspection DuplicatedCode */

namespace App\Transform\SamsungHealth;

use App\AppConstants;
use App\Entity\BodyWeight;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\SiteNews;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use App\Service\AwardManager;
use App\Service\CommsManager;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class SamsungBodyWeight
 *
 * @package App\Transform\SamsungHealth
 */
class SamsungBodyWeight extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @param CommsManager    $commsManager
     *
     * @return BodyWeight|null
     * @throws Exception
     */
    public static function translate(
        ManagerRegistry $doctrine,
        string $getContent,
        AwardManager $awardManager,
        CommsManager $commsManager
    ) {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {
            ///AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - New call too BaseBodyWeight for " . $jsonContent->remoteId);

            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent->uuid);
            if (is_null($patient)) {
                return null;
            }

            if (self::startsWith($jsonContent->comment, "Fitbit: ") && $jsonContent->device == "yJdRrYpC43") {
                /** @var ThirdPartyService $thirdPartyService */
                $thirdPartyService = self::getThirdPartyService($doctrine, self::FITBITSERVICE);
            } else {
                /** @var ThirdPartyService $thirdPartyService */
                $thirdPartyService = self::getThirdPartyService($doctrine, self::SAMSUNGHEALTHSERVICE);
            }
            if (is_null($thirdPartyService)) {
                return null;
            }

            if (self::startsWith($jsonContent->comment, "Fitbit: ") && $jsonContent->device == "yJdRrYpC43") {
                /** @var TrackingDevice $deviceTracking */
                $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, "Aria");
            } else {
                /** @var TrackingDevice $deviceTracking */
                $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService,
                    $jsonContent->device);
            }
            if (is_null($deviceTracking)) {
                return null;
            }

            /** @var PartOfDay $partOfDay */
            $partOfDay = self::getPartOfDay($doctrine, new DateTime($jsonContent->dateTime));
            if (is_null($partOfDay)) {
                return null;
            }

            /** @var UnitOfMeasurement $unitOfMeasurement */
            $unitOfMeasurement = self::getUnitOfMeasurement($doctrine, $jsonContent->weightUnitOfMeasurement);
            if (is_null($unitOfMeasurement)) {
                return null;
            }

            /** @var PatientGoals $patientGoal */
            $patientGoal = self::getPatientGoal($doctrine, "BodyWeight", $jsonContent->weightGoal, $unitOfMeasurement,
                $patient);
            if (is_null($patientGoal)) {
                return null;
            }

            $newItem = false;
            /** @var BodyWeight $dataEntry */
            $dataEntry = $doctrine->getRepository(BodyWeight::class)->findOneBy([
                'RemoteId' => $jsonContent->remoteId,
                'patient' => $patient,
                'trackingDevice' => $deviceTracking,
            ]);
            if (!$dataEntry) {
                $newItem = true;
                $dataEntry = new BodyWeight();
                $safeGuid = false;
                $i = 0;
                do {
                    $i++;
                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': Added a GUID (' . $i . ')');
                    $dataEntry->createGuid(true);
                    $dataEntryGuidCheck = $doctrine
                        ->getRepository(BodyWeight::class)
                        ->findByGuid($dataEntry->getGuid());
                    if (empty($dataEntryGuidCheck)) {
                        $safeGuid = true;
                    }

                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': ' . gettype($dataEntryGuidCheck));
                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': ' . count($dataEntryGuidCheck));
                } while (!$safeGuid);
            }

            $dataEntry->setPatient($patient);

            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent->remoteId);
            $dataEntry->setMeasurement($jsonContent->weightMeasurement);
            $dataEntry->setUnitOfMeasurement($unitOfMeasurement);
            $dataEntry->setPatientGoal($patientGoal);
            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> (new DateTime($jsonContent->dateTime))->format("U")) {
                $dataEntry->setDateTime(new DateTime($jsonContent->dateTime));
            }
            $dataEntry->setPartOfDay($partOfDay);
            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            if ($newItem) {
                /** @noinspection DuplicatedCode */
                $previousWeight = $doctrine->getRepository(BodyWeight::class)->findPrevious($patient->getId(),
                    $dataEntry->getDateTime());
                if ($previousWeight) {
                    if ($dataEntry->getMeasurement() < $previousWeight->getMeasurement()) {
                        $body = ":thumbsup: You #lost " . ($previousWeight->getMeasurement() - $dataEntry->getMeasurement()) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " since your last #weight in.";
                    } else {
                        if ($dataEntry->getMeasurement() > $previousWeight->getMeasurement()) {
                            $body = ":thumbsdown: You #gained " . ($dataEntry->getMeasurement() - $previousWeight->getMeasurement()) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " since your last #weight in.";
                        } else {
                            $body = ":ok_hand: You weight hasn't changed since your last #weight in.";
                        }
                    }

                    /* @var BodyWeight $sevenDayAgoWeight */
                    $sevenDayAgoWeight = $doctrine->getRepository(BodyWeight::class)
                        ->findSevenDayAgo($patient->getId(), new DateTime());
                    if (!is_null($sevenDayAgoWeight)) {
                        $sevenDayAgoWeightMeasurement = round($sevenDayAgoWeight->getMeasurement(), 2);
                        AppConstants::writeToLog('debug_transform.txt',
                            $sevenDayAgoWeightMeasurement . ' ' . $sevenDayAgoWeight->getUnitOfMeasurement()->getName() . ' ' . $sevenDayAgoWeight->getDateTime()->format("Y-m-d"));
                        if ($dataEntry->getMeasurement() < $sevenDayAgoWeightMeasurement) {
                            $body = $body . "\nYou're now " . ($sevenDayAgoWeightMeasurement - $dataEntry->getMeasurement()) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " less than you were seven weigh-ins ago.";
                        } else {
                            if ($dataEntry->getMeasurement() > $sevenDayAgoWeightMeasurement) {
                                $body = $body . "\nYou're actually " . ($dataEntry->getMeasurement() - $sevenDayAgoWeightMeasurement) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " heaver then seven weigh-ins ago.";
                            } else {
                                $body = $body . "\nYou weight hasn't changed over the last seven weigh-ins.";
                            }
                        }
                    }

                    /* @var float $sevenDayAvgWeight */
                    $sevenDayAvgWeight = $doctrine->getRepository(BodyWeight::class)
                        ->findSevenDayAverage($patient->getId(), new DateTime());
                    if (!is_null($sevenDayAvgWeight)) {
                        $sevenDayAvgWeight = round($sevenDayAvgWeight, 2);
                        if (round($dataEntry->getMeasurement(), 2) < $sevenDayAvgWeight) {
                            $body = $body . "\nYour " . ($sevenDayAvgWeight - $dataEntry->getMeasurement()) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " bellow your seven weigh-in average";
                        } else {
                            if (round($dataEntry->getMeasurement(), 2) > $sevenDayAvgWeight) {
                                $body = $body . "\nYour " . ($dataEntry->getMeasurement() - $sevenDayAvgWeight) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " above your seven weigh-in average";
                            } else {
                                $body = $body . "\nYour seven weigh-in average is " . $sevenDayAvgWeight . " " . $dataEntry->getUnitOfMeasurement()->getName();
                            }
                        }
                    }

                    /* @var BodyWeight[] $firstReading */
                    $firstReading = $doctrine->getRepository(BodyWeight::class)
                        ->findFirst($patient->getUuid());
                    $firstReading = $firstReading[0];

                    if ($dataEntry->getMeasurement() < $firstReading->getMeasurement()) {
                        $body = $body . "\nYou've lost " . round($firstReading->getMeasurement() - $dataEntry->getMeasurement(),
                                2) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " since your first weight in.";
                    } else {
                        if ($dataEntry->getMeasurement() > $firstReading->getMeasurement()) {
                            $body = $body . "\nYou've gained " . round($dataEntry->getMeasurement() - $firstReading->getMeasurement(),
                                    2) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " since your first weight in.";
                        } else {
                            $body = $body . "\nYou've weight hasn't changed since your first weight in.";
                        }
                    }


                    $commsManager->sendNotification(
                        "You've just #recorded a new #weight of " . $dataEntry->getMeasurement() . " " . $unitOfMeasurement->getName(),
                        $body,
                        $patient,
                        true
                    );
                } else {
                    $commsManager->sendNotification(
                        "You've just #recorded a new #weight of " . $dataEntry->getMeasurement() . " " . $unitOfMeasurement->getName(),
                        null,
                        $patient,
                        true
                    );
                }

                $notification = new SiteNews();
                $notification->setPatient($patient);
                $notification->setPublished(new DateTime());
                $notification->setTitle("New Weight Recorded");
                $notification->setText("You've just recorded a new weight of " . $jsonContent->weightMeasurement . " " . $unitOfMeasurement->getName());
                $notification->setAccent('success');
                $notification->setImage("recorded_weight");
                $notification->setExpires(new DateTime(date("Y-m-d 23:59:59")));
                $notification->setLink('/body/weight');
                $notification->setPriority(3);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($notification);
                $entityManager->flush();
            }

            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntry)), $patient,
                $thirdPartyService, $dataEntry->getDateTime());

            if ($newItem) {
                $awardManager->checkForAwards($dataEntry, "weight");
            }

            return $dataEntry;

        }

        return null;
    }
}
