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

namespace App\Controller\Sync;


use App\Service\ServerToServer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CntrSyncMembership extends AbstractController
{
    /**
     * @Route("/sync/membership/", name="sync_server_comms")
     *
     * @param ServerToServer $serverComms
     *
     * @return JsonResponse
     */
    public function syncServerToServer(ServerToServer $serverComms)
    {
        $request = Request::createFromGlobals();
        $recivedData = $request->getContent();
        return $serverComms->recievedFromMember($recivedData);
    }
}
