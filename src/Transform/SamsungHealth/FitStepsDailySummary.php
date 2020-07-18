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


use App\Transform\Base\BaseFitStepsDailySummary;

class FitStepsDailySummary extends BaseFitStepsDailySummary
{
    // {"remoteId":"1c920160-2d98-4bcb-a0b7-d1989503c2e9","value":7807,"value_distance":5818.603002059927,"value_calorie":404,"uom_distance":"meter","dateTimeDayTime":"2020-04-29 00:00:00","x-trackingDevice":"YoXWK1BhG5"}

    /**
     * @return string|null
     * @noinspection PhpUnused
     */
    public function processData()
    {
        $this->thirdPartyService = self::getThirdPartyService($this->getDoctrine(),
            CommonSamsung::SAMSUNGHEALTHSERVICE);
        $this->saveEntityFromArray("App\Entity\FitStepsDailySummary", $this->prepFromJson());

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

        return $returnData;
    }

    private function inputKeyToEntityKey(string $key)
    {
        switch ($key) {
            case "remoteId":
                return "RemoteId";
                break;
            case "value":
                return "Value";
                break;
            default:
                return null;
                break;
        }
    }
}
