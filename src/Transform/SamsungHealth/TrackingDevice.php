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


use App\Entity\ThirdPartyService;
use App\Transform\Base\BaseTrackingDevice;
use DateTime;

class TrackingDevice extends BaseTrackingDevice
{

    /** @var ThirdPartyService */
    private $thirdPartyService;

    private function inputKeyToEntityKey(string $key)
    {
        switch ($key) {
            case "name":
                return "Name";
                break;
            case "remoteId":
                return "RemoteId";
                break;
            case "type":
                return "Type";
                break;
            case "manufacturer":
                return "Manufacturer";
                break;
            case "model":
                return "Model";
                break;
            default:
                return null;
                break;
        }
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
        $returnData["Patient"] = $this->getPatientEntity();
        $returnData["Service"] = $this->thirdPartyService;
        $returnData["thirdPartyService"] = $returnData["Service"];
        $returnData["LastSynced"] = new DateTime();
        $returnData["searchFields"] = [
            'remoteId' => $returnData["RemoteId"],
            'patient' => $returnData["Patient"],
            'service' => $returnData["Service"],
        ];

        return $returnData;
    }

    /**
     * @return string|null
     * @noinspection PhpUnused
     */
    public function processData()
    {
        $this->thirdPartyService = self::getThirdPartyService($this->getDoctrine(), Constants::SAMSUNGHEALTHSERVICE);
        $this->saveEntityFromArray("App\Entity\TrackingDevice", $this->prepFromJson());

        return null;
    }
}
