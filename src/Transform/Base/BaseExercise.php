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

namespace App\Transform\Base;


use App\AppConstants;
use App\Transform\Transform;

class BaseExercise extends Transform
{
    protected function includeSearchFields($returnData)
    {
        $returnData["searchFields"] = [
            'RemoteId' => $returnData["RemoteId"],
            'patient' => $this->getPatientEntity(),
            'trackingDevice' => $returnData["TrackingDevice"],
        ];

        return $returnData;
    }

    protected function includeUserFields($returnData)
    {
        $returnData["Patient"] = $this->getPatientEntity();
        $returnData["thirdPartyService"] = $this->thirdPartyService;
        $returnData["TrackingDevice"] = AppConstants::getTrackingDevice($this->getDoctrine(), $this->getPatientEntity(),
            $this->thirdPartyService, $this->rawApiData['x-trackingDevice']);

        return $returnData;
    }
}
