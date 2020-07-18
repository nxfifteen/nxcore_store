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

namespace App;


use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AppConstants
 *
 * @package App
 */
class AppConstants
{
    const LOG_PROGRESSION_NONE = 0;
    const LOG_PROGRESSION_START = 1;
    const LOG_PROGRESSION_CONTINUE = 2;
    const LOG_PROGRESSION_STOP = 3;

    static function findIdMethod($entity)
    {
        $get_class = str_ireplace("Proxies\__CG__\\", "", get_class($entity));
        switch ($get_class) {
            case "App\Entity\Patient":
                return json_encode(["email" => $entity->getEmail()]);
                break;
            case "App\Entity\ApiAccessLog":
                return json_encode([
                    "patient" => sprintf('@App\Entity\Patient|{"email":"%s"}',
                        $entity->getPatient()->getEmail()),
                    "thirdPartyService" => sprintf('@App\Entity\ThirdPartyService|{"name":"%s"}',
                        $entity->getThirdPartyService()->getName()),
                    "entity" => $entity->getEntity(),
                ]);
                break;
            case "App\Entity\BodyFat":
            case "App\Entity\BodyWeight":
                return json_encode([
                    "patient" => sprintf('@App\Entity\Patient|{"email":"%s"}',
                        $entity->getPatient()->getEmail()),
                    "DateTime" => '%DateTime%' . $entity->getDateTime()->format("U"),
                    "trackingDevice" => '@App\Entity\TrackingDevice|' . json_encode([
                            "patient" => sprintf('@App\Entity\Patient|{"email":"%s"}',
                                $entity->getPatient()->getEmail()),
                            "service" => sprintf('@App\Entity\ThirdPartyService|{"name":"%s"}',
                                $entity->getTrackingDevice()->getService()->getName()),
                            "remoteId" => $entity->getTrackingDevice()->getName(),
                        ]),
                ]);
                break;
            case "App\Entity\PatientMembership":
                return json_encode([
                    "patient" => sprintf('@App\Entity\Patient|{"email":"%s"}',
                        $entity->getPatient()->getEmail()),
                ]);
                break;
            case "App\Entity\UnitOfMeasurement":
            case "App\Entity\ThirdPartyService":
            case "App\Entity\PartOfDay":
                return json_encode(["name" => $entity->getName()]);
                break;
            case "App\Entity\PatientFriends":
                return json_encode([
                    "friendA" => sprintf('@App\Entity\Patient|{"email":"%s"}',
                        $entity->getFriendA()->getEmail()),
                    "friendB" => sprintf('@App\Entity\Patient|{"email":"%s"}',
                        $entity->getFriendB()->getEmail()),
                ]);
                break;
            case "App\Entity\PatientGoals":
                return json_encode([
                    "patient" => sprintf('@App\Entity\Patient|{"email":"%s"}',
                        $entity->getPatient()->getEmail()),
                    "goal" => $entity->getGoal(),
                    "entity" => $entity->getEntity(),
                    "unitOfMeasurement" => sprintf('@App\Entity\UnitOfMeasurement|{"name":"%s"}',
                        $entity->getUnitOfMeasurement()->getName()),
                ]);
                break;
            case "App\Entity\PatientSettings":
                return json_encode([
                    "patient" => sprintf('@App\Entity\Patient|{"email":"%s"}',
                        $entity->getPatient()->getEmail()),
                    "service" => sprintf('@App\Entity\ThirdPartyService|{"name":"%s"}',
                        $entity->getService()->getName()),
                    "name" => $entity->getName(),
                ]);
                break;
            case "App\Entity\TrackingDevice":
                return json_encode([
                    "patient" => sprintf('@App\Entity\Patient|{"email":"%s"}',
                        $entity->getPatient()->getEmail()),
                    "service" => sprintf('@App\Entity\ThirdPartyService|{"name":"%s"}',
                        $entity->getService()->getName()),
                    "remoteId" => $entity->getRemoteId(),
                ]);
                break;
        }

        return json_encode(["guid" => $entity->getGuid()]);
    }

    /**
     * @param $stringToCompress
     *
     * @return string
     */
    static function compressString($stringToCompress)
    {
        $compressedString = "\x1f\x8b\x08\x00" . gzcompress($stringToCompress);

        return $compressedString;
    }

