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

namespace App\Transform\Base;

use App\AppConstants;

/**
 * Class Entry
 *
 * @package App\Transform\Base
 */
class BaseEntry
{

    /**
     * @param mixed $request
     */
    protected function postToMirror($request)
    {
        if (method_exists($request, "toJson")) {
            AppConstants::writeToLog('mirror.txt', __LINE__ . $request->toJson());
        }
    }

}
