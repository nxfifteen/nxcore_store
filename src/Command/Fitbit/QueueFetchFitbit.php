<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nxcore/
 * @link      https://gitlab.com/nx-core/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

/** @noinspection DuplicatedCode */

namespace App\Command\Fitbit;

use App\AppConstants;
use App\Entity\ApiAccessLog;
use App\Entity\Patient;
use App\Entity\PatientSettings;
use App\Entity\SyncQueue;
use App\Entity\ThirdPartyService;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\CommsManager;
use App\Transform\Fitbit\CommonFitbit;
use DateInterval;
use DateTime;
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
class QueueFetchFitbit extends Command
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
     * @var CommsManager
     */
    private $commsManager;

    /**
     * @var Patient
     */
    private $patient;

    /**
     * @var ThirdPartyService
     */
    private $service;

    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @param SyncQueue $serviceSyncQueue
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function actionSubscriptions(SyncQueue $serviceSyncQueue)
    {
        /** @var PatientSettings $enabledEndpoints */
        $enabledEndpoints = $this->doctrine
            ->getRepository(PatientSettings::class)
            ->findOneBy([
                'patient' => $this->patient,
                'service' => $this->service,
                'name' => 'enabledEndpoints',
            ]);

        if (!$enabledEndpoints) {
            $this->log('  No supported end points');
        } else {
            //$this->log(' Permission over ' . print_r($enabledEndpoints->getValue(), true));
            foreach ($enabledEndpoints->getValue() as $settingsEndpoint) {
                $serviceEndpoint = CommonFitbit::convertEndpointToSubscription($settingsEndpoint);

                $subscriptionFound = false;
                $subscriptionId = -1;

                if (!is_null($serviceEndpoint)) {
                    $this->log('  ' . $settingsEndpoint . ' is equal too ' . $serviceEndpoint);

                    $userSubscriptions = $this->pullSubscription();
                    if (count($userSubscriptions) > 0) {
                        foreach ($userSubscriptions as $apiSubscription) {
                            if ($apiSubscription->collectionType == $serviceEndpoint) {
                                $subscriptionFound = true;
                                $subscriptionId = $apiSubscription->subscriptionId;
                                break;
                            }
                        }
                    }

                    if (!$subscriptionFound) {
                        $this->log('   subscribing too ' . $serviceEndpoint . ' as ' . $this->patient->getId() . "_" . $serviceEndpoint);
                        $subRequest = $this->postSubscription("/" . $serviceEndpoint,
                            $this->patient->getId() . "_" . $serviceEndpoint);
                    } /*else {
                        $this->log('   already subscribed too ' . $serviceEndpoint . ' as ' . $subscriptionId);
                    }*/
                } /*else {
                    $this->log('  ' . $settingsEndpoint . ' has no endpoint');
                }*/
            }
        }
    }

    /**
     * @param string $endpoint
     * @param string $subId
     *
     * @return array|mixed
     */
    private function postSubscription($endpoint = "", $subId = "")
    {
        $path = "https://api.fitbit.com/1/user/-" . $endpoint . "/apiSubscriptions/" . $subId . ".json";

        try {
            $fitbitApp = CommonFitbit::getLibrary();
            $request = $fitbitApp->getAuthenticatedRequest('POST', $path, $this->accessToken);
            $response = $fitbitApp->getResponse($request);
            return json_decode(json_encode($response), false);
        } catch (Exception $e) {
            $this->log($e->getMessage());
        }

        return [];
    }

    /**
     * @param $patientServiceProfile
     */
    private function log($patientServiceProfile)
    {
        if (!is_string($patientServiceProfile)) {
            AppConstants::writeToLog(
                'debug_transform.txt',
                "[" . self::$defaultName . "] - " . print_r($patientServiceProfile, true)
            );
            echo "[" . self::$defaultName . "] - " . print_r($patientServiceProfile, true) . "\n";
        } else {
            AppConstants::writeToLog(
                'debug_transform.txt',
                "[" . self::$defaultName . "] - " . $patientServiceProfile
            );
            echo "[" . self::$defaultName . "] - " . $patientServiceProfile . "\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var ThirdPartyService $service */
        $service = AppConstants::getThirdPartyService($this->doctrine, "Fitbit");

        /** @var SyncQueue[] $patientCredentials */
        $serviceSyncQueues = $this->doctrine
            ->getRepository(SyncQueue::class)
            ->findBy(['service' => $service]);

        if (count($serviceSyncQueues) > 0) {
            foreach ($serviceSyncQueues as $serviceSyncQueue) {
                /** @var SyncQueue $serviceSyncQueue */
                $this->accessToken = CommonFitbit::getAccessToken($serviceSyncQueue->getCredentials());
                if (!$this->accessToken->hasExpired()) {
                    $this->patient = $serviceSyncQueue->getCredentials()->getPatient();
                    $this->service = $serviceSyncQueue->getCredentials()->getService();

                    $this->log("Downloading " . $serviceSyncQueue->getEndpoint() . " for " . $this->patient->getUsername());

                    $internalSyncQueueMethod = "action" . ucfirst($serviceSyncQueue->getEndpoint());
                    $this->log(" Looking for internal method: " . $internalSyncQueueMethod);

                    if (method_exists($this, $internalSyncQueueMethod)) {
                        $this->$internalSyncQueueMethod($serviceSyncQueue);
                    } else {
                        $transformerClassName = 'App\\Transform\\Fitbit\\' . ucfirst($serviceSyncQueue->getEndpoint());
                        if (class_exists($transformerClassName)) {
                            $this->log(" find the Transformer class: " . $transformerClassName . " for " . $serviceSyncQueue->getEndpoint());

                            /** @var DateTime $pullFromThisDate */
                            $pullFromThisDate = null;

                            /** @var ApiAccessLog $apiAccessLog */
                            $apiAccessLog = $this->doctrine
                                ->getRepository(ApiAccessLog::class)
                                ->findLastAccess($this->patient, $this->service, $serviceSyncQueue->getEndpoint());

                            if (!is_null($apiAccessLog)) {
                                $pullFromThisDate = clone $apiAccessLog->getLastRetrieved();
                                $pullFromThisDate->add(new DateInterval("P2D"));
                                $this->log("  The last pulled date was " . $pullFromThisDate->format("Y-m-d H:i:s"));
                            } else {
                                $this->log("  Didnt find a record in the API log");
                            }

                            if (!is_null($pullFromThisDate)) {
                                [$apiEndPointCalled, $apiEndPointResult] = CommonFitbit::getResponceFromApi(
                                    $this->accessToken,
                                    new DateTime(),
                                    $pullFromThisDate,
                                    $serviceSyncQueue->getEndpoint()
                                );

                                if (!is_null($apiEndPointCalled) && !is_null($apiEndPointResult)) {
                                    $transformerClass = new $transformerClassName($this->doctrine,
                                        $this->logger, $this->awardManager, $this->challengePve,
                                        $this->commsManager);
                                    $transformerClass->setPatientEntity($this->patient);
                                    $transformerClass->setCalledUrl($apiEndPointCalled);
                                    $transformerClass->setApiReturn($apiEndPointResult);
                                    $lastDateTime = $transformerClass->processData();
                                    if (is_null($lastDateTime)) {
                                        $this->log(__CLASS__ . '::' . __FUNCTION__ . '|' . __LINE__ . "  An DB error occurred");
                                    }
                                } else {
                                    $this->log(__CLASS__ . '::' . __FUNCTION__ . '|' . __LINE__ . "  An API error occurred");
                                    if (is_null($apiEndPointCalled)) {
                                        $this->log('   $apiEndPointCalled is NULL');
                                    }
                                    if (is_null($apiEndPointResult)) {
                                        $this->log('   $apiEndPointResult is NULL');
                                    }
                                }
                            }
                        } else {
                            $this->log(" Couldn't find the Transformer class: " . $transformerClassName);
                        }
                    }

                    $entityManager = $this->doctrine->getManager();
                    $entityManager->remove($serviceSyncQueue);
                    $entityManager->flush();
                } else {
                    $this->log('Credentials have expired for ' . $serviceSyncQueue->getCredentials()->getPatient()->getFirstName() . '. Will retry later');
                }

                unset($this->patient);
                unset($this->service);
                unset($this->accessToken);
            }
        } /*else {
            $this->log('No Fitbit jobs in the sync queue');
        }*/
    }

    /**
     * @required
     *
     * @param ManagerRegistry $doctrine
     * @param LoggerInterface $logger
     * @param AwardManager    $awardManager
     * @param ChallengePve    $challengePve
     * @param CommsManager    $commsManager
     */
    public function dependencyInjection(
        ManagerRegistry $doctrine,
        LoggerInterface $logger,
        AwardManager $awardManager,
        ChallengePve $challengePve,
        CommsManager $commsManager
    ): void {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->awardManager = $awardManager;
        $this->challengePve = $challengePve;
        $this->commsManager = $commsManager;
    }

    /**
     * @param string $endpoint
     *
     * @return array
     */
    private function pullSubscription($endpoint = "")
    {
        $paths = [
            "https://api.fitbit.com/1/user/-/apiSubscriptions.json",
            "https://api.fitbit.com/1/user/-/activities/apiSubscriptions.json",
            "https://api.fitbit.com/1/user/-/foods/apiSubscriptions.json",
            "https://api.fitbit.com/1/user/-/sleep/apiSubscriptions.json",
            "https://api.fitbit.com/1/user/-/body/apiSubscriptions.json",
        ];

        $subReturn = [];

        foreach ($paths as $path) {
            try {
                $fitbitApp = CommonFitbit::getLibrary();
                $request = $fitbitApp->getAuthenticatedRequest('GET', $path, $this->accessToken);
                $response = $fitbitApp->getParsedResponse($request);

                foreach (json_decode(json_encode($response), false)->apiSubscriptions as $item) {
                    $item->path = $path;
                    $subReturn[] = $item;
                }
            } catch (IdentityProviderException $e) {
            }
        }

        return $subReturn;
    }
}
