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

namespace App\Transform\SamsungHealth;


use App\AppConstants;
use App\Entity\FitFloorsIntraDay;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Service\AwardManager;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class SamsungIntraDayFloors
 *
 * @package App\Transform\SamsungHealth
 */
class SamsungIntraDayFloors extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return FitFloorsIntraDay|null
     * @throws \Exception
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {
            //AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - New call too FitFloorsIntraDay for " . $jsonContent->remoteId);

            try {
                $jsonContent->dateTime = new DateTime($jsonContent->dateTime);
                $jsonContent->dateTimeEnd = new DateTime($jsonContent->dateTimeEnd);
                $jsonContent->dateTimeOffset = new DateTime($jsonContent->dateTimeOffset);
                $jsonContent->dateRaw = $jsonContent->dateTime;
            } catch (Exception $e) {
                return NULL;
            }

            $timeDiff = $jsonContent->dateTimeOffset->format("G");
            if ($timeDiff > 0) {
                $jsonContent->dateTime->modify('+ ' . $timeDiff . ' hour');
                $jsonContent->dateTimeEnd->modify('+ ' . $timeDiff . ' hour');
            } else if ($timeDiff < 0) {
                $jsonContent->dateTime->modify('- ' . $timeDiff . ' hour');
                $jsonContent->dateTimeEnd->modify('- ' . $timeDiff . ' hour');
            }

            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent->uuid);
            if (is_null($patient)) {
                return NULL;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::SAMSUNGHEALTHSERVICE);
            if (is_null($thirdPartyService)) {
                return NULL;
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $jsonContent->device);
            if (is_null($deviceTracking)) {
                return NULL;
            }

            /** @var FitFloorsIntraDay $dataEntry */
            $dataEntry = $doctrine->getRepository(FitFloorsIntraDay::class)->findOneBy(['RemoteId' => $jsonContent->remoteId, 'patient' => $patient, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntry) {
                $dataEntry = new FitFloorsIntraDay();
            }

            if (property_exists($jsonContent, "goal")) {
                /** @var PatientGoals $patientGoal */
                self::getPatientGoal($doctrine, "FloorsDaily", $jsonContent->goal, null, $patient);
            }

            $dataEntry->setPatient($patient);
            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent->remoteId);
            $dataEntry->setValue($jsonContent->value);
            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> $jsonContent->dateTime->format("U")) {
                $dataEntry->setDateTime($jsonContent->dateTime);
            }
            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntry)), $patient, $thirdPartyService, $dataEntry->getDateTime());

            return $dataEntry;

        }

        return NULL;
    }
}
