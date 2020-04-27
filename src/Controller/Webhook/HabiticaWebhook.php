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

namespace App\Controller\Webhook;


use App\AppConstants;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HabiticaWebhook extends AbstractController
{
    /**
     * @Route("/sync/webhook/habitica", name="sync_webhook_habitica_get", methods={"GET"})
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function sync_webhook_get()
    {
        $request = Request::createFromGlobals();

        $jsonContent = json_decode($request->getContent(), false);

        AppConstants::writeToLog('debug_transform.txt', '[webhook:habitica] - ' . $jsonContent->user->_id);
        AppConstants::writeToLog('webhook_habitica.txt', $request->getContent());

        $response = new JsonResponse();
        $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
        return $response;
    }

    /**
     * @Route("/sync/webhook/habitica", name="sync_webhook_habitica_post", methods={"POST"})
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function sync_webhook_post()
    {
        return $this->sync_webhook_get();
    }
}
