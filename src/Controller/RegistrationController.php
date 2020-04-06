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

use App\AppConstants;
use App\Entity\Patient;
use App\Entity\PatientFriends;
use App\Entity\PatientMembership;
use App\Service\AwardManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationController
 *
 * @package App\Controller
 */
class RegistrationController extends AbstractController
{
    /**
     * @Route("/users/register", name="login_register")
     * @param ManagerRegistry              $doctrine
     * @param Request                      $request
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @param AwardManager                 $awardManager
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordEncoderInterface $passwordEncoder, AwardManager $awardManager)
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
        $requestJson->username = strtolower($requestJson->username);

        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . print_r([
                "username" => $requestJson->username,
                "email" => $requestJson->email,
                "invite" => $requestJson->invite,
                "password" => $requestJson->password,
            ], TRUE));
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
        $patient->setEmail($requestJson->email);
        //$patient->setAvatar('https://connect.core.nxfifteen.me.uk/new.jpg');
        $patient->setUiSettings(["showNavBar::false","showAsideBar::false"]);
        $patient->setPassword($passwordEncoder->encodePassword($patient, $requestJson->password));
        $patient->setRoles(['ROLE_USER', 'ROLE_BETA']);
        $patient->setApiToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
        $entityManager->persist($patient);

        $membership = new PatientMembership();
        $membership->setPatient($patient);
        $membership->setTear('alpha_user');
        $membership->setSince(new \DateTime());
        $membership->setLastPaid(new \DateTime());
        $membership->setActive(TRUE);
        $membership->setLifetime(TRUE);
        $entityManager->persist($membership);

        /** @var Patient $patientOwner */
        $patientOwner = $doctrine
            ->getRepository(Patient::class)
            ->findOneBy(["id" => 1]);
        if ($patientOwner) {
            $friendsWithOwner = new PatientFriends();
            $friendsWithOwner->setAccepted(true);
            $friendsWithOwner->setFriendA($patient);
            $friendsWithOwner->setFriendB($patientOwner);
            $entityManager->persist($friendsWithOwner);
        }

        $entityManager->flush();

        $awardManager->sendUserEmail(
            [$patient->getEmail() => $patient->getFirstName() . ' ' . $patient->getSurName()],
            'user_new',
            [
                'html_title' => 'Welcome',
                'header_image' => 'header1.png',
                'patients_name' => $patient->getUuid(),
                'relevant_date' => date("F jS, Y"),
                'relevant_url' => '/#/setup/profile',
            ]
        );

        $awardManager->sendUserEmail(
            [$_ENV['SITE_EMAIL_ADDRESS'] => $_ENV['SITE_EMAIL_NAME']],
            'admin_user_new',
            [
                'html_title' => 'Another Sign-up!',
                'header_image' => 'header1.png',
                'patients_name' => $patient->getUuid(),
                'patients_email' => $patient->getEmail(),
                'relevant_date' => date("F jS, Y"),
            ]
        );

        return $this->json(["username" => $patient->getUuid(), "token" => $patient->getApiToken(), "firstrun" => $patient->getFirstRun()]);
    }

    /**
     * @Route("/users/profile", name="get_profile")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function get_profile()
    {
        /** @var Patient $patient */
        $patient = $this->getUser();
        $this->hasAccess($patient->getUuid());

        $userProfile = [
            "username" => $patient->getUsername(),
            "firstName" => $patient->getFirstName(),
            "lastName" => $patient->getSurName(),
            "avatar" => $patient->getAvatar(),
            "email" => $patient->getEmail(),
            "height" => 0,
        ];

        $dob = $patient->getDateOfBirth();
        if (is_null($dob)) {
            $userProfile['dateOfBirth'] = "1900-12-01";
        } else {
            $userProfile['dateOfBirth'] = $dob->format("Y-m-d");
        }

        return $this->json($userProfile);
    }

    /**
     * @Route("/users/profile/save", name="save_profile")
     * @param ManagerRegistry              $doctrine
     * @param Request                      $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function save_profile(ManagerRegistry $doctrine, Request $request)
    {
        /** @var Patient $patient */
        $patient = $this->getUser();
        $this->hasAccess($patient->getUuid());

        $requestBody = $request->getContent();
        $requestBody = str_replace("'", "\"", $requestBody);
        $requestJson = json_decode($requestBody, FALSE);
        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . print_r($requestJson, TRUE));

        $entityManager = $doctrine->getManager();

        $patient->setFirstName($requestJson->firstName);
        $patient->setSurName($requestJson->lastName);
        $patient->setEmail($requestJson->email);
        if (!AppConstants::startsWith($requestJson->avatar, 'https://secure.gravatar.com')) {
            $patient->setAvatar($requestJson->avatar);
        } else {
            $patient->setAvatar(null);
        }
        $patient->setDateOfBirth(new \DateTime($requestJson->dateOfBirth));
        $patient->setFirstRun(false);
        $patient->setUiSettings(["showNavBar::lg","showAsideBar::false"]);

        $userProfile = [
            "username" => $patient->getUsername(),
            "firstName" => $patient->getFirstName(),
            "lastName" => $patient->getSurName(),
            "avatar" => $patient->getAvatar(),
            "email" => $patient->getEmail(),
            "dateOfBirth" => $patient->getDateOfBirth()->format("Y-m-d"),
            "height" => 0,
        ];

        $entityManager->persist($patient);



        $entityManager->flush();

        return $this->json($userProfile);
    }

    /**
     * @Route("/invite/{inviteCode}", name="login_register_invite")
     *
     * @param string                $inviteCode
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index_invite_code(string $inviteCode)
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
