<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2020. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;


use App\AppConstants;
use App\Entity\Exercise;
use App\Entity\Patient;
use App\Entity\PatientCredentials;
use App\Entity\PatientDevice;
use App\Entity\ThirdPartyService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use RedjanYm\FCMBundle\FCMClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TweetManager
{

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var FCMClient
     */
    private $fcmClient;

    public function __construct(
        ManagerRegistry $doctrine,
        FCMClient $fcmClient
    )
    {
        $this->doctrine = $doctrine;
        $this->fcmClient = $fcmClient;
    }

    /**
     * @param string      $title
     * @param string|null $body
     * @param Patient     $patient
     * @param bool        $private
     *
     * @param string|null $fileUrl
     *
     * @return array
     */
    public function sendNotification(string $title, ?string $body, Patient $patient, bool $private = FALSE, string $fileUrl = NULL)
    {
        $return = [];

        if ($private) {

            $title = $this->findUserNamesInMessage($title, $patient, TRUE);
            if (!is_null($body)) $body = $this->findUserNamesInMessage($body, $patient, FALSE);

            $this->sendNotificationChat($title, $body, $patient, $fileUrl);
            $this->sendNotificationPush($title, $body, $patient);
        } else {

            $title = $this->findUserNamesInMessage($title, $patient, FALSE);
            if (!is_null($body)) $body = $this->findUserNamesInMessage($body, $patient, FALSE);

            $this->sendChatToChannel($title, $body, $fileUrl);
        }

        return $return;
    }

    private function findUserNamesInMessage(?string $message, Patient $patient, bool $private)
    {
        if (is_null($message)) {
            return NULL;
        }

        $thirdPartyService = $this->doctrine->getRepository(ThirdPartyService::class)->findOneBy(['name' => "Synology Chat"]);
        if ($thirdPartyService) {
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $message);

            if ($private) {
                $message = str_replace("@" . $patient->getUuid(), "You", $message);
            }

            preg_match_all('/@([\w]+)/m', $message, $regs, PREG_PATTERN_ORDER);
            foreach ($regs[1] as $reg) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' UUID = ' . $reg);

                $userChatId = NULL;
                try {
                    $userChatId = $this->doctrine->getRepository(PatientCredentials::class)->createQueryBuilder('c')
                        ->leftJoin('c.patient', 'p')
                        ->andWhere('p.uuid = :patientId')
                        ->setParameter('patientId', $reg)
                        ->andWhere('c.service = :serviceId')
                        ->setParameter('serviceId', $thirdPartyService->getId())
                        ->select('c.token as userChatId')
                        ->getQuery()->getOneOrNullResult()['userChatId'];
                } catch (NonUniqueResultException $e) {
                }

                if (!is_null($userChatId) && is_numeric($userChatId)) {
                    AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' userChatId = ' . $userChatId);
                    $message = str_replace("@" . $reg, "@u:" . $userChatId, $message);
                }
            }
        }

        return $message;
    }

    private function sendNotificationChat(string $title, ?string $body, Patient $patient, string $fileUrl = NULL)
    {
        $thirdPartyService = $this->doctrine->getRepository(ThirdPartyService::class)->findOneBy(['name' => "Synology Chat"]);
        if ($thirdPartyService) {
            /** @var PatientCredentials $dbSynologyAccount */
            $dbSynologyAccount = $this->doctrine
                ->getRepository(PatientCredentials::class)
                ->findOneBy(['patient' => $patient, 'service' => $thirdPartyService]);

            if (!$dbSynologyAccount) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' User doesnt have a Synology account');
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Registered Email address is ' . $patient->getEmail());
                $foundUser = $this->findUserInChat($patient->getEmail());
                if (!is_null($foundUser)) {
                    AppConstants::writeToLog('debug_transform.txt', __LINE__ . '  The Bot can see the user');
                    AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . print_r($foundUser, TRUE));

                    $dbSynologyAccount = new PatientCredentials();
                    $dbSynologyAccount->setPatient($patient);
                    $dbSynologyAccount->setService($thirdPartyService);
                    $dbSynologyAccount->setToken($foundUser->user_id);

                    $manager = $this->doctrine->getManager();
                    $manager->persist($dbSynologyAccount);
                    $manager->flush();
                }
            }

            if ($dbSynologyAccount) {
                $this->sendChatToUser($title, $body, $dbSynologyAccount->getToken(), $fileUrl);
            }
        }
    }

    private function findUserInChat(string $emailAddress)
    {
        $chat_param = "webapi/entry.cgi?api=SYNO.Chat.External&method=user_list&version=2&token=%22" . $_ENV['SYNOLOGY_CHATBOT'] . "%22";

        $client = HttpClient::create();
        // code execution continues immediately; it doesn't wait to receive the response
        try {
            $response = $client->request('GET', $_ENV['SYNOLOGY_CHAT_URL'] . $chat_param);

            // trying to get the response contents will block the execution until
            // the full response contents are received
            $contents = json_decode($response->getContent(), FALSE);

            if ($contents->success) {
                foreach ($contents->data->users as $user) {
                    if (!is_null($user->user_props->email) && strtolower($emailAddress) == strtolower($user->user_props->email)) {
                        return $user;
                    }
                }
            }
        } catch (TransportExceptionInterface $e) {
        } catch (ClientExceptionInterface $e) {
        } catch (RedirectionExceptionInterface $e) {
        } catch (ServerExceptionInterface $e) {
        }

        return NULL;
    }

    private function sendChatToUser(string $title, ?string $body, int $userId, string $fileUrl = NULL)
    {
        $payload = [
            "text" => $title,
            "user_ids" => [
                $userId,
            ],
            "attachments" => [
                "callback_id" => "abc",
                "text" => "attachment",
                "actions" => [
                    "type" => "button",
                    "name" => "resp",
                    "value" => "ok",
                    "text" => "OK",
                    "style" => "green",
                ],
            ],
        ];

        if (!is_null($fileUrl)) {
            $payload['file_url'] = $fileUrl;
        }

        if (!is_null($body)) {
            $payload['text'] = $payload['text'] . "\n" . $body;
        }

        return $this->makeSynologyChatRequest('chatbot', $_ENV['SYNOLOGY_CHATBOT'], $payload);
    }

    private function makeSynologyChatRequest(string $method, string $apiKey, array $payload)
    {
        $chat_param = "webapi/entry.cgi?api=SYNO.Chat.External&method=" . $method . "&version=2&token=%22" . $apiKey . "%22";

        $contents = [];

        $client = HttpClient::create();
        // code execution continues immediately; it doesn't wait to receive the response
        try {
            $response = $client->request('POST', $_ENV['SYNOLOGY_CHAT_URL'] . $chat_param, [
                'body' => 'payload=' . json_encode($payload),
            ]);

            // trying to get the response contents will block the execution until
            // the full response contents are received
            $contents = json_decode($response->getContent(), TRUE);
        } catch (TransportExceptionInterface $e) {
        } catch (ClientExceptionInterface $e) {
        } catch (RedirectionExceptionInterface $e) {
        } catch (ServerExceptionInterface $e) {
        }

        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . print_r($contents, TRUE));

        return $contents;
    }

    private function sendNotificationPush(string $title, ?string $body, Patient $patient)
    {
        /** @var PatientDevice $dbPatientDevice */
        $dbPatientDevice = $this->doctrine
            ->getRepository(PatientDevice::class)
            ->findOneBy(['patient' => $patient], ['lastSeen' => 'DESC']);

        if ($dbPatientDevice && is_string($dbPatientDevice->getSms())) {
            $testNotice = $this->fcmClient->createDeviceNotification(
                $title,
                $body,
                $dbPatientDevice->getSms()
            );

            $this->fcmClient->sendNotification($testNotice);
        }
    }

    private function sendChatToChannel(string $title, ?string $body, string $fileUrl = NULL)
    {
        $payload = [
            "text" => $title,
            "attachments" => [
                "callback_id" => "abc",
                "text" => "attachment",
                "actions" => [
                    "type" => "button",
                    "name" => "resp",
                    "value" => "ok",
                    "text" => "OK",
                    "style" => "green",
                ],
            ],
        ];

        if (!is_null($fileUrl)) {
            $payload['file_url'] = $fileUrl;
        }

        if (!is_null($body)) {
            $payload['text'] = $payload['text'] . "\n" . $body;
        }

        return $this->makeSynologyChatRequest('incoming', $_ENV['SYNOLOGY_CHAT'], $payload);
    }

}
