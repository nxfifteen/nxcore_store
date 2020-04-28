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
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\CommsManager;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class Transform
 *
 * @package App\Transform
 */
class Transform
{
    /** @var ManagerRegistry */
    private $doctrine;

    /** @var LoggerInterface */
    private $logger;

    /** @var AwardManager */
    private $awardManager;

    /** @var ChallengePve */
    private $challengePve;

    /** @var CommsManager */
    private $commsManager;

    /** @var Patient */
    private $patient;

    /**
     * BodyWeight constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param LoggerInterface $logger
     * @param AwardManager    $awardManager
     * @param ChallengePve    $challengePve
     * @param CommsManager    $commsManager
     */
    public function __construct(
        ManagerRegistry $doctrine,
        LoggerInterface $logger,
        AwardManager $awardManager,
        ChallengePve $challengePve,
        CommsManager $commsManager
    ) {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->awardManager = $awardManager;
        $this->challengePve = $challengePve;
        $this->commsManager = $commsManager;
    }

    /**
     * @param String $getContent
     *
     * @return mixed
     */
    protected static function decodeJson(string $getContent)
    {
        $jsonObject = json_decode($getContent, false);
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
     * @param $getContent
     *
     * @return mixed
     */
    protected static function encodeJson($getContent)
    {
        $jsonObject = json_encode($getContent);
        return $jsonObject;
    }

    /**
     * @return AwardManager
     */
    protected function getAwardManager(): AwardManager
    {
        return $this->awardManager;
    }

    /**
     * @return ChallengePve
     */
    protected function getChallengePve(): ChallengePve
    {
        return $this->challengePve;
    }

    /**
     * @return CommsManager
     */
    protected function getCommsManager(): CommsManager
    {
        return $this->commsManager;
    }

    /**
     * @return ManagerRegistry
     */
    protected function getDoctrine(): ManagerRegistry
    {
        return $this->doctrine;
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return ExerciseType|null
     */
    protected static function getExerciseType(ManagerRegistry $doctrine, string $serviceName)
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
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $food_info_id
     *
     * @return FoodDatabase|null
     */
    protected static function getMealFoodItem(ManagerRegistry $doctrine, string $food_info_id)
    {
        /** @var FoodDatabase $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(FoodDatabase::class)->findByFoodInfoId($food_info_id);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            return null;
        }
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return FoodMeals|null
     */
    protected static function getMealType(ManagerRegistry $doctrine, string $serviceName)
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
        } else {
            if ($trackedDate >= 17 && $trackedDate <= 20) {
                $partOfDayString = "evening";
            } else {
                if ($trackedDate >= 5 && $trackedDate <= 11) {
                    $partOfDayString = "morning";
                } else {
                    $partOfDayString = "night";
                }
            }
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
     * @param ManagerRegistry $doctrine
     * @param                 $uuid
     *
     * @return Patient|null
     */
    protected static function getPatient(ManagerRegistry $doctrine, string $uuid)
    {
        /** @var Patient $patient */
        $patient = $doctrine->getRepository(Patient::class)->findOneBy(['uuid' => $uuid]);
        if ($patient) {
            return $patient;
        }

        return null;
    }

    /**
     * @return Patient
     */
    protected function getPatientEntity(): Patient
    {
        return $this->patient;
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
    protected static function getPatientGoal(
        ManagerRegistry $doctrine,
        string $serviceName,
        float $serviceGoal,
        $unitOfMeasurement,
        Patient $patient,
        bool $matchGoal = null
    ) {
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
    protected static function getThirdPartyService(ManagerRegistry $doctrine, string $serviceName)
    {
        /** @var ThirdPartyService $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(ThirdPartyService::class)->findOneBy(['name' => $serviceName]);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            $entityManager = $doctrine->getManager();
            $thirdPartyService = new ThirdPartyService();
            $safeGuid = false;
            $i = 0;
            while ($safeGuid == false) {
                $i++;
                AppConstants::writeToLog('debug_transform.txt', 'Added a GUID (' . $i . ')');
                $thirdPartyService->createGuid();
                $dataEntryGuidCheck = $doctrine
                    ->getRepository(ThirdPartyService::class)
                    ->findByGuid($thirdPartyService->getGuid());
                if (empty($dataEntryGuidCheck)) {
                    $safeGuid = true;
                }
            }
            $thirdPartyService->setName($serviceName);
            $entityManager->persist($thirdPartyService);
            $entityManager->flush();

            return $thirdPartyService;
        }
    }

    /**
     * @param ManagerRegistry   $doctrine
     * @param Patient           $patient
     * @param ThirdPartyService $thirdPartyService
     * @param String            $deviceName
     *
     * @param array             $options
     *
     * @return TrackingDevice|null
     * @deprecated use AppConstants::getTrackingDevice instead
     *
     */
    protected static function getTrackingDevice(
        ManagerRegistry $doctrine,
        Patient $patient,
        ThirdPartyService $thirdPartyService,
        string $deviceName,
        array $options = []
    ) {
        return AppConstants::getTrackingDevice($doctrine, $patient, $thirdPartyService, $deviceName, $options);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return UnitOfMeasurement|null
     */
    protected static function getUnitOfMeasurement(ManagerRegistry $doctrine, string $serviceName)
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
     * @param $logMessage
     */
    protected function log($logMessage)
    {
        if (!is_string($logMessage)) {
            AppConstants::writeToLog(
                'debug_transform.txt',
                "[Transform] - " . print_r($logMessage, true)
            );
            echo "[Transform] - " . print_r($logMessage, true) . "\n";
        } else {
            AppConstants::writeToLog(
                'debug_transform.txt',
                "[Transform] - " . $logMessage
            );
            echo "[Transform] - " . $logMessage . "\n";
        }
    }

    /**
     * @param                   $entityTargetClass
     * @param                   $entityDataArray
     *
     * @param DateInterval|null $interval
     */
    protected function saveEntityFromArray($entityTargetClass, $entityDataArray, DateInterval $interval = null)
    {
        if (class_exists($entityTargetClass)) {
            $dataEntry = $this->getDoctrine()->getRepository($entityTargetClass)->findOneBy($entityDataArray['searchFields']);
            if (!$dataEntry) {
                if ($entityDataArray['DateTime']->format("Y-m-d") == date("Y-m-d")) {
                    $newItem = true;
                }
                $dataEntry = new $entityTargetClass();
            }

            foreach ($entityDataArray as $entityDataKey => $entityDataItem) {
                if ($entityDataKey != "searchFields" && $entityDataKey != "thirdPartyService") {
                    $targetMethodName = "set" . $entityDataKey;
                    if (method_exists($dataEntry, $targetMethodName)) {
                        $dataEntry->$targetMethodName($entityDataItem);
                    } else {
                        $this->log("There is no method called " . $targetMethodName);
                    }
                }
            }

            self::updateApi($this->getDoctrine(), str_ireplace("App\Entity\\", "", $entityTargetClass),
                $entityDataArray['Patient'],
                $entityDataArray['thirdPartyService'], $dataEntry->getDateTime(), $interval);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dataEntry);
            $entityManager->flush();
        } else {
            $this->log("Unable to find the " . $entityTargetClass . " class");
        }
    }

    /**
     * @param $string
     * @param $startString
     *
     * @return bool
     * @deprecated User AppConstants::startsWith instead
     *
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
     * @param DateInterval|null $interval
     *
     * @return ApiAccessLog
     */
    protected static function updateApi(
        ManagerRegistry $doctrine,
        string $fullClassName,
        Patient $patient,
        ThirdPartyService $service,
        DateTimeInterface $dateTime,
        DateInterval $interval = null
    ) {
        //AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - translateEntity class type is : " . $fullClassName);
        /** @var ApiAccessLog $dataEntry */
        $dataEntry = $doctrine->getRepository(ApiAccessLog::class)->findOneBy([
            'entity' => $fullClassName,
            'patient' => $patient,
            'thirdPartyService' => $service,
        ]);
        if (!$dataEntry) {
            $dataEntry = new ApiAccessLog();
        }

        $dataEntry->setPatient($patient);
        $dataEntry->setThirdPartyService($service);
        $dataEntry->setEntity($fullClassName);
        $dataEntry->setLastRetrieved($dateTime);
        $dataEntry->setLastPulled(new DateTime());
        if (is_null($interval)) {
            $interval = new DateInterval('PT40M');
        }
        $dataEntry->setCooldown((new DateTime())->add($interval));

        $entityManager = $doctrine->getManager();
        $entityManager->persist($dataEntry);
        $entityManager->flush();

        return $dataEntry;
    }

    public function setPatientEntity(Patient $patient)
    {
        $this->patient = $patient;
    }
}
