<?php

namespace App\Transform\Fitbit;


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
use App\Service\TweetManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class FitbitBodyWeight extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param Object          $jsonContent
     *
     * @param AwardManager    $awardManager
     *
     * @param TweetManager    $tweetManager
     *
     * @return BodyWeight|null
     * @throws Exception
     */
    public static function translate(ManagerRegistry $doctrine, $jsonContent, AwardManager $awardManager, TweetManager $tweetManager)
    {
        if (property_exists($jsonContent[0], "uuid") && $jsonContent[2]->body->weight > 0) {

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
            $unitOfMeasurement = self::getUnitOfMeasurement($doctrine, "kg");
            if (is_null($unitOfMeasurement)) {

                return NULL;
            }


            /** @var PatientGoals $patientGoal */
            $patientGoal = self::getPatientGoal($doctrine, "BodyWeight", $jsonContent[2]->goals->weight, $unitOfMeasurement, $patient);
            if (is_null($patientGoal)) {

                return NULL;
            }


            $jsonContent[0]->remoteId = $jsonContent[0]->remoteId . 'FitbitBodyWeight' . (new \DateTime($jsonContent[0]->dateTime))->format("Y-m-d");

            $newItem = FALSE;
            /** @var BodyWeight $dataEntry */
            $dataEntry = $doctrine->getRepository(BodyWeight::class)->findOneBy(['RemoteId' => $jsonContent[0]->remoteId, 'patient' => $patient, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntry) {
                if ((new \DateTime($jsonContent[0]->dateTime))->format("Y-m-d") == date("Y-m-d")) $newItem = TRUE;
                $dataEntry = new BodyWeight();
            }

            $dataEntry->setPatient($patient);

            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent[0]->remoteId);
            $dataEntry->setPatientGoal($patientGoal);
            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> (new \DateTime($jsonContent[0]->dateTime))->format("U")) {
                $dataEntry->setDateTime(new \DateTime($jsonContent[0]->dateTime));
            }
            $dataEntry->setMeasurement($jsonContent[2]->body->weight);
            $dataEntry->setUnitOfMeasurement($unitOfMeasurement);
            $dataEntry->setPartOfDay($partOfDay);
            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            if ($newItem) {
                /** @noinspection DuplicatedCode */
                $previousWeight = $doctrine->getRepository(BodyWeight::class)->findPrevious($patient->getId(), $dataEntry->getDateTime());
                if ($previousWeight) {
                    if ($dataEntry->getMeasurement() < $previousWeight->getMeasurement()) {
                        $body = ":thumbsup: You #lost " . ($previousWeight->getMeasurement() - $dataEntry->getMeasurement()) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " since your last #weight in.";
                    } else if ($dataEntry->getMeasurement() > $previousWeight->getMeasurement()) {
                        $body = ":thumbsdown: You #gained " . ($dataEntry->getMeasurement() - $previousWeight->getMeasurement()) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " since your last #weight in.";
                    } else {
                        $body = ":ok_hand: You weight hasn't changed since your last #weight in.";
                    }

                    /* @var BodyWeight $sevenDayAgoWeight */
                    $sevenDayAgoWeight = $doctrine->getRepository(BodyWeight::class)
                        ->findSevenDayAgo($patient->getId(), new \DateTime());
                    if (!is_null($sevenDayAgoWeight)) {
                        $sevenDayAgoWeightMeasurement = round($sevenDayAgoWeight->getMeasurement(), 2);
                        AppConstants::writeToLog('debug_transform.txt', $sevenDayAgoWeightMeasurement . ' ' . $sevenDayAgoWeight->getUnitOfMeasurement()->getName() . ' ' . $sevenDayAgoWeight->getDateTime()->format("Y-m-d"));
                        if ($dataEntry->getMeasurement() < $sevenDayAgoWeightMeasurement) {
                            $body = $body . "\nYou're now " . ($sevenDayAgoWeightMeasurement - $dataEntry->getMeasurement()) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " less than you were seven weigh-ins ago.";
                        } else if ($dataEntry->getMeasurement() > $sevenDayAgoWeightMeasurement) {
                            $body = $body . "\nYou're actually " . ($dataEntry->getMeasurement() - $sevenDayAgoWeightMeasurement) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " heaver then seven weigh-ins ago.";
                        } else {
                            $body = $body . "\nYou weight hasn't changed over the last seven weigh-ins.";
                        }
                    }

                    /* @var float $sevenDayAvgWeight */
                    $sevenDayAvgWeight = $doctrine->getRepository(BodyWeight::class)
                        ->findSevenDayAverage($patient->getId(), new \DateTime());
                    if (!is_null($sevenDayAvgWeight)) {
                        $sevenDayAvgWeight = round($sevenDayAvgWeight, 2);
                        if (round($dataEntry->getMeasurement(), 2) < $sevenDayAvgWeight) {
                            $body = $body . "\nYour " . ($sevenDayAvgWeight - $dataEntry->getMeasurement()) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " bellow your seven weigh-in average";
                        } else if (round($dataEntry->getMeasurement(), 2) > $sevenDayAvgWeight) {
                            $body = $body . "\nYour " . ($dataEntry->getMeasurement() - $sevenDayAvgWeight) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " above your seven weigh-in average";
                        } else {
                            $body = $body . "\nYour seven weigh-in average is " . $sevenDayAvgWeight . " " . $dataEntry->getUnitOfMeasurement()->getName();
                        }
                    }

                    /* @var BodyWeight[] $firstReading */
                    $firstReading = $doctrine->getRepository(BodyWeight::class)
                        ->findFirst($patient->getUuid());
                    $firstReading = $firstReading[0];

                    if ($dataEntry->getMeasurement() < $firstReading->getMeasurement()) {
                        $body = $body . "\nYou've lost " . round($firstReading->getMeasurement() - $dataEntry->getMeasurement(), 2) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " since your first weight in.";
                    } else if ($dataEntry->getMeasurement() > $firstReading->getMeasurement()) {
                        $body = $body . "\nYou've gained " . round($dataEntry->getMeasurement() - $firstReading->getMeasurement(), 2) . " " . $dataEntry->getUnitOfMeasurement()->getName() . " since your first weight in.";
                    } else {
                        $body = $body . "\nYou've weight hasn't changed since your first weight in.";
                    }


                    $tweetManager->sendNotification(
                        "You've just #recorded a new #weight of " . $dataEntry->getMeasurement() . " " . $unitOfMeasurement->getName(),
                        $body,
                        $patient,
                        TRUE
                    );
                } else {
                    $tweetManager->sendNotification(
                        "You've just #recorded a new #weight of " . $dataEntry->getMeasurement() . " " . $unitOfMeasurement->getName(),
                        NULL,
                        $patient,
                        TRUE
                    );
                }

                $notification = new SiteNews();
                $notification->setPatient($patient);
                $notification->setPublished(new \DateTime());
                $notification->setTitle("New Weight Recorded");
                $notification->setText("You've just recorded a new weight of " . $dataEntry->getMeasurement() . " " . $unitOfMeasurement->getName());
                $notification->setAccent('success');
                $notification->setImage("recorded_weight");
                $notification->setExpires(new \DateTime(date("Y-m-d 23:59:59")));
                $notification->setLink('/body/weight');
                $notification->setPriority(3);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($notification);
                $entityManager->flush();
            }

            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntry)), $patient, $thirdPartyService, $dataEntry->getDateTime());

            return $dataEntry;

        }

        return NULL;
    }

}