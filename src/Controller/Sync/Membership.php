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
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Membership extends AbstractController
{
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /** @var bool $debug */
    private $debug = false;

    private function checkForReferencesData($datum)
    {
        $datumOrig = $datum;

        if (gettype($datum) == "string" && substr($datum, 0, 1) == "[") {
            $datum = json_decode($datum, true);
        } else {
            if (gettype($datum) == "string" && substr($datum, 0, 10) == "%DateTime%") {
                /** @noinspection PhpUnhandledExceptionInspection */
                $datum = new DateTime(date("Y-m-d H:i:s", str_ireplace("%DateTime%", "", $datum)));
            } else {
                if (gettype($datum) == "string" && substr($datum, 0, 1) == "@") {
                    $entitySearchParam = explode('|', $datum, 2);
                    $entitySearchClass = substr($entitySearchParam[0], 1, strlen($entitySearchParam[0]) - 1);
                    $entitySearchArgs = json_decode($entitySearchParam[1], true);

                    if (is_array($entitySearchArgs)) {
                        foreach ($entitySearchArgs as $key => $entitySearchArg) {
                            $entitySearchArgs[$key] = $this->checkForReferencesData($entitySearchArg);
                        }
                    }

                    if ($this->debug) {
                        AppConstants::writeToLog('debug_transform.txt',
                            __LINE__ . ' $entitySearchClass = ' . $entitySearchClass);
                    }

                    if ($entitySearchClass == "App\Entity\ThirdPartyService") {
                        $datum = AppConstants::getThirdPartyService($this->getDoctrine(), $entitySearchArgs['name']);
                    } else {
                        $entitySearchDB = $this->getDoctrine()
                            ->getRepository($entitySearchClass)
                            ->findOneBy($entitySearchArgs);
                        if (!is_null($entitySearchDB)) {
                            $datum = $entitySearchDB;
                        } else {
                            if ($this->debug) {
                                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' died');
                            }
                            die();
                        }
                    }

                }
            }
        }

        if ($datumOrig !== $datum) {
            if ($this->debug) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . '  $datumOrig = ' . $datumOrig);
            }
            if ($this->debug) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . '   gettype($datum) = ' . gettype($datum));
            }
            if (gettype($datum) == "object") {
                if ($this->debug) {
                    AppConstants::writeToLog('debug_transform.txt',
                        __LINE__ . '   get_class($datum) = ' . get_class($datum));
                }
            } elseif (gettype($datum) == "array") {
                if ($this->debug) {
                    AppConstants::writeToLog('debug_transform.txt', __LINE__ . '   $datum = ' . print_r($datum, true));
                }
            }
        }

        return $datum;
    }

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
        if (
            $messageTranslated['className'] != 'App\Entity\Patient' &&
            $messageTranslated['className'] != 'App\Entity\ThirdPartyService' &&
            $messageTranslated['className'] != 'App\Entity\PatientSettings' &&
            $messageTranslated['className'] != 'App\Entity\PatientGoals' &&
            $messageTranslated['className'] != 'App\Entity\TrackingDevice' &&
            $messageTranslated['className'] != 'App\Entity\FitStepsDailySummary' &&
            $messageTranslated['className'] != 'App\Entity\PatientCredentials' &&
            $messageTranslated['className'] != 'App\Entity\PatientFriends'
        ) {
            $this->debug = true;
        }
        if ($this->debug) AppConstants::writeToLog('debug_transform.txt',
            '---------------------------------------------------------');
        if ($this->debug) AppConstants::writeToLog('debug_transform.txt',
            __LINE__ . ' Packet ' . print_r($messageTranslated, true));

        if (is_int($messageTranslated)) {
            if ($this->debug) AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' died');

            $response = new JsonResponse();
            $response->setStatusCode($messageTranslated);
            return $response;
        } else {
            $this->passwordEncoder = $passwordEncoder;

            foreach ($messageTranslated['search'] as $key => $search) {
                $messageTranslated['search'][$key] = $this->checkForReferencesData($search);
            }

            $newEntity = $this->getDoctrine()
                ->getRepository($messageTranslated['className'])
                ->findOneBy($messageTranslated['search']);
            if (is_null($newEntity)) {
                $newEntity = new $messageTranslated['className']();
                $updateEntity = false;
            } else {
                $updateEntity = true;
            }

            $entityAdditonMethod = "add" . str_ireplace("App\Entity\\", "", $messageTranslated['className']) . "Entity";
            if (method_exists($this, $entityAdditonMethod)) {
                $newEntity = $this->$entityAdditonMethod($newEntity, $messageTranslated, $updateEntity);
            } else {
                if ($this->debug) AppConstants::writeToLog('debug_transform.txt',
                    __LINE__ . ' Looked for ' . $entityAdditonMethod);
                $newEntity = $this->addDefaultEntity($newEntity, $messageTranslated, $updateEntity);
            }

            if (!is_null($newEntity)) {
                $_ENV['EntityEnv'] = 'ServerComms';
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newEntity);
                $entityManager->flush();
                unset($_ENV['EntityEnv']);
            }

            $response = new JsonResponse();
            $response->setStatusCode(204);
            return $response;
        }
    }

    /**
     * @param $newEntity
     * @param $messageTranslated
     * @param $updateEntity
     *
     * @return mixed
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function addPatientMembershipEntity($newEntity, $messageTranslated, $updateEntity)
    {
        $newEntity = $this->addDefaultEntity($newEntity, $messageTranslated, $updateEntity);
        $newEntity->setSince(new DateTime());
        $newEntity->setSince(new DateTime());
        return $newEntity;
    }

    /**
     * @param $newEntity
     * @param $messageTranslated
     * @param $updateEntity
     *
     * @return null
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function addPatientEntity($newEntity, $messageTranslated, $updateEntity)
    {
        $newEntity = $this->addDefaultEntity($newEntity, $messageTranslated, $updateEntity);

        if (!$updateEntity || is_null($newEntity->getPassword())) {
            $newEntity->setPassword($this->passwordEncoder->encodePassword($newEntity, $_ENV['CORE_INVITE_CODE']));
        }

        if (!$updateEntity || is_null($newEntity->getApiToken())) {
            try {
                $newEntity->setApiToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
            } catch (Exception $e) {
            }
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

    /**
     * @param $newEntity
     * @param $messageTranslated
     * @param $updateEntity
     *
     * @return mixed
     */
    private function addDefaultEntity($newEntity, $messageTranslated, $updateEntity)
    {
        foreach ($messageTranslated['data'] as $key => $datum) {
            $methodName = "set" . $key;
            if (method_exists($newEntity, $methodName)) {
                $datum = $this->checkForReferencesData($datum);

                switch (gettype($datum)) {
                    case "string":
                    case "boolean":
                    case "integer":
                    case "array":
                        $newEntity->$methodName($datum);
                        break;
                    case "object":
                        switch (get_class($datum)) {
                            case "DateTime":
                            case "App\Entity\Patient":
                            case "App\Entity\ThirdPartyService":
                            case "App\Entity\PatientGoals":
                            case "App\Entity\TrackingDevice":
                                $newEntity->$methodName($datum);
                                break;
                            default:
                                if ($this->debug) AppConstants::writeToLog('debug_transform.txt',
                                    __LINE__ . ' Decypted gettype = ' . get_class($datum));
                                break;
                        }
                        break;
                    default:
                        if ($this->debug) AppConstants::writeToLog('debug_transform.txt',
                            __LINE__ . ' Decypted gettype = ' . gettype($datum));
                        break;
                }
            }

            if (
                method_exists($newEntity, "setRemoteId") &&
                !array_key_exists("RemoteId", $messageTranslated['data']) &&
                array_key_exists("Guid", $messageTranslated['data'])
            ) {
                $newEntity->setRemoteId($messageTranslated['data']['Guid']);
            }
        }

        return $newEntity;
    }
}
