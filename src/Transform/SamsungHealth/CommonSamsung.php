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

class CommonSamsung
{
    /**
     *
     */
    const SAMSUNGHEALTHSERVICE = "Samsung Health";

    /**
     * @param $endpoint
     *
     * @return string|null
     */
    public static function convertDatasetToEntity($endpoint)
    {
        switch ($endpoint) {
            case "tracking_devices":
                return "TrackingDevice";
                break;
            default:
                AppConstants::writeToLog('debug_transform.txt', 'Unknown $endpoint ' . $endpoint);
                return null;
                break;
        }
    }

}
