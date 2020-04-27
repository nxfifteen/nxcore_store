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

/** @noinspection DuplicatedCode */

namespace App\Transform\Core;


use App\AppConstants;
use App\Entity\FitStepsIntraDay;
use App\Service\AwardManager;
use App\Transform\Base\BaseIntraDaySteps;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class SamsungIntraDaySteps
 *
 * @package App\Transform\Core
 */
class CoreSyncIntraDaySteps extends BaseIntraDaySteps
{
    /**
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return FitStepsIntraDay|null
     * @throws Exception
     */
    public static function translate(ManagerRegistry $doctrine, string $getContent, AwardManager $awardManager)
    {
        $jsonContent = Constants::decodeJson($getContent);
        AppConstants::writeToLog('debug_transform.txt',
            __CLASS__ . '::' . __FUNCTION__ . '|' . __LINE__ . " - : " . print_r($jsonContent, true));

        return null;
    }
}
