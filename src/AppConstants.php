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


use App\Entity\ThirdPartyService;
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
        $get_class = get_class($entity);
        switch ($get_class) {
            case "App\Entity\Patient":
                return json_encode(["email" => $entity->getEmail()]);
                break;
            case "App\Entity\PatientMembership":
                return json_encode(["~patient" => $entity->getPatient()->getEmail()]);
                break;
            case "App\Entity\ThirdPartyService":
                return json_encode(["name" => $entity->getName()]);
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
                                if (substr(get_class($holdValue), 0, strlen("App\Entity\\")) === "App\Entity\\") {
                                    $returnString[$methodValue] = "@" . get_class($holdValue) . "|" . self::findIdMethod($holdValue);
                                } else {
                                    $returnString[$methodValue] = "#" . get_class($holdValue);
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
