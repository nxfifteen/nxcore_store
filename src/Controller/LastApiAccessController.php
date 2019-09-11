<?php

namespace App\Controller;

use App\Entity\ApiAccessLog;
use App\Entity\Patient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LastApiAccessController extends AbstractController
{

    /**
     * @Route("/help/last_upload", name="get_endpoint_last_help")
     */
    public function index_help()
    {
        return $this->render('last_api_access/index.html.twig', [
            'controller_name' => 'LastApiAccessController',
        ]);
    }

    /**
     * @Route("/json/{uuid}/api/{endpoint}/{service}/last", name="get_endpoint_last_pulled")
     * @param String $uuid     A users UUID
     * @param String $service  The Service ID requested
     * @param String $endpoint The endpoint name
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(String $uuid, String $service, String $endpoint)
    {
        $this->hasAccess($uuid);

        $return = [];

        /** @var Patient $patient */
        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findOneBy(['uuid' => $uuid]);

        if (!$patient) {
            $return['status'] = "error";
            $return['code'] = "404";
            $return['message'] = "Patient not found with UUID specified";
            $return['payload'] = "2000-01-01 00:00:00.000";

            return $this->json($return);
        }

        /** @var ApiAccessLog $patient */
        $apiAccessLog = $this->getDoctrine()
            ->getRepository(ApiAccessLog::class)
            ->findLastAccess($patient, $service, $endpoint);

        if (!$apiAccessLog) {
            $return['status'] = "warning";
            $return['code'] = "201";
            $return['message'] = "No last access log for Service/EndPoint combination";
            $return['payload'] = "2000-01-01 00:00:00.000";

            return $this->json($return);
        }

        $return['status'] = "okay";
        $return['code'] = "200";
        $return['message'] = "";
        $return['payload'] = $apiAccessLog->getLastRetrieved()->format("Y-m-d H:i:s.v");

        return $this->json($return);
    }

    /**
     * @param String $uuid
     *
     * @throws \LogicException If the Security component is not available
     */
    private function hasAccess(String $uuid)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', NULL, 'User tried to access a page without having ROLE_USER');

        /** @var \App\Entity\Patient $user */
        $user = $this->getUser();
        if ($user->getUuid() != $uuid) {
            $exception = $this->createAccessDeniedException("User tried to access another users information");
            throw $exception;
        }
    }

}
