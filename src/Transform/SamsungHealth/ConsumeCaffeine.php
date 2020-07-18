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


use App\Transform\Base\BaseConsumeCaffeine;
use DateTime;

class ConsumeCaffeine extends BaseConsumeCaffeine
{

    /**
     * @return string|null
     * @noinspection PhpUnused
     */
    public function processData()
    {
        $this->thirdPartyService = self::getThirdPartyService($this->getDoctrine(),
            CommonSamsung::SAMSUNGHEALTHSERVICE);
        $this->saveEntityFromArray("App\Entity\ConsumeCaffeine", $this->prepFromJson());

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

        $uomMg = self::getUnitOfMeasurement($this->getDoctrine(), "mg");

        $returnData = $this->includeUserFields($returnData);
        $returnData = $this->includeSearchFields($returnData);

        $returnData["DateTime"] = new DateTime($this->rawApiData['dateTime']);
        $returnData["PartOfDay"] = self::getPartOfDay($this->getDoctrine(), $returnData["DateTime"]);
        $returnData["PatientGoal"] = self::getPatientGoal($this->getDoctrine(), "ConsumeCaffeine", (80 * 3), $uomMg,
            $this->getPatientEntity());
        $returnData["UnitOfMeasurement"] = $uomMg;

        return $returnData;
    }

    private function inputKeyToEntityKey(string $key)
    {
        switch ($key) {
            case "remoteId":
                return "RemoteId";
                break;
            case "measurement":
                return "Measurement";
                break;
            case "comment":
                return "Comment";
                break;
            default:
                return null;
                break;
        }
    }

}
