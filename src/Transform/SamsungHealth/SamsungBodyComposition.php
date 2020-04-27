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
use App\Entity\BodyComposition;
use App\Entity\PartOfDay;
use App\Entity\Patient;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Service\AwardManager;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class SamsungBodyComposition
 *
 * @package App\Transform\SamsungHealth
 */
class SamsungBodyComposition extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return BodyComposition|null
     * @throws Exception
     */
    public static function translate(ManagerRegistry $doctrine, string $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid") &&
            ($jsonContent->skeletal_muscle > 0 ||
                $jsonContent->muscle_mass > 0 ||
                $jsonContent->basal_metabolic_rate > 0 ||
                $jsonContent->skeletal_muscle_mass > 0 ||
                $jsonContent->total_body_water > 0)) {
            ///AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - New call too BodyComposition for " . $jsonContent->remoteId);

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

            /** @var BodyComposition $dataEntry */
            $dataEntry = $doctrine->getRepository(BodyComposition::class)->findOneBy([
                'RemoteId' => $jsonContent->remoteId,
                'patient' => $patient,
                'trackingDevice' => $deviceTracking,
            ]);
            if (!$dataEntry) {
                $dataEntry = new BodyComposition();
                $safeGuid = false;
                $i = 0;
                do {
                    $i++;
                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': Added a GUID (' . $i . ')');
                    $dataEntry->createGuid(true);
                    AppConstants::writeToLog('debug_transform.txt',
                        __FILE__ . '@' . __LINE__ . ': ' . $dataEntry->getGuid()->toString());
                    $dataEntryGuidCheck = $doctrine
                        ->getRepository(BodyComposition::class)
                        ->findByGuid($dataEntry->getGuid());
                    if (empty($dataEntryGuidCheck) || (is_array($dataEntryGuidCheck) && count($dataEntryGuidCheck) == 0)) {
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

            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> (new DateTime($jsonContent->dateTime))->format("U")) {
                $dataEntry->setDateTime(new DateTime($jsonContent->dateTime));
            }
            $dataEntry->setPartOfDay($partOfDay);
            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            $dataEntry->setBasalMetabolicRate($jsonContent->basal_metabolic_rate);
            $dataEntry->setMuscleMass($jsonContent->muscle_mass);
            $dataEntry->setSkeletalMuscle($jsonContent->skeletal_muscle);
            $dataEntry->setSkeletalMuscleMass($jsonContent->skeletal_muscle_mass);
            $dataEntry->setTotalBodyWater($jsonContent->total_body_water);

            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntry)), $patient,
                $thirdPartyService, $dataEntry->getDateTime());

            return $dataEntry;

        }

        return null;
    }
}
