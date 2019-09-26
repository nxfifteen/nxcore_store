<?php

namespace App\Command;

use App\AppConstants;
use App\Entity\ApiAccessLog;
use App\Entity\PatientCredentials;
use App\Entity\SyncQueue;
use App\Entity\ThirdPartyService;
use App\Transform\Fitbit\Constants;
use djchen\OAuth2\Client\Provider\Fitbit;
use Doctrine\Common\Persistence\ManagerRegistry;
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

    private $syncDate;
    private $syncPeriod;

    /**
     * @required
     *
     * @param ManagerRegistry $doctrine
     */
    public function dependencyInjection(
        ManagerRegistry $doctrine,
        LoggerInterface $logger
    ): void
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->syncServiceFitbit();
    }

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
                AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' Job ' .
                    $serviceSyncQueue->getId() . ' :: Updating ' .
                    $serviceSyncQueue->getService()->getName() . ':' . $serviceSyncQueue->getEndpoint()
                    . ' for ' . $serviceSyncQueue->getCredentials()->getPatient()->getUsername());

                $accessToken = $this->getAccessToken($serviceSyncQueue->getCredentials());
                if (!$accessToken->hasExpired()) {
                    $transformerClassName = 'App\\Transform\\Fitbit\\Entry';
                    if (!class_exists($transformerClassName)) {
                        /** @noinspection SpellCheckingInspection */
                        AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . 'Couldn\'t find a Transformer for Fitbit');
                    } else {
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
                            $serviceDataArray[] = $this->pullBabel($accessToken, $serviceSyncQueue, $endpoint);
                        }

                        if (!is_null($serviceDataArray)) {
                            $transformerClass = new $transformerClassName($this->logger);
                            /** @noinspection PhpUndefinedMethodInspection */
                            $savedId = $transformerClass->transform($serviceSyncQueue->getEndpoint(), $serviceDataArray, $this->doctrine);

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
                } else {
                    AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . 'Credentials have expired. Will retry later');
                }
            }
        } else {
            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' ' . 'No Fitbit jobs in the sync queue');
        }
    }

    private function getAccessToken(PatientCredentials $credentials)
    {
        return new AccessToken([
            'access_token' => $credentials->getToken(),
            'refresh_token' => $credentials->getRefreshToken(),
            'expires' => $credentials->getExpires()->format("U"),
        ]);
    }

    private function pullBabel(AccessToken $accessToken, SyncQueue $serviceSyncQueue, string $requestedEndpoint)
    {
        /** @var ApiAccessLog $patient */
        $apiAccessLog = $this->doctrine
            ->getRepository(ApiAccessLog::class)
            ->findLastAccess($serviceSyncQueue->getCredentials()->getPatient(), $serviceSyncQueue->getCredentials()->getService(), $requestedEndpoint);

        $path = $this->getApiPath($requestedEndpoint, $apiAccessLog);

        if ($requestedEndpoint != "BodyWeight" &&
            $requestedEndpoint != "FitStepsDailySummary" &&
            $requestedEndpoint != "PatientGoals" &&
            $requestedEndpoint != "TrackingDevice") {
            AppConstants::writeToLog('debug_transform.txt', "[" . SyncFitbit::$defaultName . "] - " . ' Unsupported EndPoint - ' . $path);
        } else {
            try {
                $request = $this->getLibrary()->getAuthenticatedRequest('GET', $path . ".json", $accessToken);
                $response = $this->getLibrary()->getParsedResponse($request);

                $responseObject = json_decode(json_encode($response), FALSE);

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

        if (strpos($path, "{date}") !== FALSE) {
            if (!$apiAccessLog) {
                $this->syncDate = date("Y-m-d");
            } else {
                $this->syncDate = $apiAccessLog->getLastPulled()->format("Y-m-d");
            }

            $path = str_replace("{date}", $this->syncDate, $path);
        }

        if (strpos($path, "{period}") !== FALSE) {
            $syncPeriod = $this->getDaysSyncPeriod();
            $path = str_replace("{period}", $syncPeriod, $path);
        }

//        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Path = ' . $path);

        return $path;
    }

    private function getDaysSyncPeriod()
    {
        $daysSince = (date("U") - strtotime($this->syncDate)) / (60 * 60 * 24);
        if ($daysSince < 8) {
            $daysSince = "7d";
        } else if ($daysSince < 30) {
            $daysSince = "30d";
        } else if ($daysSince < 90) {
            $daysSince = "3m";
        } else if ($daysSince < 180) {
            $daysSince = "6m";
        } else if ($daysSince < 364) {
            $daysSince = "1y";
        } else {
            $daysSince = "1y";
        }

        return $daysSince;
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
