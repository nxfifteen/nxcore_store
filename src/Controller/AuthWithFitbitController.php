<?php

namespace App\Controller;

use App\AppConstants;
use App\Entity\Patient;
use App\Entity\PatientCredentials;
use App\Entity\ThirdPartyService;
use djchen\OAuth2\Client\Provider\Fitbit;
use Doctrine\Common\Persistence\ManagerRegistry;
use League\OAuth2\Client\Token\AccessToken;
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
    public function index(ManagerRegistry $doctrine, RequestStack $request, String $uuid)
    {
        $this->hasAccess($uuid);

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
        $_SESSION['returnUrl'] = $request->getMasterRequest()->get("return");
        $_SESSION['oauth2state'] = $provider->getState();

        // Redirect the user to the authorization URL.
        header('Location: ' . $authorizationUrl);
        exit;
    }

    /**
     * @Route("/auth/callback/fitbit", name="auth_with_fitbit_callback")
     * @param ManagerRegistry $doctrine
     * @param RequestStack    $request
     *
     * @return void
     */
    public function callback(ManagerRegistry $doctrine, RequestStack $request)
    {
        $queryString = $request->getCurrentRequest();
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
            $service = $this->getThirdPartyService($doctrine, "Fitbit");

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

            $entityManager = $doctrine->getManager();
            $entityManager->persist($patientCredentials);
            $entityManager->flush();
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Failed to get the access token or user details.
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $e->getMessage());
        }

        // Redirect the user to the authorization URL.
        header('Location: ' . $_SESSION['returnUrl'] . '/#/setup/fitbit?complete=true');
        exit;
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return ThirdPartyService|null
     */
    private static function getThirdPartyService(ManagerRegistry $doctrine, String $serviceName)
    {
        /** @var ThirdPartyService $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(ThirdPartyService::class)->findOneBy(['name' => $serviceName]);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            $entityManager = $doctrine->getManager();
            $thirdPartyService = new ThirdPartyService();
            $thirdPartyService->setName($serviceName);
            $entityManager->persist($thirdPartyService);
            $entityManager->flush();

            return $thirdPartyService;
        }
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