    /**
     * @param $value
     * @param $valueUnit
     * @param $targetUnit
     *
     * @return float|int
     */
    static function convertUnitOfMeasurement($value, $valueUnit, $targetUnit)
    {
        if ($valueUnit == "mile" && $targetUnit == "meter") {
            return $value * 1609.34;
        } else {
            if ($valueUnit == "meter" && $targetUnit == "mile") {
                return $value / 1609.34;
            } else {
                if ($valueUnit == "meter" && $targetUnit == "km") {
                    return $value / 1000;
                }
            }
        }

        return 0.5;
    }

    /**
     * @param      $seconds
     * @param bool $withHours
     *
     * @return string
     */
    static function formatSeconds($seconds, bool $withHours = true)
    {
        $hours = 0;
        $milliseconds = str_replace("0.", '', $seconds - floor($seconds));

        if ($seconds > 3600) {
            $hours = floor($seconds / 3600);
        }
        $seconds = $seconds % 3600;

        if ($withHours) {
            return str_pad($hours, 2, '0', STR_PAD_LEFT)
                . gmdate(':i:s', $seconds)
                . ($milliseconds ? ".$milliseconds" : '');
        } else {
            return gmdate('i:s', $seconds)
                . ($milliseconds ? ".$milliseconds" : '');
        }
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return ThirdPartyService|null
     */
    static function getThirdPartyService(ManagerRegistry $doctrine, string $serviceName)
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
    static function getPatientGoal(
        ManagerRegistry $doctrine,
        string $serviceName,
        float $serviceGoal,
        $unitOfMeasurement,
        Patient $patient,
        bool $matchGoal = null
    ) {
        $serviceGoal = floatval($serviceGoal);

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
     * @param ManagerRegistry   $doctrine
     * @param Patient           $patient
     * @param ThirdPartyService $thirdPartyService
     * @param String            $deviceName
     *
     * @param array             $options
     *
     * @return TrackingDevice|null
     */
    public static function getTrackingDevice(
        ManagerRegistry $doctrine,
        Patient $patient,
        ThirdPartyService $thirdPartyService,
        string $deviceName,
        array $options = []
    ) {
        /** @var TrackingDevice $deviceTracking */
        $deviceTracking = $doctrine->getRepository(TrackingDevice::class)->findOneBy([
            'remoteId' => $deviceName,
            'patient' => $patient,
            'service' => $thirdPartyService,
        ]);
        if ($deviceTracking) {
            return $deviceTracking;
        } else {
            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = $doctrine->getRepository(TrackingDevice::class)->findOneBy([
                'name' => $deviceName,
                'patient' => $patient,
                'service' => $thirdPartyService,
            ]);
            if ($deviceTracking) {
                return $deviceTracking;
            } else {
                $entityManager = $doctrine->getManager();
                $deviceTracking = new TrackingDevice();
                $safeGuid = false;
                $i = 0;
                while ($safeGuid == false) {
                    $i++;
                    AppConstants::writeToLog('debug_transform.txt', 'Added a GUID (' . $i . ')');
                    $deviceTracking->createGuid();
                    $dataEntryGuidCheck = $doctrine
                        ->getRepository(TrackingDevice::class)
                        ->findByGuid($deviceTracking->getGuid());
                    if (empty($dataEntryGuidCheck)) {
                        $safeGuid = true;
                    }
                }
                $deviceTracking->setPatient($patient);
                $deviceTracking->setService($thirdPartyService);
                $deviceTracking->setRemoteId($deviceName);
                $deviceTracking->setName($deviceName);
                $deviceTracking->setType("Unknown");

                if (count($options) > 0) {
                    if (array_key_exists("name", $options)) {
                        $deviceTracking->setName($options['name']);
                    }
                    if (array_key_exists("comment", $options)) {
                        $deviceTracking->setComment($options['comment']);
                    }
                    if (array_key_exists("battery", $options)) {
                        $deviceTracking->setBattery($options['battery']);
                    }
                    if (array_key_exists("type", $options)) {
                        $deviceTracking->setType($options['type']);
                    }
                    if (array_key_exists("manufacturer", $options)) {
                        $deviceTracking->setManufacturer($options['manufacturer']);
                    }
                    if (array_key_exists("model", $options)) {
                        $deviceTracking->setModel($options['model']);
                    }
                }

                $entityManager->persist($deviceTracking);
                $entityManager->flush();

                return $deviceTracking;
            }
        }
    }

    /**
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    static function startsWith($haystack, $needle)
    {
        $len = strlen($needle);
        return (substr($haystack, 0, $len) === $needle);
    }

    /**
     * Helper method to create json string from entiry
     *
     * @param $inputEntity
     *
     * @return string
     */
    static function toJson($inputEntity)
    {
        $pirvateMethods = [
            "getId",
            "getRemoteId",
            "getPassword",
            "getSalt",
            "getApiToken",
        ];

        $returnString = [];
        foreach (get_class_methods($inputEntity) as $classMethod) {
            unset($holdValue);
            if (substr($classMethod, 0, 3) === "get" && !in_array($classMethod, $pirvateMethods)) {
                $methodValue = str_ireplace("get", "", $classMethod);
                $holdValue = $inputEntity->$classMethod();
                switch (gettype($holdValue)) {
                    case "string":
                    case "boolean":
                    case "integer":
                    case "double":
                        $returnString[$methodValue] = $holdValue;
                        break;
                    case "array":
                        $returnString[$methodValue] = json_encode($holdValue);
                        break;
                    case "object":
                        switch (get_class($holdValue)) {
                            case "DateTime":
                                $returnString[$methodValue] = "%DateTime%" . $holdValue->format("U");
                                break;
                            case "Doctrine\ORM\PersistentCollection":
                            case "App\Entity\PatientMembership":
                                //
                                break;
                            case "Ramsey\\Uuid\\Uuid":
                                /** @var $holdValue UuidInterface */
                                $returnString[$methodValue] = $holdValue->toString();
                                break;
                            default:
                                $holdValueClass = str_ireplace("Proxies\__CG__\\", "", get_class($holdValue));
                                if (substr($holdValueClass, 0, strlen("App\Entity\\")) === "App\Entity\\") {
                                    $returnString[$methodValue] = "@" . $holdValueClass . "|" . self::findIdMethod($holdValue);
                                } else {
                                    $returnString[$methodValue] = "#" . $holdValueClass;
                                }
                                break;
                        }
                        break;
                    case "NULL":
                        //
                        break;
                    default:
                        $returnString[$methodValue] = gettype($holdValue);
                        break;
                }
            }
        }

        return json_encode($returnString);
    }

    /**
     * @param $stringToUncompress
     *
     * @return false|string
     */
    static function uncompressString($stringToUncompress)
    {
        $uncompressedString = gzuncompress(substr($stringToUncompress, 4));

        return $uncompressedString;
    }

    /**
     * @param String      $fileName
     * @param String|null $body
     * @param int         $progession
     */
    static function writeToLog(string $fileName, ?string $body, int $progession = self::LOG_PROGRESSION_NONE)
    {
        try {
            $path = dirname(__FILE__) . '/../var/log';
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }

        if (!empty($path)) {
            $file = $path . '/' . $fileName;

            $fileSystem = new Filesystem();
            try {
                $fileSystem->mkdir($path);
                if ($fileSystem->exists($file)) {
                    if ($progession == self::LOG_PROGRESSION_NONE) {
                        $fileSystem->appendToFile($file, date("Y-m-d H:i:s") . ":: " . $body . "\n");
                    } else {
                        if ($progession == self::LOG_PROGRESSION_START) {
                            $fileSystem->appendToFile($file, date("Y-m-d H:i:s") . ":: " . $body . " ");
                        } else {
                            if ($progession == self::LOG_PROGRESSION_CONTINUE) {
                                $fileSystem->appendToFile($file, ".");
                            } else {
                                if ($progession == self::LOG_PROGRESSION_STOP) {
                                    $fileSystem->appendToFile($file, "\n");
                                }
                            }
                        }
                    }
                } else {
                    if ($progession == self::LOG_PROGRESSION_NONE) {
                        $fileSystem->dumpFile($file, date("Y-m-d H:i:s") . ":: " . $body . "\n");
                    } else {
                        if ($progession == self::LOG_PROGRESSION_START) {
                            $fileSystem->dumpFile($file, date("Y-m-d H:i:s") . ":: " . $body . "\n");
                        } else {
                            if ($progession == self::LOG_PROGRESSION_CONTINUE) {
                                $fileSystem->dumpFile($file, ".");
                            } else {
                                if ($progession == self::LOG_PROGRESSION_STOP) {
                                    $fileSystem->dumpFile($file, " [DONE]\n");
                                }
                            }
                        }
                    }
                }

            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at " . $exception->getPath();
            }
        }
    }
}
