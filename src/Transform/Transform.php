<?php

namespace App\Transform;

use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

class Transform
{

    /**
     * @param String $getContent
     *
     * @return mixed
     */
    protected static function decodeJson(String $getContent)
    {
        $jsonObject = json_decode($getContent, FALSE);
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
     * @return TrackingDevice|null
     */
    protected static function getTrackingDevice(ManagerRegistry $doctrine, Patient $patient, ThirdPartyService $thirdPartyService, String $remote_id)
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
            $entityManager->persist($deviceTracking);
            $entityManager->flush();

            return $deviceTracking;
        }
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @param float           $serviceGoal
     *
     * @param Patient         $patient
     *
     * @return PatientGoals|null
     */
    protected static function getPatientGoal(ManagerRegistry $doctrine, String $serviceName, float $serviceGoal, Patient $patient)
    {
        /** @var PatientGoals $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(PatientGoals::class)->findOneBy(['entity' => $serviceName]);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            $entityManager = $doctrine->getManager();
            $thirdPartyService = new PatientGoals();
            $thirdPartyService->setPatient($patient);
            $thirdPartyService->setGoal($serviceGoal);
            $thirdPartyService->setEntity($serviceName);
            $thirdPartyService->setDateSet(new DateTime());

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

}