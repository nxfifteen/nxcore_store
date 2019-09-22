<?php

namespace App\Controller;

use App\AppConstants;
use App\Entity\Patient;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginAuthenticatorController extends AbstractController
{
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
        $requestJson = json_decode($requestBody, FALSE);

        $requestJson->username = strtolower($requestJson->username);

        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findOneBy(['uuid' => $requestJson->username]);

        if (!$patient || !$passwordEncoder->isPasswordValid($patient, $requestJson->password)) {
            if (!$patient) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' No matching user ' . $requestJson->username);
            } elseif (!$passwordEncoder->isPasswordValid($patient, $requestJson->password)) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Invalid password for ' . $requestJson->username);
            }
            $exception = $this->createAccessDeniedException("Invalid login");
            throw $exception;
        }

        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' user ' . $requestJson->username . ' logged in');

        $returnPatient = [];
        $returnPatient['username'] = $patient->getUuid();
        $returnPatient['token'] = $patient->getApiToken();
        $returnPatient['firstrun'] = $patient->getFirstRun();

        return $this->json($returnPatient);
    }
}
