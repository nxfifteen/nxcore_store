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

namespace App;


use App\Entity\Patient;
use App\Entity\ThirdPartyService;
use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AppConstants
 *
 * @package App
 */
class AppConstants
{
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
     * @param String $fileName
     * @param String $body
     */
    static function writeToLog(string $fileName, string $body)
    {
        try {
            $path = sys_get_temp_dir() . '/sync_upload_post';
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }

        if (!empty($path)) {
            $file = $path . '/' . $fileName;

            $fileSystem = new Filesystem();
            try {
                $fileSystem->mkdir($path);
                if ($fileSystem->exists($file)) {
                    $fileSystem->appendToFile($file, date("Y-m-d H:i:s") . ":: " . $body . "\n");
                } else {
                    $fileSystem->dumpFile($file, date("Y-m-d H:i:s") . ":: " . $body . "\n");
                }

            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at " . $exception->getPath();
            }
        }
    }
}
