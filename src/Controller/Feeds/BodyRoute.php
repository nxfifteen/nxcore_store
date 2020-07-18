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

namespace App\Controller\Feeds;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @noinspection PhpUnused */

class BodyRoute extends CommonFeeds
{
    /**
     * @Route("/feed/body/weight/{readings}", name="angular_body_weight")
     * @param int $readings A users UUID
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index_body_weight(int $readings)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $return['status'] = "okay";
        $return['code'] = "200";
        $return['weight'] = $this->getPatientWeight(false, date("Y-m-d"), $readings);

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }
}
