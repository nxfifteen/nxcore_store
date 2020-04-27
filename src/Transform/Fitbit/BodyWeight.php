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

namespace App\Transform\Fitbit;


use App\Entity\ThirdPartyService;
use App\Transform\Base\BaseBodyMeasurments;
use DateTime;
use Exception;

class BodyWeight extends BaseBodyMeasurments
{
    const ENDPOINT_UNKNOWN = 0;
    const ENDPOINT_STANDARD = 1;
    const ENDPOINT_TIMESERIES = 2;
    /** @var array */
    private $rawApiData;
    /** @var string */
    private $apiUrl;
    /** @var string */
    private $apiDataType;
    /** @var ThirdPartyService */
    private $thirdPartyService;

    /**
     * @return array[]
     * @throws Exception
     */
    private function convertForDuelData()
    {
        $recordSetWeight = [];
        $recordSetFat = [];
        foreach ($this->rawApiData as $rawApiDatum) {
            $dateTime = new DateTime($rawApiDatum->date . ' ' . $rawApiDatum->time);
            $partOfDay = self::getPartOfDay($this->getDoctrine(), $dateTime);
            if (array_key_exists("source", $rawApiDatum)) {
                $trackingDevice = self::getTrackingDevice($this->getDoctrine(), $this->getPatientEntity(),
                    $this->thirdPartyService, $rawApiDatum->source);
            } else {
                $trackingDevice = self::getTrackingDevice($this->getDoctrine(), $this->getPatientEntity(),
                    $this->thirdPartyService, "History");
            }
            $uomKg = self::getUnitOfMeasurement($this->getDoctrine(), "kg");
            $uomPercentage = self::getUnitOfMeasurement($this->getDoctrine(), "%");

            if (array_key_exists("weight", $rawApiDatum)) {
                $recordSetWeight[] = [
                    "searchFields" => [
                        'RemoteId' => $rawApiDatum->logId,
                        'patient' => $this->getPatientEntity(),
                        'trackingDevice' => $trackingDevice,
                    ],
                    "DateTime" => $dateTime,
                    "Measurement" => $rawApiDatum->weight,
                    "PartOfDay" => $partOfDay,
                    "Patient" => $this->getPatientEntity(),
                    "PatientGoal" => self::getPatientGoal($this->getDoctrine(), "BodyWeight", 70.32, $uomKg,
                        $this->getPatientEntity()),
                    "RemoteId" => $rawApiDatum->logId,
                    "TrackingDevice" => $trackingDevice,
                    "UnitOfMeasurement" => $uomKg,
                    "thirdPartyService" => $this->thirdPartyService,
                ];
            }

            if (array_key_exists("fat", $rawApiDatum)) {
                $recordSetFat[] = [
                    "searchFields" => [
                        'RemoteId' => $rawApiDatum->logId,
                        'patient' => $this->getPatientEntity(),
                        'trackingDevice' => $trackingDevice,
                    ],
                    "DateTime" => $dateTime,
                    "BodyFatMass" => null,
                    "FatFree" => null,
                    "FatFreeMass" => null,
                    "Measurement" => $rawApiDatum->fat,
                    "PartOfDay" => $partOfDay,
                    "Patient" => $this->getPatientEntity(),
                    "PatientGoal" => self::getPatientGoal($this->getDoctrine(), "BodyFat", 21, $uomPercentage,
                        $this->getPatientEntity()),
                    "RemoteId" => $rawApiDatum->logId,
                    "TrackingDevice" => $trackingDevice,
                    "UnitOfMeasurement" => $uomPercentage,
                    "thirdPartyService" => $this->thirdPartyService,
                ];
            }
        }

        return [$recordSetWeight, $recordSetFat];
    }

    private function whichDataDoWeHave()
    {
        if (strpos($this->apiUrl, "https://api.fitbit.com/1/user/-/body/log/") !== false) {
            $this->apiDataType = BodyWeight::ENDPOINT_STANDARD;
        } else {
            if (strpos($this->apiUrl, "https://api.fitbit.com/1/user/-/body/") !== false) {
                $this->apiDataType = BodyWeight::ENDPOINT_TIMESERIES;
            } else {
                $this->apiDataType = BodyWeight::ENDPOINT_UNKNOWN;
            }
        }
    }

    /**
     * @return string|null
     * @noinspection PhpUnused
     */
    public function processData()
    {
        $this->thirdPartyService = self::getThirdPartyService($this->getDoctrine(), Constants::FITBITSERVICE);

        if ($this->apiDataType == BodyWeight::ENDPOINT_STANDARD) {
            try {
                [$recordSetWeights, $recordSetFats] = $this->convertForDuelData();
                if (count($recordSetWeights) == 0) {
                    return "nill";
                }

                $i = 1;
                $this->log(" Creating " . count($recordSetWeights) . " new BodyWeight entity");
                foreach ($recordSetWeights as $recordSetWeight) {
                    $this->saveEntityFromArray("App\Entity\BodyWeight", $recordSetWeight);
                }

                $i = 1;
                $this->log(" Creating " . count($recordSetFats) . " new BodyFat entity");
                foreach ($recordSetFats as $recordSetFat) {
                    $this->saveEntityFromArray("App\Entity\BodyFat", $recordSetFat);
                }

                $this->log(" Last date was " . $recordSetWeights[0]['DateTime']->format("Y-m-d H:i:s"));
                return $recordSetWeights[0]['DateTime']->format("Y-m-d H:i:s");
            } catch (Exception $e) {
                $this->log(__LINE__ . $e->getMessage());
            }
        } else {
            $this->log($this->rawApiData[0]);
        }

        return null;
    }

    /**
     * @param $apiEndPointResult
     *
     * @noinspection PhpUnused
     */
    public function setApiReturn($apiEndPointResult)
    {
        $this->rawApiData = $apiEndPointResult->weight;
    }

    /**
     * @param $apiEndPointCalled
     *
     * @noinspection PhpUnused
     */
    public function setCalledUrl($apiEndPointCalled)
    {
        $this->apiUrl = $apiEndPointCalled;
        $this->whichDataDoWeHave();
    }
}
