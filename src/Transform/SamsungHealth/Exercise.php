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

namespace App\Transform\SamsungHealth;


use App\AppConstants;
use App\Transform\Base\BaseExercise;
use DateTime;

class Exercise extends BaseExercise
{
    /**
     * @return string|null
     * @noinspection PhpUnused
     */
    public function processData()
    {
        $this->thirdPartyService = self::getThirdPartyService($this->getDoctrine(),
            CommonSamsung::SAMSUNGHEALTHSERVICE);
        $dataEntry = $this->saveEntityFromArray("App\Entity\Exercise", $this->prepFromJson());
        if (!is_null($dataEntry)) {
            $this->saveEntityFromArray("App\Entity\ExerciseSummary", $this->prepSummaryFromJson($dataEntry));
        }

        return null;
    }

    private function prepFromJson()
    {
        $returnData = [];
        foreach ($this->rawApiData as $key => $rawApiDatum) {
            $keyTranslation = $this->inputKeyToEntityKey($key);
            if (!is_null($keyTranslation)) {
                $returnData[$keyTranslation] = $rawApiDatum;
            }
        }

        $returnData = $this->includeUserFields($returnData);
        $returnData = $this->includeSearchFields($returnData);

        $returnData["DateTime"] = new DateTime($this->rawApiData['dateTime']);
        $returnData["DateTimeEnd"] = new DateTime($this->rawApiData['dateTimeEnd']);
        $returnData["DateTimeStart"] = new DateTime($this->rawApiData['dateTimeStart']);
        $returnData["Duration"] = $returnData["DateTimeEnd"]->format("U") - $returnData["DateTimeStart"]->format("U");
        $returnData["PartOfDay"] = self::getPartOfDay($this->getDoctrine(), $returnData["DateTime"]);
        $returnData["ExerciseType"] = self::getExerciseType($this->getDoctrine(), CommonSamsung::convertExerciseType($this->rawApiData["exerciseType"]));
        $returnData["LiveDataBlob"] = AppConstants::compressString($this->rawApiData["liveData"]);
        $returnData["LocationDataBlob"] = AppConstants::compressString($this->rawApiData["locationData"]);
        $returnData["CountType"] = CommonSamsung::convertCountType($this->rawApiData["countType"]);

        return $returnData;
    }

    private function inputKeyToEntityKey(string $key)
    {
        switch ($key) {
            case "remoteId":
                return "RemoteId";
                break;
            case "count":
                return "Count";
                break;
            case "comment":
                return "Comment";
                break;
            default:
                return null;
                break;
        }
    }

    private function prepSummaryFromJson($dataEntry)
    {
        $returnData = [];
        foreach ($this->rawApiData as $key => $rawApiDatum) {
            $keyTranslation = $this->inputSummaryKeyToEntityKey($key);
            if (!is_null($keyTranslation)) {
                $returnData[$keyTranslation] = $rawApiDatum;
            }
        }

        $returnData["Exercise"] = $dataEntry;
        $returnData["Patient"] = $this->getPatientEntity();
        $returnData["thirdPartyService"] = $this->thirdPartyService;
        $returnData["searchFields"] = [
            'exercise' => $dataEntry
        ];

        return $returnData;
    }

    private function inputSummaryKeyToEntityKey(string $key)
    {
        switch ($key) {
            case "altitudeGain":
                return "AltitudeGain";
                break;
            case "altitudeLoss":
                return "AltitudeLoss";
                break;
            case "altitudeMax":
                return "AltitudeMax";
                break;
            case "altitudeMin":
                return "AltitudeMin";
                break;
            case "cadenceMax":
                return "CadenceMax";
                break;
            case "cadenceMean":
                return "CadenceMean";
                break;
            case "calorie":
                return "Calorie";
                break;
            case "distance":
                return "Distance";
                break;
            case "declineDistance":
                return "DistanceDecline";
                break;
            case "inclineDistance":
                return "DistanceIncline";
                break;
            case "heartRateMax":
                return "HeartRateMax";
                break;
            case "heartRateMean":
                return "HeartRateMean";
                break;
            case "heartRateMin":
                return "HeartRateMin";
                break;
            case "speedMax":
                return "SpeedMax";
                break;
            case "speedMean":
                return "SpeedMean";
                break;
            default:
                return null;
                break;
        }
    }
}
