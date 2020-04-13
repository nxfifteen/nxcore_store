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

namespace App\Service;


use App\AppConstants;
use App\Entity\Patient;
use App\Entity\PatientCredentials;
use App\Entity\PatientDevice;
use App\Entity\ThirdPartyService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use RedjanYm\FCMBundle\FCMClient;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class CommsManager
 *
 * @package App\Service
 */
class CommsManager
{

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var FCMClient
     */
    private $fcmClient;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * CommsManager constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param Swift_Mailer    $mailer
     * @param Environment     $twig
     * @param FCMClient       $fcmClient
     */
    public function __construct(
        ManagerRegistry $doctrine,
        Swift_Mailer $mailer,
        Environment $twig,
        FCMClient $fcmClient
    ) {
        $this->doctrine = $doctrine;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->fcmClient = $fcmClient;
    }

    /**
     * @param string $emailAddress
     *
     * @return |null
     */
    private function findUserInChat(string $emailAddress)
    {
        $chat_param = "webapi/entry.cgi?api=SYNO.Chat.External&method=user_list&version=2&token=%22" . $_ENV['SYNOLOGY_CHATBOT'] . "%22";

        $client = HttpClient::create();
        // code execution continues immediately; it doesn't wait to receive the response
        try {
            $response = $client->request('GET', $_ENV['SYNOLOGY_CHAT_URL'] . $chat_param);

            // trying to get the response contents will block the execution until
            // the full response contents are received
            $contents = json_decode($response->getContent(), false);

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

        return null;
    }

    /**
     * @param string|null $message
     * @param Patient     $patient
     * @param bool        $private
     *
     * @return mixed|string|null
     */
    private function findUserNamesInMessage(?string $message, Patient $patient, bool $private)
    {
        if (is_null($message)) {
            return null;
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

                $userChatId = null;
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

    /**
     * @param string $method
     * @param string $apiKey
     * @param array  $payload
     *
     * @return array|mixed
     */
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
            $contents = json_decode($response->getContent(), true);
        } catch (TransportExceptionInterface $e) {
        } catch (ClientExceptionInterface $e) {
        } catch (RedirectionExceptionInterface $e) {
        } catch (ServerExceptionInterface $e) {
        }

        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . print_r($contents, TRUE));

        return $contents;
    }

    /**
     * @param string      $title
     * @param string|null $body
     * @param string|NULL $fileUrl
     *
     * @return array|mixed
     */
    private function sendChatToChannel(string $title, ?string $body, string $fileUrl = null)
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

    /**
     * @param string      $title
     * @param string|null $body
     * @param int         $userId
     * @param string|NULL $fileUrl
     *
     * @return array|mixed
     */
    private function sendChatToUser(string $title, ?string $body, int $userId, string $fileUrl = null)
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

    /**
     * @param string      $title
     * @param string|null $body
     * @param Patient     $patient
     * @param string|NULL $fileUrl
     */
    private function sendNotificationChat(string $title, ?string $body, Patient $patient, string $fileUrl = null)
    {
        $thirdPartyService = $this->doctrine->getRepository(ThirdPartyService::class)->findOneBy(['name' => "Synology Chat"]);
        if ($thirdPartyService) {
            /** @var PatientCredentials $dbSynologyAccount */
            $dbSynologyAccount = $this->doctrine
                ->getRepository(PatientCredentials::class)
                ->findOneBy(['patient' => $patient, 'service' => $thirdPartyService]);

            if (!$dbSynologyAccount) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' User doesnt have a Synology account');
                AppConstants::writeToLog('debug_transform.txt',
                    __LINE__ . ' Registered Email address is ' . $patient->getEmail());
                $foundUser = $this->findUserInChat($patient->getEmail());
                if (!is_null($foundUser)) {
                    AppConstants::writeToLog('debug_transform.txt', __LINE__ . '  The Bot can see the user');
                    AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . print_r($foundUser, true));

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

    /**
     * @param string      $title
     * @param string|null $body
     * @param Patient     $patient
     */
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

    public function discord(string $channel, string $message, array $embeds = [])
    {
        $webhookurl = $_ENV['DISCORD_WH_' . $channel];

        $json_data = json_encode([
            // Message
            "content" => $message,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init($webhookurl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
        // echo $response;
        curl_close($ch);
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
    public function sendNotification(
        string $title,
        ?string $body,
        Patient $patient,
        bool $private = false,
        string $fileUrl = null
    ) {
        $return = [];

        if ($private) {

            $title = $this->findUserNamesInMessage($title, $patient, true);
            if (!is_null($body)) {
                $body = $this->findUserNamesInMessage($body, $patient, false);
            }

            $this->sendNotificationChat($title, $body, $patient, $fileUrl);
            $this->sendNotificationPush($title, $body, $patient);
        } else {

            $title = $this->findUserNamesInMessage($title, $patient, false);
            if (!is_null($body)) {
                $body = $this->findUserNamesInMessage($body, $patient, false);
            }

            $this->sendChatToChannel($title, $body, $fileUrl);
        }

        return $return;
    }

    /**
     * @param array  $setTo
     * @param string $setTemplateName
     * @param array  $setTemplateVariables
     */
    public function sendUserEmail(array $setTo, string $setTemplateName, array $setTemplateVariables)
    {
        $setTemplateVariables = array_merge($setTemplateVariables,
            [
                'store_domain' => $_ENV['INSTALL_URL'],
                'ui_domain' => $_ENV['UI_URL'],
                'asset_domain' => $_ENV['ASSET_URL'],
            ]
        );

        // Create the message
        try {
            $message = (new Swift_Message())
                // Add subject
                ->setSubject($setTemplateVariables['html_title'])
                //Put the From address
                ->setFrom([$_ENV['SITE_EMAIL_NOREPLY'] => $_ENV['SITE_EMAIL_NAME']])
                ->setBody(
                    $this->twig->render(
                        'emails/' . $setTemplateName . '.html.twig',
                        $setTemplateVariables
                    ),
                    'text/html'
                )
                // you can remove the following code if you don't define a text version for your emails
                ->addPart(
                    $this->twig->render(
                        'emails/' . $setTemplateName . '.txt.twig',
                        $setTemplateVariables
                    ),
                    'text/plain'
                );

            if (count($setTo) > 1) {
                // Include several To addresses
                $message->setTo([$_ENV['SITE_EMAIL_NOREPLY'] => $_ENV['SITE_EMAIL_NAME']]);
                // Include several To addresses
                $message->setBcc($setTo);
            } else {
                // Include several To addresses
                $message->setTo($setTo);
            }

            $this->mailer->send($message);
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }
    }

    public function social(string $message, string $channel, string $service = "all", Patient $patient = null)
    {
        if ($service == "all" || $service == "discord") {
            $this->discord($channel, $message);
        } else {
            if (!is_null($patient) && ($service == "all" || $service == "synology")) {
                $this->sendNotification(
                    "",
                    $message,
                    null,
                    false
                );
            }
        }
    }
}
