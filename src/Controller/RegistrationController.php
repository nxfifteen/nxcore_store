<?php
/**
 * Created by IntelliJ IDEA.
 * User: stuar
 * Date: 15/09/2019
 * Time: 18:58
 */

namespace App\Controller;

use App\AppConstants;
use App\Entity\Patient;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/users/register", name="login_register")
     * @param ManagerRegistry              $doctrine
     * @param Request                      $request
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $blockedUserNames = [
            "root",
            "admin",
            "core",
            "nxfifteen",
            "nx15"
        ];

        $requestBody = $request->getContent();
        $requestBody = str_replace("'", "\"", $requestBody);
        $requestJson = json_decode($requestBody, FALSE);
        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . print_r($requestJson, TRUE));
        AppConstants::writeToLog('debug_transform.txt',
            __LINE__ . ' New user registration ' . $requestJson->username . ' ' . $requestJson->email);

        if (!hash_equals($requestJson->invite, $_ENV['CORE_INVITE_CODE'])) {
            AppConstants::writeToLog('debug_transform.txt',
                __LINE__ . ' Invalid Token'
            );

            $exception = $this->createAccessDeniedException("Invalid invite");
            throw $exception;
        }

        if (in_array($requestJson->username, $blockedUserNames)) {
            AppConstants::writeToLog('debug_transform.txt',
                __LINE__ . ' Username blocked'
            );

            $exception = $this->createAccessDeniedException("Invalid invite");
            throw $exception;
        }

        if (!hash_equals($requestJson->password, $requestJson->passwordConfirm)) {
            AppConstants::writeToLog('debug_transform.txt',
                __LINE__ . ' Passwords do not match'
            );

            $exception = $this->createAccessDeniedException("Invalid invite");
            throw $exception;
        }

        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findOneBy(['uuid' => $requestJson->username]);
        if ($patient) {
            AppConstants::writeToLog('debug_transform.txt',
                __LINE__ . ' Username already in use'
            );

            $exception = $this->createAccessDeniedException("Invalid invite");
            throw $exception;
        }
        unset($patient);

        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findOneBy(['email' => $requestJson->email]);
        if ($patient) {
            AppConstants::writeToLog('debug_transform.txt',
                __LINE__ . ' Email already in use'
            );

            $exception = $this->createAccessDeniedException("Invalid invite");
            throw $exception;
        }
        unset($patient);

        $entityManager = $doctrine->getManager();
        $patient = new Patient();
        $patient->setUuid($requestJson->username);
        $patient->setFirstName($requestJson->name);
        $patient->setEmail($requestJson->email);
        $patient->setAvatar('https://connect.core.nxfifteen.me.uk/new.jpg');
        $patient->setUiSettings(["showNavBar::lg","showAsideBar::false"]);
        $patient->setPassword($passwordEncoder->encodePassword($patient, $requestJson->password));
        $patient->setRoles(['ROLE_USER', 'ROLE_BETA']);
        $patient->setApiToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
        $entityManager->persist($patient);
        $entityManager->flush();

        return $this->json(["username" => $patient->getUuid(), "token" => $patient->getApiToken(), "firstrun" => $patient->getFirstRun()]);
    }

    /**
     * @Route("/invite/{inviteCode}", name="login_register_invite")
     *
     * @param string                $inviteCode
     *
     * @param ContainerBagInterface $params
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_invite_code(string $inviteCode, ContainerBagInterface $params)
    {
        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Invite Code ' . $inviteCode);

        return $this->json(["status" => TRUE]);
    }

    /*
     * @Route("/users/update/password", name="login_update_password")
     * @param ManagerRegistry              $doctrine
     * @param Request                      $request
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    /*public function update_password(ManagerRegistry $doctrine, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $requestBody = $request->getContent();
        $requestBody = str_replace("'", "\"", $requestBody);
        $requestJson = json_decode($requestBody, FALSE);
        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . print_r($requestJson, true));

        $patient = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findOneBy(['uuid' => $requestJson->username]);

        if (!$patient) {
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' No matching user');
            $exception = $this->createAccessDeniedException("User tried to access another users information");
            throw $exception;
        }

        $entityManager = $doctrine->getManager();
        $patient->setPassword($passwordEncoder->encodePassword($patient, ''));
        $entityManager->persist($patient);
        $entityManager->flush();


        return $this->json(["status" => true]);
    }*/
}