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

namespace App\Controller\Feeds\Digest;


use App\Controller\Feeds\CommonFeeds;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @noinspection PhpUnused */

class HomeAssistantRoute extends CommonFeeds
{

    /**
     * @Route("/feed/hass", name="index_hass_digest")
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index_hass_digest()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $return['uuid'] = $this->patient->getUuid();
        $return['steps'] = $this->getPatientSteps()['value'];
        $return['floors'] = $this->getPatientFloors()['value'];
        $return['water'] = 0;
        $return['weight'] = $this->getPatientWeight()['value'];
        $return['fat'] = 0;
        $return['bmi'] = 0;
        $return['minutes_active'] = 0;
        $return['calories'] = 0;
        $return['distance'] = $this->getPatientDistance()['value'];
        $return['weight_loss'] = 0;
        $return['resting_heart_rate'] = 0;

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }
}
