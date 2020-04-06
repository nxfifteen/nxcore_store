<?php
/**
 * DONE This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

namespace App\Controller;

use App\AppConstants;
use App\Entity\Patient;
use App\Entity\PatientCredentials;
use App\Entity\PatientSettings;
use App\Entity\ThirdPartyService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Patreon\API;
use Patreon\AuthUrl;
use Patreon\OAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class AuthWithPatreonController extends AbstractController
{
    /**
     * @Route("/auth/with/patreon/{uuid}", name="auth_with_patreon")
     * @param ManagerRegistry $doctrine
     * @param RequestStack    $request
     *
     * @param String          $uuid
     *
     * @return void
     */
    public function auth_with_patreon(ManagerRegistry $doctrine, RequestStack $request, String $uuid)
    {
        $this->hasAccess($uuid);

        if (!isset($_SESSION)) {
            session_start();
        }

        $queryCallback = explode('/auth/with/patreon', $request->getMasterRequest()->getUri())[0];
        $queryCallback = $queryCallback . '/auth/callback/patreon';
        $queryCallback = str_replace("http://", "https://", $queryCallback);
        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $uuid . ' initiated a Fitbit auth ');
        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $queryCallback);

        $_SESSION['uuid'] = $uuid;
        $_SESSION['key'] = $request->getMasterRequest()->get("key");
        $_SESSION['returnUrl'] = $request->getMasterRequest()->get("return") . '/#/' . $request->getMasterRequest()->get("returnPath");

        $redirect_uri = "https://connect.core.nxfifteen.me.uk/auth/callback/patreon";
        $href = (new AuthUrl($_ENV['PATREON_CLIENT_ID']))
            ->withRedirectUri($redirect_uri);

        $state = [];
        $state['final_page'] = 'https://core.nxfifteen.me.uk/#/patreon/thanks';

        $href = $href->withState($state);
        $href = $href
            ->withAddedScope('identity')
            ->withAddedScope('identity[email]');

        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $href);

        // Redirect the user to the authorization URL.
        header('Location: ' . $href);
        exit;
    }

    /**
     * @param String $uuid
     *
     * @throws \LogicException If the Security component is not available
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

    /**
     * @Route("/auth/callback/patreon", name="auth_with_patreon_callback")
     * @param ManagerRegistry $doctrine
     * @param RequestStack    $request
     *
     * @return void
     * @throws \Exception
     */
    public function auth_with_patreon_callback(ManagerRegistry $doctrine, RequestStack $request)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $queryCallback = explode('?', $request->getMasterRequest()->getUri())[0];
        $queryCallback = str_replace("http://", "https://", $queryCallback);
        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $_SESSION['uuid'] . '\'s Fitbit authentication succeeded');

        if ($_GET['code'] != '') {
            /** @var Patient $patient */
            $patient = $this->getDoctrine()
                ->getRepository(Patient::class)
                ->findOneBy(['uuid' => $_SESSION['uuid']]);

            if (!$patient) {
                $exception = $this->createAccessDeniedException("User tried to access another users information");
                throw $exception;
            }

            $oauth_client = new OAuth($_ENV['PATREON_CLIENT_ID'], $_ENV['PATREON_CLIENT_ID']);

            $tokens = $oauth_client->get_tokens($_GET['code'], $queryCallback);
            $access_token = $tokens['access_token'];
            $refresh_token = $tokens['refresh_token'];

            $api_client = new API($access_token);
            $patron_user = $api_client->fetch_user();

            /** @var ThirdPartyService $service */
            $service = AppConstants::getThirdPartyService($doctrine, "Patreon");

            /** @var PatientCredentials $patientCredentials */
            $patientCredentials = $this->getDoctrine()
                ->getRepository(PatientCredentials::class)
                ->findOneBy(['patient' => $patient, 'service' => $service]);

            if (!$patientCredentials) {
                $patientCredentials = new PatientCredentials();
                $patientCredentials->setPatient($patient);
                $patientCredentials->setService($service);
            }

            $patientCredentials->setToken($access_token);
            $patientCredentials->setRefreshToken($refresh_token);

            if (array_key_exists("data", $patron_user) && array_key_exists("attributes", $patron_user['data'])) {
                if (array_key_exists("image_url", $patron_user['data']['attributes']) && is_null($patient->getAvatar())) {
                    $image_url = $patron_user['data']['attributes']['image_url'];
                    $patient->setAvatar($image_url);
                }
                if (array_key_exists("first_name", $patron_user['data']['attributes']) && is_null($patient->getFirstName())) {
                    $first_name = $patron_user['data']['attributes']['first_name'];
                    $patient->setFirstName($first_name);
                }
                if (array_key_exists("last_name", $patron_user['data']['attributes']) && is_null($patient->getSurName())) {
                    $last_name = $patron_user['data']['attributes']['last_name'];
                    $patient->setSurName($last_name);
                }
            }

            if (array_key_exists("included", $patron_user) && array_key_exists("0", $patron_user['included']) && array_key_exists("attributes", $patron_user['included'][0])) {
                if (array_key_exists("currently_entitled_amount_cents", $patron_user['included'][0]['attributes'])) {
                    $currently_entitled_amount_cents = $patron_user['included'][0]['attributes']['currently_entitled_amount_cents'];
                } else {
                    $currently_entitled_amount_cents = -1;
                }

                if (array_key_exists("lifetime_support_cents", $patron_user['included'][0]['attributes'])) {
                    $lifetime_support_cents = $patron_user['included'][0]['attributes']['lifetime_support_cents'];
                } else {
                    $lifetime_support_cents = -1;
                }

                // Tear Settings
                $endPointTearSettings = new PatientSettings();
                $endPointTearSettings->setPatient($patient);
                $endPointTearSettings->setService($service);
                $endPointTearSettings->setName("tearPaid");
                $endPointTearSettings->setValue([
                    $currently_entitled_amount_cents,
                    $lifetime_support_cents,
                ]);

                if (array_key_exists("last_charge_status", $patron_user['included'][0]['attributes'])) {
                    $last_charge_status = $patron_user['included'][0]['attributes']['last_charge_status'];
                } else {
                    $last_charge_status = 'unknown';
                };
                if (array_key_exists("last_charge_date", $patron_user['included'][0]['attributes'])) {
                    $last_charge_date = $patron_user['included'][0]['attributes']['last_charge_date'];
                } else {
                    $last_charge_date = '';
                };
                if (array_key_exists("patron_status", $patron_user['included'][0]['attributes'])) {
                    $patron_status = $patron_user['included'][0]['attributes']['patron_status'];
                } else {
                    $patron_status = 'unknown';
                };
                if (array_key_exists("pledge_relationship_start", $patron_user['included'][0]['attributes'])) {
                    $pledge_relationship_start = $patron_user['included'][0]['attributes']['pledge_relationship_start'];
                } else {
                    $pledge_relationship_start = '';
                };

                // Patreon Status
                $endPointStatusSettings = new PatientSettings();
                $endPointStatusSettings->setPatient($patient);
                $endPointStatusSettings->setService($service);
                $endPointStatusSettings->setName("tearStatus");
                $endPointStatusSettings->setValue([
                    $patron_status,
                    $last_charge_status,
                    $last_charge_date,
                    $pledge_relationship_start,
                ]);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($patientCredentials);
            if (isset($endPointTearSettings)) {
                $entityManager->persist($endPointTearSettings);
            }
            if (isset($endPointStatusSettings)) {
                $entityManager->persist($endPointStatusSettings);
            }
            $entityManager->persist($patient);
            $entityManager->flush();

            // Redirect the user to the authorization URL.
            header('Location: ' . $_SESSION['returnUrl']);
            exit;
        } else {
            // Redirect the user to the authorization URL.
            header('Location: ' . $_SESSION['returnUrl'] . '/oops');
            exit;
        }

    }
}
