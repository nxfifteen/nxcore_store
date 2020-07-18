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


use App\Transform\Base\BaseFitStepsIntraDay;
use DateTime;

class FitStepsIntraDay extends BaseFitStepsIntraDay
{

    /**
     * @return string|null
     * @noinspection PhpUnused
     */
    public function processData()
    {
        $this->thirdPartyService = self::getThirdPartyService($this->getDoctrine(),
            CommonSamsung::SAMSUNGHEALTHSERVICE);
        $this->saveEntityFromArray("App\Entity\FitStepsIntraDay", $this->prepFromJson());

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

        $returnData["DateTime"] = new DateTime($this->rawApiData['date']);

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
            case "hour":
                return "Hour";
                break;
            case "duration":
                return "Duration";
                break;
            default:
                return null;
                break;
        }
    }


}
