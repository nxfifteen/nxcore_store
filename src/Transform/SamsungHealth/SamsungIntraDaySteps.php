<?php

namespace App\Transform\SamsungHealth;


use App\AppConstants;
use App\Entity\FitStepsIntraDay;
use App\Entity\Patient;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Service\AwardManager;
use Doctrine\Common\Persistence\ManagerRegistry;

class SamsungIntraDaySteps extends Constants
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return FitStepsIntraDay|null
     * @throws \Exception
     */
    public static function translate(ManagerRegistry $doctrine, String $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
        // AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {
            try {
                $jsonContent->date = new \DateTime($jsonContent->date);
                $jsonContent->dateRaw = $jsonContent->date;
            } catch (\Exception $e) {
                return NULL;
            }

            $timeDiff = 1;
            if ($timeDiff > 0) {
                $jsonContent->date->modify('+ ' . $timeDiff . ' hour');
                $jsonContent->hour = $jsonContent->hour + $timeDiff;
            } else if ($timeDiff < 0) {
                $jsonContent->date->modify('- ' . $timeDiff . ' hour');
                $jsonContent->hour = $jsonContent->hour - $timeDiff;
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

            $jsonContent->remoteId = sha1(
                $thirdPartyService->getId() .
                $thirdPartyService->getName() .
                $patient->getUuid() .
                $deviceTracking->getRemoteId() .
                $jsonContent->date->format("Y-m-d H:i")
            );

            /** @var FitStepsIntraDay $dataEntry */
            $dataEntry = $doctrine->getRepository(FitStepsIntraDay::class)->findOneBy(['RemoteId' => $jsonContent->remoteId, 'patient' => $patient, 'trackingDevice' => $deviceTracking]);
            if (!$dataEntry) {
                $dataEntry = new FitStepsIntraDay();
            }

            $dataEntry->setPatient($patient);
            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setRemoteId($jsonContent->remoteId);
            $dataEntry->setValue($jsonContent->value);
            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> $jsonContent->date->format("U")) {
                $dataEntry->setDateTime($jsonContent->date);
            }
            $dataEntry->setHour($dataEntry->getDateTime()->format("H"));
            if (property_exists($jsonContent, "duration")) $dataEntry->setDuration($jsonContent->duration);
            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            self::updateApi($doctrine, str_ireplace("App\\Entity\\", "", get_class($dataEntry)), $patient, $thirdPartyService, $dataEntry->getDateTime());

            return $dataEntry;

        }

        return NULL;
    }
}