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

namespace App\Transform\Fitbit;

use App\Entity\FitStepsDailySummary;
use App\Entity\Patient;
use App\Entity\PatientGoals;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class FitbitCountPeriodSteps
 *
 * @package App\Transform\Fitbit
 */
class FitbitCountPeriodSteps extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param array           $jsonContent
     *
     * @return FitStepsDailySummary|FitStepsDailySummary[]|null
     * @throws Exception
     */
    public static function translate(ManagerRegistry $doctrine, array $jsonContent)
    {
        if (array_key_exists("uuid", $jsonContent[0])) {

            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent[0]['uuid']);
            if (is_null($patient)) {
                return null;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::FITBITSERVICE);
            if (is_null($thirdPartyService)) {
                return null;
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService,
                $jsonContent[0]['tracker']);
            if (is_null($deviceTracking)) {
                return null;
            }

            /** @var PatientGoals $patientGoal */
            $patientGoal = self::getPatientGoal($doctrine, "FitStepsDailySummary", 10000, null, $patient, false);
            if (is_null($patientGoal)) {
                return null;
            }

            $returnEntities = [];
            foreach ($jsonContent[1] as $item) {
                if ($item['dateTime'] != date("Y-m-d")) {

                    $item['remoteId'] = sha1($patient->getId() .
                            $thirdPartyService->getId() .
                            $thirdPartyService->getName()) .
                        'FitStepsDailySummary' .
                        $item['dateTime'];

                    /** @var FitStepsDailySummary $dataEntry */
                    $dataEntry = $doctrine->getRepository(FitStepsDailySummary::class)->findOneBy([
                        'RemoteId' => $item['remoteId'],
                        'patient' => $patient,
                    ]);

                    if (!$dataEntry) {
                        $dataEntry = new FitStepsDailySummary();
                        $dataEntry->setGoal($patientGoal);

                        $dataEntry->setPatient($patient);
                        $dataEntry->setTrackingDevice($deviceTracking);
                        $dataEntry->setRemoteId($item['remoteId']);
                        $dataEntry->setValue($item['value']);
                        if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> strtotime($item['dateTime'] . " 23:59:59")) {
                            $dataEntry->setDateTime(new DateTime($item['dateTime'] . " 23:59:59"));
                        }

                        $returnEntities[] = $dataEntry;
                    }
                }
            }

            return $returnEntities;

        }
        return null;
    }
}
