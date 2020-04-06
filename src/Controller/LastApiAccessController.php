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

namespace App\Controller;

use App\Entity\ApiAccessLog;
use App\Entity\Patient;
use App\Entity\PatientSettings;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LastApiAccessController
 *
 * @package App\Controller
 */
class LastApiAccessController extends AbstractController
{
    /**
     * @Route("/json/{uuid}/api/{endpoint}/{service}/last", name="get_endpoint_last_pulled")
     * @param String $uuid     A users UUID
     * @param String $service  The Service ID requested
     * @param String $endpoint The endpoint name
     *
     * @return JsonResponse
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
            /** @var PatientSettings $dbSettings */
            $dbSettings = $this->getDoctrine()
                ->getRepository(PatientSettings::class)
                ->findOneBy(['patient' => $patient, 'service' => $service, 'name' => 'from']);
            if ($dbSettings) {
                $return['status'] = "warning";
                $return['code'] = "201";
                $return['message'] = "From birth";
                $return['payload'] = $dbSettings->getValue()[0];

                return $this->json($return);
            }

            $return['status'] = "warning";
            $return['code'] = "201";
            $return['message'] = "Fallback date";
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
     * @throws LogicException If the Security component is not available
     */
    private function hasAccess(String $uuid)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', NULL, 'User tried to access a page without having ROLE_USER');

        /** @var Patient $user */
        $user = $this->getUser();
        if ($user->getUuid() != $uuid) {
            $exception = $this->createAccessDeniedException("User tried to access another users information");
            throw $exception;
        }
    }

}
