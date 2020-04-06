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

namespace App\Transform;

use App\AppConstants;
use App\Entity\ApiAccessLog;
use App\Entity\ExerciseType;
use App\Entity\FoodDatabase;
use App\Entity\FoodMeals;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class Transform
 *
 * @package App\Transform
 */
class Transform
{

    /**
     * @param Object $getContent
     *
     * @return mixed
     */
    protected static function encodeJson(Object $getContent)
    {
        $jsonObject = json_encode($getContent);
        return $jsonObject;
    }

    /**
     * @param String $getContent
     *
     * @return mixed
     */
    protected static function decodeJson(String $getContent)
    {
        $jsonObject = json_decode($getContent, FALSE);
        if (is_object($jsonObject)) {
            // @TODO: Remove hard coding
            if (!property_exists($jsonObject, "uuid")) {
                $jsonObject->uuid = "269VLG";
            }
            if (property_exists($jsonObject, "x-trackingDevice")) {
                $jsonObject->device = $jsonObject->{'x-trackingDevice'};
            }
            if (property_exists($jsonObject, "x-unitOfMeasurement")) {
                $jsonObject->units = $jsonObject->{'x-unitOfMeasurement'};
            }
        }
        return $jsonObject;
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param                 $uuid
     *
     * @return Patient|null
     */
    protected static function getPatient(ManagerRegistry $doctrine, String $uuid)
    {
        /** @var Patient $patient */
        $patient = $doctrine->getRepository(Patient::class)->findOneBy(['uuid' => $uuid]);
        if ($patient) {
            return $patient;
        }

        return NULL;
    }

    /**
     * @param ManagerRegistry   $doctrine
     * @param Patient           $patient
     * @param ThirdPartyService $thirdPartyService
     * @param String            $remote_id
     *
     * @param array             $options
     *
     * @return TrackingDevice|null
     */
    protected static function getTrackingDevice(ManagerRegistry $doctrine, Patient $patient, ThirdPartyService $thirdPartyService, String $remote_id, array $options = [])
    {
        /** @var TrackingDevice $deviceTracking */
        $deviceTracking = $doctrine->getRepository(TrackingDevice::class)->findOneBy(['remoteId' => $remote_id, 'patient' => $patient, 'service' => $thirdPartyService]);
        if ($deviceTracking) {
            return $deviceTracking;
        } else {
            $entityManager = $doctrine->getManager();
            $deviceTracking = new TrackingDevice();
            $deviceTracking->setPatient($patient);
            $deviceTracking->setService($thirdPartyService);
            $deviceTracking->setRemoteId($remote_id);
            $deviceTracking->setName($remote_id);
            $deviceTracking->setType("Unknown");

            if (count($options) > 0) {
                if (array_key_exists("name", $options)) $deviceTracking->setName($options['name']);
                if (array_key_exists("comment", $options)) $deviceTracking->setComment($options['comment']);
                if (array_key_exists("battery", $options)) $deviceTracking->setBattery($options['battery']);
                if (array_key_exists("type", $options)) $deviceTracking->setType($options['type']);
                if (array_key_exists("manufacturer", $options)) $deviceTracking->setManufacturer($options['manufacturer']);
                if (array_key_exists("model", $options)) $deviceTracking->setModel($options['model']);
            }

            $entityManager->persist($deviceTracking);
            $entityManager->flush();

            return $deviceTracking;
        }
    }

    /**
     * @param ManagerRegistry   $doctrine
     * @param String            $serviceName
     *
     * @param float             $serviceGoal
     *
     * @param UnitOfMeasurement $unitOfMeasurement
     * @param Patient           $patient
     *
     * @param bool              $matchGoal
     *
     * @return PatientGoals|null
     * @throws Exception
     */
    protected static function getPatientGoal(ManagerRegistry $doctrine, String $serviceName, float $serviceGoal, $unitOfMeasurement, Patient $patient, bool $matchGoal = NULL)
    {
        if (!is_null($matchGoal) && $matchGoal) {
            $findBy = ['entity' => $serviceName, 'patient' => $patient, 'goal' => $serviceGoal];
        } else {
            $findBy = ['entity' => $serviceName, 'patient' => $patient];
        }

        /** @var PatientGoals $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(PatientGoals::class)->findOneBy($findBy, ['dateSet' => 'DESC']);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            $entityManager = $doctrine->getManager();
            $thirdPartyService = new PatientGoals();
            $thirdPartyService->setPatient($patient);
            $thirdPartyService->setGoal($serviceGoal);
            $thirdPartyService->setEntity($serviceName);
            $thirdPartyService->setDateSet(new DateTime());
            if (!is_null($unitOfMeasurement)) {
                $thirdPartyService->setUnitOfMeasurement($unitOfMeasurement);
            }

            $entityManager->persist($thirdPartyService);
            $entityManager->flush();

            return $thirdPartyService;
        }
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return ThirdPartyService|null
     */
    protected static function getThirdPartyService(ManagerRegistry $doctrine, String $serviceName)
    {
        /** @var ThirdPartyService $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(ThirdPartyService::class)->findOneBy(['name' => $serviceName]);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            $entityManager = $doctrine->getManager();
            $thirdPartyService = new ThirdPartyService();
            $thirdPartyService->setName($serviceName);
            $entityManager->persist($thirdPartyService);
            $entityManager->flush();

            return $thirdPartyService;
        }
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return UnitOfMeasurement|null
     */
    protected static function getUnitOfMeasurement(ManagerRegistry $doctrine, String $serviceName)
    {
        /** @var UnitOfMeasurement $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(UnitOfMeasurement::class)->findOneBy(['name' => $serviceName]);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            $entityManager = $doctrine->getManager();
            $thirdPartyService = new UnitOfMeasurement();
            $thirdPartyService->setName($serviceName);
            $entityManager->persist($thirdPartyService);
            $entityManager->flush();

            return $thirdPartyService;
        }
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $food_info_id
     *
     * @return FoodDatabase|null
     */
    protected static function getMealFoodItem(ManagerRegistry $doctrine, String $food_info_id)
    {
        /** @var FoodDatabase $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(FoodDatabase::class)->findByFoodInfoId($food_info_id);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            return NULL;
        }
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return FoodMeals|null
     */
    protected static function getMealType(ManagerRegistry $doctrine, String $serviceName)
    {
        /** @var FoodMeals $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(FoodMeals::class)->findOneBy(['name' => $serviceName]);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            $entityManager = $doctrine->getManager();
            $thirdPartyService = new FoodMeals();
            $thirdPartyService->setName($serviceName);
            $entityManager->persist($thirdPartyService);
            $entityManager->flush();

            return $thirdPartyService;
        }
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return ExerciseType|null
     */
    protected static function getExerciseType(ManagerRegistry $doctrine, String $serviceName)
    {
        /** @var ExerciseType $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(ExerciseType::class)->findOneBy(['name' => $serviceName]);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            $entityManager = $doctrine->getManager();
            $thirdPartyService = new ExerciseType();
            $thirdPartyService->setName($serviceName);
            $entityManager->persist($thirdPartyService);
            $entityManager->flush();

            return $thirdPartyService;
        }
    }

    /**
     * @param ManagerRegistry   $doctrine
     * @param DateTimeInterface $trackedDate
     *
     * @return PartOfDay|null
     */
    protected static function getPartOfDay(ManagerRegistry $doctrine, DateTimeInterface $trackedDate)
    {
        /** @var DateTime $trackedDate */
        $trackedDate = $trackedDate->format("U");
        $trackedDate = date("H", $trackedDate);

        if ($trackedDate >= 12 && $trackedDate <= 16) {
            $partOfDayString = "afternoon";
        } else if ($trackedDate >= 17 && $trackedDate <= 20) {
            $partOfDayString = "evening";
        } else if ($trackedDate >= 5 && $trackedDate <= 11) {
            $partOfDayString = "morning";
        } else {
            $partOfDayString = "night";
        }

        /** @var PartOfDay $partOfDay */
        $partOfDay = $doctrine->getRepository(PartOfDay::class)->findOneBy(['name' => $partOfDayString]);
        if ($partOfDay) {
            return $partOfDay;
        } else {
            $entityManager = $doctrine->getManager();
            $partOfDay = new PartOfDay();
            $partOfDay->setName($partOfDayString);
            $entityManager->persist($partOfDay);
            $entityManager->flush();

            return $partOfDay;
        }
    }

    /**
     * @param $string
     * @param $startString
     *
     * @deprecated User AppConstants::startsWith instead
     *
     * @return bool
     */
    protected static function startsWith($string, $startString)
    {
        return AppConstants::startsWith($string, $startString);
    }

    /**
     * @param ManagerRegistry   $doctrine
     * @param String            $fullClassName
     * @param Patient           $patient
     * @param ThirdPartyService $service
     * @param DateTimeInterface $dateTime
     *
     * @return ApiAccessLog
     * @throws Exception
     */
    protected static function updateApi(ManagerRegistry $doctrine, String $fullClassName, Patient $patient, ThirdPartyService $service, DateTimeInterface $dateTime)
    {
        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - translateEntity class type is : " . $fullClassName);
        /** @var ApiAccessLog $dataEntry */
        $dataEntry = $doctrine->getRepository(ApiAccessLog::class)->findOneBy(['entity' => $fullClassName, 'patient' => $patient, 'thirdPartyService' => $service]);
        if (!$dataEntry) {
            $dataEntry = new ApiAccessLog();
        }

        $dataEntry->setPatient($patient);
        $dataEntry->setThirdPartyService($service);
        $dataEntry->setEntity($fullClassName);
        $dataEntry->setLastRetrieved($dateTime);
        $dataEntry->setLastPulled(new DateTime());
        $interval = new DateInterval('PT40M');
        $dataEntry->setCooldown((new DateTime())->add($interval));

        $entityManager = $doctrine->getManager();
        $entityManager->persist($dataEntry);
        $entityManager->flush();

        return $dataEntry;
    }
}
