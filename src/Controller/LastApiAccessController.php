<?php

namespace App\Controller;

use App\Entity\ApiAccessLog;
use App\Entity\Patient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LastApiAccessController extends AbstractController
{

    /**
     * @Route("/json/{id}/api/{endpoint}/{service}/last", name="get_endpoint_last_pulled")
     * @param String $id A users UUID
     * @param String $service The Service ID requested
     * @param String $endpoint The endpoint name
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(String $id, String $service, String $endpoint)
    {
        $return = [];

        /** @var Patient $patient */
        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findByUuid($id);

        if (!$patient) {
            $return['status'] = "error";
            $return['code'] = "404";
            $return['message'] = "Patient not found with UUID specified";
            $return['payload'] = "2000-01-01 00:00:00";

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
            $return['payload'] = "2000-01-01 00:00:00";

            return $this->json($return);
        }

        $return['status'] = "okay";
        $return['code'] = "200";
        $return['message'] = "";
        $return['payload'] = $apiAccessLog->getLastRetrieved()->format("Y-m-d H:i:s");

        return $this->json($return);
    }
}
