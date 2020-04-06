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
use djchen\OAuth2\Client\Provider\Fitbit;
use Doctrine\Common\Persistence\ManagerRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class AuthWithFitbitController extends AbstractController
{
    /**
     * @Route("/auth/with/fitbit/{uuid}", name="auth_with_fitbit")
     * @param ManagerRegistry $doctrine
     * @param RequestStack    $request
     *
     * @param String          $uuid
     *
     * @return void
     */
    public function auth_with_fitbit(ManagerRegistry $doctrine, RequestStack $request, String $uuid)
    {
        $this->hasAccess($uuid);

        if (!isset($_SESSION)) {
            session_start();
        }

        $queryCallback = explode('/auth/with/fitbit', $request->getMasterRequest()->getUri())[0];
        $queryCallback = $queryCallback . '/auth/callback/fitbit';
        $queryCallback = str_replace("http://", "https://", $queryCallback);
        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $uuid . ' initiated a Fitbit auth ');

        $provider = new Fitbit([
            'clientId' => $_ENV['FITBIT_ID'],
            'clientSecret' => $_ENV['FITBIT_SECRET'],
            'redirectUri' => $queryCallback,
        ]);

        // Fetch the authorization URL from the provider; this returns the
        // urlAuthorize option and generates and applies any necessary parameters
        // (e.g. state).
        $authorizationUrl = $provider->getAuthorizationUrl();

        // Get the state generated for you and store it to the session.
        $_SESSION['uuid'] = $uuid;
        $_SESSION['key'] = $request->getMasterRequest()->get("key");
        $_SESSION['returnUrl'] = $request->getMasterRequest()->get("return") . '/#/' . $request->getMasterRequest()->get("returnPath");
        $_SESSION['oauth2state'] = $provider->getState();

        // Redirect the user to the authorization URL.
        header('Location: ' . $authorizationUrl);
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
     * @Route("/auth/callback/fitbit", name="auth_with_fitbit_callback")
     * @param ManagerRegistry $doctrine
     * @param RequestStack    $request
     *
     * @return void
     * @throws \Exception
     */
    public function auth_with_fitbit_callback(ManagerRegistry $doctrine, RequestStack $request)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $queryCallback = explode('?', $request->getMasterRequest()->getUri())[0];
        $queryCallback = str_replace("http://", "https://", $queryCallback);
        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $_SESSION['uuid'] . '\'s Fitbit authentication succeeded');

        /** @var Patient $patient */
        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findOneBy(['uuid' => $_SESSION['uuid']]);

        if (!$patient) {
            $exception = $this->createAccessDeniedException("User tried to access another users information");
            throw $exception;
        }

        $provider = new Fitbit([
            'clientId' => $_ENV['FITBIT_ID'],
            'clientSecret' => $_ENV['FITBIT_SECRET'],
            'redirectUri' => $queryCallback,
        ]);

        try {

            // Try to get an access token using the authorization code grant.
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $request->getMasterRequest()->get("code"),
            ]);

            /** @var ThirdPartyService $service */
            $service = AppConstants::getThirdPartyService($doctrine, "Fitbit");

            /** @var PatientCredentials $patientCredentials */
            $patientCredentials = $this->getDoctrine()
                ->getRepository(PatientCredentials::class)
                ->findOneBy(['patient' => $patient, 'service' => $service]);

            if (!$patientCredentials) {
                $patientCredentials = new PatientCredentials();
                $patientCredentials->setPatient($patient);
                $patientCredentials->setService($service);
            }

            $patientCredentials->setToken($accessToken->getToken());
            $patientCredentials->setRefreshToken($accessToken->getRefreshToken());
            $date = new \DateTime();
            $date->setTimestamp($accessToken->getExpires());
            $patientCredentials->setExpires($date);

            $patient->setFirstRun(FALSE);

            $endPointSettings = new PatientSettings();
            $endPointSettings->setPatient($patient);
            $endPointSettings->setService($service);
            $endPointSettings->setName("enabledEndpoints");
            $endPointSettings->setValue([
                "TrackingDevice",
                "FitStepsDailySummary",
            ]);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($patientCredentials);
            $entityManager->persist($endPointSettings);
            $entityManager->persist($patient);
            $entityManager->flush();

            $path = "https://api.fitbit.com/1/user/-/activities/apiSubscriptions/" . $patient->getId() . ".json";

            try {
                $request = $this->getLibrary()->getAuthenticatedRequest('POST', $path, $accessToken);

                $response = $this->getLibrary()->getResponse($request);
            } catch (IdentityProviderException $e) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . " - " . ' ' . $e->getMessage());        // Redirect the user to the authorization URL.
                header('Location: ' . $_SESSION['returnUrl'] . '?complete=true');
                exit;
            }

        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Failed to get the access token or user details.
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $e->getMessage());        // Redirect the user to the authorization URL.
            header('Location: ' . $_SESSION['returnUrl'] . '?complete=true');
            exit;
        }

        // Redirect the user to the authorization URL.
        header('Location: ' . $_SESSION['returnUrl'] . '?complete=true');
        exit;
    }

    private function getLibrary()
    {
        return new Fitbit([
            'clientId' => $_ENV['FITBIT_ID'],
            'clientSecret' => $_ENV['FITBIT_SECRET'],
            'redirectUri' => $_ENV['INSTALL_URL'] . '/auth/refresh/fitbit',
        ]);
    }
}
