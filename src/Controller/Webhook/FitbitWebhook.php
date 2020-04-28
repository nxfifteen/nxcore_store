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
use App\Entity\Patient;
use App\Entity\PatientCredentials;
use App\Entity\SyncQueue;
use App\Entity\ThirdPartyService;
use App\Transform\Fitbit\CommonFitbit;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FitbitWebhook extends AbstractController
{
    private function addItemToQueue(
        ThirdPartyService $serviceObject,
        PatientCredentials $patientCredential,
        $queueEndpoints
    ) {
        $serviceSyncQueue = $this->getDoctrine()
            ->getRepository(SyncQueue::class)
            ->findOneBy([
                'credentials' => $patientCredential,
                'service' => $serviceObject,
                'endpoint' => $queueEndpoints,
            ]);

        if (!$serviceSyncQueue) {
            $serviceSyncQueue = new SyncQueue();
            $serviceSyncQueue->setService($serviceObject);
            $serviceSyncQueue->setDatetime(new DateTime());
            $serviceSyncQueue->setCredentials($patientCredential);
            $serviceSyncQueue->setEndpoint($queueEndpoints);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($serviceSyncQueue);
            $entityManager->flush();
        }
    }

    /**
     * @Route("/sync/webhook/fitbit", name="sync_webhook_fitbit_get", methods={"GET"})
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function sync_webhook_get()
    {
        $request = Request::createFromGlobals();

        if (is_array($_GET) && array_key_exists("verify", $_GET)) {
            if ($_GET['verify'] != $_ENV['FITBIT_VERIFIY']) {
                AppConstants::writeToLog('debug_transform.txt',
                    '[webhook:fitbit] - Verification ' . $_GET['verify'] . ' code invalid');

                $response = new JsonResponse();
                $response->setStatusCode(JsonResponse::HTTP_NOT_FOUND);
                return $response;
            }

            $response = new JsonResponse();
            $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
            return $response;
        }

        $jsonContent = json_decode($request->getContent(), false);

        $serviceObject = AppConstants::getThirdPartyService($this->getDoctrine(), "Fitbit");
        if (!is_null($serviceObject)) {
            foreach ($jsonContent as $item) {
                $patient = $this->getDoctrine()
                    ->getRepository(Patient::class)
                    ->findOneBy(['id' => $item->subscriptionId]);

                if (!$patient) {
                    AppConstants::writeToLog('debug_transform.txt',
                        '[webhook:fitbit] - No patient with this ID ' . $item->subscriptionId);
                } else {
                    /** @var PatientCredentials $patientCredential */
                    $patientCredential = $this->getDoctrine()
                        ->getRepository(PatientCredentials::class)
                        ->findOneBy(["service" => $serviceObject, "patient" => $patient]);

                    $queueEndpoints = CommonFitbit::convertSubscriptionToClass($item->collectionType);
                    if (is_array($queueEndpoints)) {
                        foreach ($queueEndpoints as $queueEndpoint) {
                            $this->addItemToQueue($serviceObject, $patientCredential, $queueEndpoint);
                        }
                    } else {
                        $this->addItemToQueue($serviceObject, $patientCredential, $queueEndpoints);
                    }
                }
            }
        }

        $response = new JsonResponse();
        $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
        return $response;
    }

    /**
     * @Route("/sync/webhook/fitbit", name="sync_webhook_fitbit_post", methods={"POST"})
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function sync_webhook_post()
    {
        return $this->sync_webhook_get();
    }
}
