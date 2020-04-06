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

namespace App\Command;

use App\AppConstants;
use App\Entity\ApiAccessLog;
use App\Entity\Patient;
use App\Entity\PatientCredentials;
use App\Entity\PatientSettings;
use App\Entity\SyncQueue;
use App\Entity\ThirdPartyService;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\TweetManager;
use App\Transform\Fitbit\Constants;
use DateTime;
use djchen\OAuth2\Client\Provider\Fitbit;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use MyBuilder\Bundle\CronosBundle\Annotation\Cron;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for sending our email messages from the database.
 *
 * @Cron(minute="/3", noLogs=true, server="web")
 */
class SyncFitbit extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'queue:fetch:fitbit';

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AwardManager
     */
    private $awardManager;

    /**
     * @var ChallengePve
     */
    private $challengePve;

    /**
     * @var TweetManager
     */
    private $tweetManager;

    /**
     * @var
     */
    private $syncDate;
    /**
     * @var
     */
    private $syncPeriod;
    /**
     * @var
     */
    private $userSubscriptions;

    /**
     * @required
     *
     * @param ManagerRegistry $doctrine
     * @param LoggerInterface $logger
     * @param AwardManager    $awardManager
     * @param ChallengePve    $challengePve
     * @param TweetManager    $tweetManager
     */
    public function dependencyInjection(
        ManagerRegistry $doctrine,
        LoggerInterface $logger,
        AwardManager $awardManager,
        ChallengePve $challengePve,
        TweetManager $tweetManager
    ): void
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->awardManager = $awardManager;
        $this->challengePve = $challengePve;
        $this->tweetManager = $tweetManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->syncServiceFitbit();
    }

    /**
     *
     */
    private function syncServiceFitbit()
    {
        /** @var ThirdPartyService $service */
        $service = AppConstants::getThirdPartyService($this->doctrine, "Fitbit");

        /** @var SyncQueue[] $patientCredentials */
        $serviceSyncQueues = $this->doctrine
            ->getRepository(SyncQueue::class)
            ->findBy(['service' => $service]);

        if (count($serviceSyncQueues) > 0) {
            foreach ($serviceSyncQueues as $serviceSyncQueue) {

                $accessToken = $this->getAccessToken($serviceSyncQueue->getCredentials());
                if (!$accessToken->hasExpired()) {
                    $transformerClassName = 'App\\Transform\\Fitbit\\Entry';
                    if (!class_exists($transformerClassName)) {
                        /** @noinspection SpellCheckingInspection */
                        AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . 'Couldn\'t find a Transformer for Fitbit');
                    } else {
                        $continueQueueItems = TRUE;
                        $continueQueueActions = TRUE;
                        $dbPatientSettingsUntilOR = NULL;

                        /** @var PatientSettings $dbPatientSettingsUntil */
                        $dbPatientSettingsUntil = $this->doctrine
                            ->getRepository(PatientSettings::class)
                            ->findOneBy(['patient' => $serviceSyncQueue->getCredentials()->getPatient(), 'service' => $serviceSyncQueue->getCredentials()->getService(), 'name' => 'until']);

                        if ($dbPatientSettingsUntil) {
                            try {
                                $serviceDeath = new DateTime($dbPatientSettingsUntil->getValue()[0]);
                            } catch (Exception $e) {
                                AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . "User has an end date for this service");
                                AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . $dbPatientSettingsUntil->getValue()[0]);

                                $entityManager = $this->doctrine->getManager();
                                $entityManager->remove($serviceSyncQueue);
                                $entityManager->flush();

                                $continueQueueItems = FALSE;
                            }

                            /** @var PatientSettings $dbPatientSettingsUntil */
                            $dbPatientSettingsUntilOR = $this->doctrine
                                ->getRepository(PatientSettings::class)
                                ->findOneBy(['patient' => $serviceSyncQueue->getCredentials()->getPatient(), 'service' => $serviceSyncQueue->getCredentials()->getService(), 'name' => 'untilOR']);
                            if ($dbPatientSettingsUntilOR) {
                                $dbPatientSettingsUntilOR = $dbPatientSettingsUntilOR->getValue();
                            } else {
                                AppConstants::writeToLog('debug_transform.txt', $serviceSyncQueue->getCredentials()->getPatient()->getUuid() . " has no overrides");

                                $entityManager = $this->doctrine->getManager();
                                $entityManager->remove($serviceSyncQueue);
                                $entityManager->flush();

                                $continueQueueItems = FALSE;
                            }

                            if ($serviceSyncQueue->getEndpoint() != "subscriptions")
                                AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . $serviceSyncQueue->getCredentials()->getPatient()->getUuid() . " stopped using this service on " . $serviceDeath->format("Y-m-d") . ". Updating " . $serviceSyncQueue->getCredentials()->getPatient()->getPronounTheir() . " overrides");

                            $continueQueueActions = FALSE;
                        }

                        if ($continueQueueItems) {

                            $endpoints = explode("::", $serviceSyncQueue->getEndpoint());

                            $serviceDataArray = [];

                            // Setup the sync metadata in first array item
                            $serviceDataArray[0] = [];
                            $serviceDataArray[0]['uuid'] = $serviceSyncQueue->getCredentials()->getPatient()->getUuid();
                            $serviceDataArray[0]['dateTime'] = $this->syncDate . " 00:00:00";
                            $serviceDataArray[0]['remoteId'] = sha1($serviceSyncQueue->getCredentials()->getPatient()->getId() .
                                $serviceSyncQueue->getCredentials()->getService()->getId() .
                                $serviceSyncQueue->getCredentials()->getService()->getName());

                            $serviceDataArray[0] = json_decode(json_encode($serviceDataArray[0]), FALSE);

                            foreach ($endpoints as $endpoint) {

                                if (!is_null($dbPatientSettingsUntilOR) && is_array($dbPatientSettingsUntilOR) && count($dbPatientSettingsUntilOR) > 0) {
                                    AppConstants::writeToLog('debug_transform.txt', "Starting endpoint: " . $endpoint);
                                    if (in_array($endpoint, $dbPatientSettingsUntilOR)) {
                                        AppConstants::writeToLog('debug_transform.txt', " Which is in the override");
                                        $continueQueueActions = TRUE;
                                    } else {
                                        AppConstants::writeToLog('debug_transform.txt', " Which is NOT in the override");
                                        $continueQueueActions = FALSE;
                                        $entityManager = $this->doctrine->getManager();
                                        $entityManager->remove($serviceSyncQueue);
                                        $entityManager->flush();
                                    }
                                }

                                if ($continueQueueActions) {
                                    if ($endpoint == "subscriptions") {
                                        $this->userSubscriptions = NULL;

                                        /** @var PatientSettings $patientSettings */
                                        $patientSettings = $this->doctrine
                                            ->getRepository(PatientSettings::class)
                                            ->findOneBy([
                                                'patient' => $serviceSyncQueue->getCredentials()->getPatient(),
                                                'service' => $service,
                                                'name' => 'enabledEndpoints',
                                            ]);

                                        if (!$patientSettings) {
                                            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . 'No supported end points');
                                        } else {
                                            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' Permission over ' . print_r($patientSettings->getValue(), TRUE));
                                            foreach ($patientSettings->getValue() as $settingsEndpoint) {
                                                if (
                                                    is_null($dbPatientSettingsUntilOR) ||
                                                    (
                                                        is_array($dbPatientSettingsUntilOR) &&
                                                        count($dbPatientSettingsUntilOR) > 0 &&
                                                        in_array($settingsEndpoint, $dbPatientSettingsUntilOR)
                                                    )
                                                ) {
                                                    $this->checkSubscription($settingsEndpoint, $accessToken, $serviceSyncQueue->getCredentials()->getPatient());
                                                }
                                            }

                                            $entityManager = $this->doctrine->getManager();
                                            $entityManager->remove($serviceSyncQueue);
                                            $entityManager->flush();
                                        }
                                    } else {
                                        AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " .
                                            'Updating ' . $endpoint .
                                            ' for ' . $serviceSyncQueue->getCredentials()->getPatient()->getUsername());

                                        $var = $this->pullBabel($accessToken, $serviceSyncQueue, $endpoint);
                                        $serviceDataArray[] = $var;
                                    }
                                }
                            }

                            if (!is_null($serviceDataArray) && count($serviceDataArray) > 1 && !empty($serviceDataArray[1])) {
                                $transformerClass = new $transformerClassName($this->logger, $serviceSyncQueue->getCredentials()->getPatient());

                                /** @noinspection PhpUndefinedMethodInspection */
                                $savedId = $transformerClass->transform($serviceSyncQueue->getEndpoint(), $serviceDataArray, $this->doctrine, $this->awardManager, $this->challengePve, $this->tweetManager);

                                if (is_array($savedId)) {
                                    $remove = TRUE;
                                    foreach ($savedId as $saved) {
                                        if ($saved < 0) {
                                            $remove = FALSE;
                                        }
                                    }

                                    if ($remove) {
                                        $entityManager = $this->doctrine->getManager();
                                        $entityManager->remove($serviceSyncQueue);
                                        $entityManager->flush();
                                    }
                                } else if ($savedId > 0) {
                                    $entityManager = $this->doctrine->getManager();
                                    $entityManager->remove($serviceSyncQueue);
                                    $entityManager->flush();
                                }
                            }

                        }
                    }
                } else {
                    AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . 'Credentials have expired for ' . $serviceSyncQueue->getCredentials()->getPatient()->getFirstName() . '. Will retry later');
                }
            }
        } /*else {
            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . 'No Fitbit jobs in the sync queue');
        }*/
    }

    /**
     * @param PatientCredentials $credentials
     *
     * @return AccessToken
     */
    private function getAccessToken(PatientCredentials $credentials)
    {
        return new AccessToken([
            'access_token' => $credentials->getToken(),
            'refresh_token' => $credentials->getRefreshToken(),
            'expires' => $credentials->getExpires()->format("U"),
        ]);
    }

    /**
     * @param             $settingsEndpoint
     * @param AccessToken $accessToken
     * @param Patient     $patient
     */
    private function checkSubscription($settingsEndpoint, AccessToken $accessToken, Patient $patient)
    {
        AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' $settingsEndpoint = ' . $settingsEndpoint);
        $serviceEndpoint = $this->convertEndpointToSubscription($settingsEndpoint);

        $subscriptionFound = FALSE;
        $subscriptionId = -1;

        if (!is_null($serviceEndpoint)) {
            $userSubscriptions = $this->pullSubscription($accessToken);
            if (count($userSubscriptions) > 0) {
//                AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' $userSubscription = ' . print_r($userSubscriptions, TRUE));
                $currentSubs = [];
                foreach ($userSubscriptions as $apiSubscription) {
                    if ($apiSubscription->collectionType == $serviceEndpoint) {
                        $subscriptionFound = TRUE;
                        $subscriptionId = $apiSubscription->subscriptionId;
                        break;
                    }
                }
            }

            if (!$subscriptionFound) {
//                AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' subscribing too ' . $serviceEndpoint . ' as ' . $patient->getId()."_".$serviceEndpoint);
                $subRequest = $this->postSubscription($accessToken, "/" . $serviceEndpoint, $patient->getId() . "_" . $serviceEndpoint);
//                AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . print_r($subRequest, TRUE));
            } else {
//                AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' already subscribed too ' . $serviceEndpoint . ' as ' . $subscriptionId);
            }
        }
    }

    /**
     * @param $endpoint
     *
     * @return string|null
     */
    private function convertEndpointToSubscription($endpoint)
    {
        switch ($endpoint) {
            case "BodyWeight":
                return "body";
                break;
            case "FitStepsDailySummary":
                return "activities";
                break;
            default:
                return NULL;
                break;
        }
    }

    /**
     * @param AccessToken $accessToken
     * @param string      $endpoint
     *
     * @return array
     */
    private function pullSubscription(AccessToken $accessToken, $endpoint = "")
    {
        if (is_null($this->userSubscriptions)) {
            $paths = [
                "https://api.fitbit.com/1/user/-/apiSubscriptions.json",
                "https://api.fitbit.com/1/user/-/activities/apiSubscriptions.json",
                "https://api.fitbit.com/1/user/-/foods/apiSubscriptions.json",
                "https://api.fitbit.com/1/user/-/sleep/apiSubscriptions.json",
                "https://api.fitbit.com/1/user/-/body/apiSubscriptions.json",
            ];

            $subReturn = [];

            foreach ($paths as $path) {
//                AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . $path);
                try {
                    $request = $this->getLibrary()->getAuthenticatedRequest('GET', $path, $accessToken);
                    $response = $this->getLibrary()->getParsedResponse($request);
//                    AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . print_r(json_decode(json_encode($response), FALSE), true));

                    foreach (json_decode(json_encode($response), FALSE)->apiSubscriptions as $item) {
                        $item->path = $path;
                        $subReturn[] = $item;
                    }
                } catch (IdentityProviderException $e) {
                }
            }
//            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . print_r($subReturn, true));

            $this->userSubscriptions = $subReturn;
        }

        return $this->userSubscriptions;
    }

    /**
     * @return Fitbit
     */
    private function getLibrary()
    {
        return new Fitbit([
            'clientId' => $_ENV['FITBIT_ID'],
            'clientSecret' => $_ENV['FITBIT_SECRET'],
            'redirectUri' => $_ENV['INSTALL_URL'] . '/auth/refresh/fitbit',
        ]);
    }

    /**
     * @param AccessToken $accessToken
     * @param string      $endpoint
     * @param string      $subId
     *
     * @return array|mixed
     */
    private function postSubscription(AccessToken $accessToken, $endpoint = "", $subId = "")
    {
        $path = "https://api.fitbit.com/1/user/-" . $endpoint . "/apiSubscriptions/" . $subId . ".json";

        try {
            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . $path);
            $request = $this->getLibrary()->getAuthenticatedRequest('POST', $path, $accessToken);
            // Make the authenticated API request and get the response.

            $responseHdr = $this->getLibrary()->getHeaders();
            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . print_r($responseHdr, TRUE));
            $response = $this->getLibrary()->getResponse($request);
            $responseObject = json_decode(json_encode($response), FALSE);
            return $responseObject;
        } catch (IdentityProviderException $e) {
            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . $e->getMessage());
        }

        return [];
    }

    /**
     * @param AccessToken $accessToken
     * @param SyncQueue   $serviceSyncQueue
     * @param string      $requestedEndpoint
     *
     * @return array|mixed
     */
    private function pullBabel(AccessToken $accessToken, SyncQueue $serviceSyncQueue, string $requestedEndpoint)
    {
        /** @var ApiAccessLog $patient */
        $apiAccessLog = $this->doctrine
            ->getRepository(ApiAccessLog::class)
            ->findLastAccess($serviceSyncQueue->getCredentials()->getPatient(), $serviceSyncQueue->getCredentials()->getService(), $requestedEndpoint);

        $path = $this->getApiPath($requestedEndpoint, $apiAccessLog);
        /*if ($requestedEndpoint == "BodyWeight") {
            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . $path);
        }*/

        if ($requestedEndpoint != "BodyWeight" &&
            $requestedEndpoint != "FitStepsDailySummary" &&
            $requestedEndpoint != "PatientGoals" &&
            $requestedEndpoint != "TrackingDevice" &&
            $requestedEndpoint != "Exercise") {
            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' Unsupported EndPoint - ' . $requestedEndpoint);
            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] -  " . $path);
        } else {
            try {
                if (strpos($path, '.json') !== FALSE) {
                    $request = $this->getLibrary()->getAuthenticatedRequest('GET', $path, $accessToken);
                } else {
                    $request = $this->getLibrary()->getAuthenticatedRequest('GET', $path . ".json", $accessToken);
                }
                $response = $this->getLibrary()->getParsedResponse($request);

                $responseObject = json_decode(json_encode($response), FALSE);
                /*if ($requestedEndpoint == "BodyWeight") {
                    AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . print_r($responseObject, true));
                }*/

                return $responseObject;
            } catch (IdentityProviderException $e) {
                AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . $e->getMessage());
            }
        }

        return [];
    }

    /**
     * @param                   $requestedEndpoint
     * @param ApiAccessLog|NULL $apiAccessLog
     *
     * @return bool|mixed|string
     */
    private function getApiPath($requestedEndpoint, ApiAccessLog $apiAccessLog = NULL)
    {
        $path = Constants::getPath($requestedEndpoint);

        if (is_null($path))
            return NULL;


        if (strpos($path, "{ext}") !== FALSE) {
            $path = str_replace("{ext}", ".json", $path);
        }

        if (strpos($path, "{date}") !== FALSE) {
            if (!$apiAccessLog) {
                $this->syncDate = date("Y-m-d");
            } else {
                $this->syncDate = $apiAccessLog->getLastPulled()->format("Y-m-d");
            }

            $path = str_replace("{date}", $this->syncDate, $path);
        }

        if (strpos($path, "{+31days}") !== FALSE) {
            $path = str_replace("{+31days}", date("Y-m-d"), $path);
        }

        if (strpos($path, "{today}") !== FALSE) {
            $path = str_replace("{today}", date("Y-m-d"), $path);
        }

        if (strpos($path, "{period}") !== FALSE) {
            $syncPeriod = $this->getDaysSyncPeriod();
            $path = str_replace("{period}", $syncPeriod, $path);
        }

        // AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Path = ' . $path);

        return $path;
    }

    /**
     * @return float|int|string
     */
    private function getDaysSyncPeriod()
    {
        $daysSince = (date("U") - strtotime($this->syncDate)) / (60 * 60 * 24);
        if ($daysSince < 8) {
            $daysSince = "7d";
        } else if ($daysSince < 30) {
            $daysSince = "30d";
        } else {
            $daysSince = "1m";
        }

        return $daysSince;
    }

    /**
     * @param AccessToken $accessToken
     * @param string      $endpoint
     * @param string      $subId
     *
     * @return array|mixed
     */
    private function deleteSubscription(AccessToken $accessToken, $endpoint = "", $subId = "")
    {
        $path = "https://api.fitbit.com/1/user/-" . $endpoint . "/apiSubscriptions/" . $subId . ".json";

        try {
            $request = $this->getLibrary()->getAuthenticatedRequest('DELETE', $path, $accessToken);
            // Make the authenticated API request and get the response.

            $response = $this->getLibrary()->getResponse($request);
            $responseObject = json_decode(json_encode($response), FALSE);

            return $responseObject;
        } catch (IdentityProviderException $e) {
            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . $e->getMessage());
        }

        return [];
    }

}
