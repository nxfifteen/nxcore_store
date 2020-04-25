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


use App\AppConstants;
use App\Entity\PatientMembership;
use App\Service\ServerToServer;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Membership extends AbstractController
{
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * @Route("/sync/membership/", name="sync_server_comms")
     *
     * @param ServerToServer               $serverComms
     * @param UserPasswordEncoderInterface $passwordEncoder ,
     *
     * @return JsonResponse
     */
    public function syncServerToServer(ServerToServer $serverComms, UserPasswordEncoderInterface $passwordEncoder)
    {
        $request = Request::createFromGlobals();
        $recivedData = $request->getContent();

        $messageTranslated = $serverComms->recievedFromMember($recivedData);

        if (is_int($messageTranslated)) {
            $response = new JsonResponse();
            $response->setStatusCode($messageTranslated);
            return $response;
        } else {
//            $this->passwordEncoder = $passwordEncoder;
//
//            AppConstants::writeToLog('debug_transform.txt', 'Decypted Data = ' . print_r($messageTranslated, true));
//            $newEntity = $this->getDoctrine()
//                ->getRepository($messageTranslated['className'])
//                ->findOneBy($messageTranslated['search']);
//            if (is_null($newEntity)) {
//                AppConstants::writeToLog('debug_transform.txt', 'Decypted Data = ' . __LINE__);
//                $newEntity = new $messageTranslated['className']();
//                $updateEntity = false;
//            } else {
//                $updateEntity = true;
//            }
//
//            $entityAdditonMethod = "add" . str_ireplace("App\Entity\\", "", $messageTranslated['className']) . "Entity";
//            if (method_exists($this, $entityAdditonMethod)) {
//                AppConstants::writeToLog('debug_transform.txt', 'Found ' . $entityAdditonMethod);
//                $newEntity = $this->$entityAdditonMethod($newEntity, $messageTranslated, $updateEntity);
//            } else {
//                AppConstants::writeToLog('debug_transform.txt', 'Looked for ' . $entityAdditonMethod);
//                $newEntity = $this->addDefaultEntity($newEntity, $messageTranslated, $updateEntity);
//            }
//
//            if (!is_null($newEntity)) {
//                $_ENV['EntityEnv'] = 'ServerComms';
//                $entityManager = $this->getDoctrine()->getManager();
//                $entityManager->persist($newEntity);
//                $entityManager->flush();
//                unset($_ENV['EntityEnv']);
//            }

            $response = new JsonResponse();
            $response->setStatusCode(204);
            return $response;
        }
    }

    private function addPatientEntity($newEntity, $messageTranslated, $updateEntity)
    {
        $newEntity = $this->addDefaultEntity($newEntity, $messageTranslated, $updateEntity);

        if (!$updateEntity || is_null($newEntity->getPassword())) {
            $newEntity->setPassword($this->passwordEncoder->encodePassword($newEntity, $_ENV['CORE_INVITE_CODE']));
        }

        if (!$updateEntity || is_null($newEntity->getApiToken())) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $newEntity->setApiToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
        }

        $_ENV['EntityEnv'] = 'ServerComms';
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($newEntity);
        $entityManager->flush();

        if (!$updateEntity) {
            /** @var PatientMembership $patientMembership */
            $patientMembership = $this->getDoctrine()
                ->getRepository(PatientMembership::class)
                ->findOneBy(["patient" => $newEntity->getId()]);
            if (!is_null($patientMembership)) {
                $membership = new PatientMembership();
                $membership->setPatient($newEntity);
                $membership->setTear('mesh_user');
                $membership->setSince(new DateTime());
                $membership->setLastPaid(new DateTime());
                $membership->setActive(true);
                $membership->setLifetime(false);
                $entityManager->persist($membership);
                $entityManager->flush();
            }
        }

        unset($_ENV['EntityEnv']);

        return null;
    }

    private function addDefaultEntity($newEntity, $messageTranslated, $updateEntity)
    {
        foreach ($messageTranslated['data'] as $key => $datum) {
            $methodName = "set" . $key;
//            AppConstants::writeToLog('debug_transform.txt', 'Decypted gettype = ' . gettype($datum));
//            AppConstants::writeToLog('debug_transform.txt', 'Decypted $methodName = ' . $methodName);
            if (method_exists($newEntity, $methodName)) {
                if (gettype($datum) == "string" && substr($datum, 0, 1) == "[") {
                    $datum = json_decode($datum, true);
                } else {
                    if (gettype($datum) == "string" && substr($datum, 0, 10) == "%DateTime%") {
                        /** @noinspection PhpUnhandledExceptionInspection */
                        $datum = new DateTime(date("Y-m-d H:i:s", str_ireplace("%DateTime%", "", $datum)));
                    }
                }

                switch (gettype($datum)) {
                    case "string":
                    case "boolean":
                    case "integer":
                    case "array":
//                        AppConstants::writeToLog('debug_transform.txt', 'Decypted Data = ' . __LINE__);
                        $newEntity->$methodName($datum);
                        break;
                    default:
                        AppConstants::writeToLog('debug_transform.txt', 'Decypted Data = ' . __LINE__);
                        AppConstants::writeToLog('debug_transform.txt', 'Decypted gettype = ' . gettype($datum));
                        break;
                }
            }
        }

        return $newEntity;
    }
}
