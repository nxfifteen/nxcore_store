<?php

namespace App\Controller;

use App\AppConstants;
use App\Entity\Patient;
use Sentry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginAuthenticatorController extends AbstractController
{
    private $requestJson;
    
    /**
     * @Route("/users/authenticate", name="login_authenticator")
     * @param Request                      $request
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $requestBody = $request->getContent();
        $requestBody = str_replace("'", "\"", $requestBody);
        $this->requestJson = json_decode($requestBody, FALSE);

        $this->requestJson->username = strtolower($this->requestJson->username);

        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setUser([
                'username' => $this->requestJson->username,
            ]);
        });

        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findOneBy(['uuid' => $this->requestJson->username]);

        if (!$patient || !$passwordEncoder->isPasswordValid($patient, $this->requestJson->password)) {
            if (!$patient) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' No matching user ' . $this->requestJson->username);
            } else if (!$passwordEncoder->isPasswordValid($patient, $this->requestJson->password)) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Invalid password for ' . $this->requestJson->username);
            }
            $exception = $this->createAccessDeniedException("Invalid login");
            throw $exception;
        }

        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' user ' . $this->requestJson->username . ' logged in');

        $returnPatient = [];
        $returnPatient['username'] = $patient->getUuid();
        $returnPatient['token'] = $patient->getApiToken();
        $returnPatient['firstrun'] = $patient->getFirstRun();

        return $this->json($returnPatient);
    }
}
