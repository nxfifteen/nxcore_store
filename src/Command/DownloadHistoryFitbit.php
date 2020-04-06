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

namespace App\Command;

use App\AppConstants;
use App\Entity\Patient;
use App\Entity\PatientCredentials;
use App\Entity\PatientSettings;
use App\Entity\ThirdPartyService;
use App\Service\AwardManager;
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
 * @Cron(minute="/5", noLogs=true, server="web")
 */
class DownloadHistoryFitbit extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'history:download:fitbit';

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
     * @var Patient
     */
    private $patient;

    /**
     * @var ThirdPartyService
     */
    private $service;

    /**
     * @var array
     */
    private $patientSettings;

    /**
     * @var bool
     */
    private $hasHistoryMembership;

    /*private function setupCommand()
    {
        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
            ]);
        });
    }*/

    /**
     * @required
     *
     * @param ManagerRegistry $doctrine
     * @param LoggerInterface $logger
     * @param AwardManager    $awardManager
     */
    public function dependencyInjection(
        ManagerRegistry $doctrine,
        LoggerInterface $logger,
        AwardManager $awardManager
    ): void
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->awardManager = $awardManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->service = AppConstants::getThirdPartyService($this->doctrine, "Fitbit");

        /** @var PatientCredentials[] $patientCredentials */
        $patientCredentials = $this->doctrine
            ->getRepository(PatientCredentials::class)
            ->findBy(["service" => $this->service]);

        if (count($patientCredentials) > 0) {
            foreach ($patientCredentials as $patientCredential) {
                $this->patient = $patientCredential->getPatient();

                /** @var \DateTime $serviceBirth */
                try {
                    $serviceBirth = $this->getPatientSetting("from", TRUE);
                } catch (\Exception $e) {
                    $this->restUserLoop();
                    break;
                }
                /** @var \DateTime $serviceDeath */
                try {
                    $serviceDeath = $this->getPatientSetting("until", TRUE);
                } catch (\Exception $e) {
                    $serviceDeath = NULL;
                }

                if (is_null($serviceBirth)) {
                    $patientServiceProfile = $this->pullBabel($this->getAccessToken($patientCredential), new \DateTime(), new \DateTime(), 'serviceProfile');
                    if (!is_null($patientServiceProfile)) {
                        try {
                            $serviceBirth = new \DateTime($patientServiceProfile->user->memberSince);
                        } catch (\Exception $e) {
                            $this->restUserLoop();
                            break;
                        }

                        $this->updateUserSetting("from", $serviceBirth->format("Y-m-d H:i:s"));
                    }
                }

                if (!is_null($serviceBirth)) {
                    list($servicePullFrom, $premiumString) = $this->calcServicePullFromDate($serviceBirth);
                    /** @var \DateTime $serviceOldestPull */
                    try {
                        $serviceOldestPull = $this->getPatientSetting("oldestPull", TRUE);
                        if (is_null($serviceOldestPull)) {
                            if (!is_null($serviceDeath)) {
                                $serviceOldestPull = $serviceDeath;
                            } else {
                                $serviceOldestPull = new \DateTime();
                            }
                        }
                    } catch (\Exception $e) {
                        if (!is_null($serviceDeath)) {
                            $serviceOldestPull = $serviceDeath;
                        } else {
                            $serviceOldestPull = new \DateTime();
                        }
                    }

                    if ($serviceOldestPull->format("U") > $servicePullFrom->format("U")) {
                        $this->log("Downloading history for " . $premiumString . " " . $this->patient->getUsername() . " since " . $servicePullFrom->format("Y-m-d"));

                        try {
                            $supportedEndpoints = $this->getPatientSetting("enabledEndpoints");
                        } catch (\Exception $e) {
                            break;
                        }

                        foreach ($supportedEndpoints as $supportedEndpoint) {
                            $transformerClassName = 'App\\Transform\\Fitbit\\Entry';
                            if (!class_exists($transformerClassName)) {
                                $this->log("Couldn't find a Transformer for Fitbit");
                            } else {
                                $supportedEndpoint = str_ireplace("Daily", "Period", $supportedEndpoint);
                                if (
                                    $supportedEndpoint == "Exercise" ||
                                    ($supportedEndpoint !== "TrackingDevice" && strpos($supportedEndpoint, 'Period') !== FALSE)
                                ) {
                                    $loopWatchCount = 0;
                                    $loopWatchCountMax = 3;
                                    $loopWatchRef = strtotime('now');
                                    $loopWatchRefMax = $servicePullFrom->format('U');

                                    while ($loopWatchCount < $loopWatchCountMax && $loopWatchRef > $loopWatchRefMax) {
                                        $loopWatchCount++;
                                        $this->log("Pulling $supportedEndpoint since " . $serviceOldestPull->format("Y-m-d") . ", loop " . $loopWatchCount);

//                                        $patientServiceProfile = $this->pullBabel($this->getAccessToken($patientCredential), $serviceOldestPull, $serviceBirth, $supportedEndpoint);
//                                        if (!is_null($patientServiceProfile) && property_exists($patientServiceProfile, "activities-steps")) {
//                                            $lastDateTime = null;
//                                            if ($supportedEndpoint == "FitStepsPeriodSummary") {
//                                                $lastDateTime = $this->saveFitStepsPeriodSummary($transformerClassName, $servicePullFrom, $supportedEndpoint, $patientServiceProfile);
//                                            }
//
//                                            if (!is_null($lastDateTime)) {
//                                                $loopWatchCount = $loopWatchCountMax + 1;
//                                                $this->log("An DB error occurred");
//                                            } else {
//                                                $loopWatchRef = strtotime($lastDateTime);
//                                                $serviceOldestPull->setTimestamp($loopWatchRef);
//                                                $this->updateUserSetting("oldestPull", $lastDateTime);
//                                            }
//
//                                        } else {
//                                            $loopWatchCount = $loopWatchCountMax + 1;
//                                            $this->log("An API error occurred");
//                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $this->log("History for " . $premiumString . " " . $this->patient->getUsername() . " is fully up to date ");
                    }
                }

                $this->restUserLoop();
            }
        }
    }

    /**
     * @param string $settingKey
     *
     * @param bool   $returnDateTime
     *
     * @return mixed|null
     * @throws \Exception
     */
    private function getPatientSetting(string $settingKey, bool $returnDateTime = FALSE)
    {
        if (!is_array($this->patientSettings)) {
            /** @var PatientSettings[] $dbPatientSettings */
            $dbPatientSettings = $this->doctrine
                ->getRepository(PatientSettings::class)
                ->findBy(['patient' => $this->patient, 'service' => $this->service]);

            if (!$dbPatientSettings) {
                AppConstants::writeToLog('debug_transform.txt', "[" . DownloadHistoryFitbit::$defaultName . "] - No Settings");
                return NULL;
            }

            $this->patientSettings = [];

            foreach ($dbPatientSettings as $patientSetting) {
                if (count($patientSetting->getValue()) == 1) {
                    $this->patientSettings[$patientSetting->getName()] = $patientSetting->getValue()[0];
                } else {
                    $this->patientSettings[$patientSetting->getName()] = $patientSetting->getValue();
                }
            }
        }

        if (array_key_exists($settingKey, $this->patientSettings)) {
            if ($returnDateTime) {
                if (is_array($this->patientSettings[$settingKey]) && count($this->patientSettings[$settingKey]) > 1) {
                    $returnArray = [];
                    foreach ($this->patientSettings[$settingKey] as $setting) {
                        $returnArray[] = new \DateTime($setting);
                    }
                    return $returnArray;
                } else {
                    return new \DateTime($this->patientSettings[$settingKey]);
                }
            } else {
                return $this->patientSettings[$settingKey];
            }
        } else {
            return NULL;
        }
    }

    private function restUserLoop()
    {
        $this->hasHistoryMembership = NULL;
        $this->patientSettings = NULL;
        $this->patient = NULL;
    }

    /**
     * @param AccessToken $accessToken
     * @param \DateTime   $referenceTodayDate
     * @param \DateTime   $apiAccessLog
     * @param string      $requestedEndpoint
     *
     * @return object|array
     */
    private function pullBabel(AccessToken $accessToken, \DateTime $referenceTodayDate, \DateTime $apiAccessLog, string $requestedEndpoint)
    {
        if (!$accessToken->hasExpired()) {
            $path = $this->getApiPath($requestedEndpoint, $referenceTodayDate, $apiAccessLog);

            try {
                $request = $this->getLibrary()->getAuthenticatedRequest('GET', $path . ".json", $accessToken);
                $response = $this->getLibrary()->getParsedResponse($request);

                $responseObject = json_decode(json_encode($response), FALSE);

                return $responseObject;
            } catch (IdentityProviderException $e) {
                AppConstants::writeToLog('debug_transform.txt', "[" . DownloadHistoryFitbit::$defaultName . "] - " . ' ' . $e->getMessage());
            }
        } else {
            $this->log("Token Expired, will retry later");
        }

        return NULL;
    }

    /**
     * @param                   $requestedEndpoint
     * @param \DateTime         $apiAccessLog
     *
     * @return string|null
     */
    private function getApiPath($requestedEndpoint, \DateTime $referenceTodayDate, \DateTime $apiAccessLog)
    {
        $path = Constants::getPath($requestedEndpoint);

        if (is_null($path))
            return NULL;

        $daysSince = round(($referenceTodayDate->format("U") - $apiAccessLog->format("U")) / (60 * 60 * 24), 0, PHP_ROUND_HALF_UP);
        $syncDate = clone $apiAccessLog;
        if ($daysSince > 0) {
            $syncDate->modify("+ " . $daysSince . " days");
            if ($syncDate->format("Y-m-d") > $syncDate->format("Y-m-d")) {
                $syncDate = $syncDate->setTimestamp(strtotime('now'));
            }
        }
        $syncPeriod = $this->getDaysSyncPeriod($apiAccessLog->format("Y-m-d"));

        if (strpos($path, "{period}") !== FALSE) {
            $path = str_replace("{period}", $syncPeriod, $path);
        }

        if (strpos($path, "{date}") !== FALSE) {
            $path = str_replace("{date}", $syncDate->format("Y-m-d"), $path);
        }

//        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Path = ' . $path);

        return $path;
    }

    /**
     * @param $syncDate
     *
     * @return float|string
     */
    private function getDaysSyncPeriod($syncDate)
    {
        $daysSince = round((date("U") - strtotime($syncDate)) / (60 * 60 * 24), PHP_ROUND_HALF_UP);
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

    private function log($patientServiceProfile)
    {
        if (!is_string($patientServiceProfile)) {
            AppConstants::writeToLog(
                'debug_transform.txt',
                "[" . DownloadHistoryFitbit::$defaultName . "] - " . print_r($patientServiceProfile, TRUE)
            );
        } else {
            AppConstants::writeToLog(
                'debug_transform.txt',
                "[" . DownloadHistoryFitbit::$defaultName . "] - " . $patientServiceProfile
            );
        }
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

    private function updateUserSetting(string $string, string $serviceBirth)
    {
        $this->log("Updating " . $this->patient->getFirstName() . " for " . $this->service->getName() . " key " . $string . " to " . $serviceBirth);

        /** @var PatientSettings $dbPatientSettings */
        $dbPatientSetting = $this->doctrine
            ->getRepository(PatientSettings::class)
            ->findOneBy(['patient' => $this->patient, 'service' => $this->service, 'name' => $string]);

        if (!$dbPatientSetting) {
            $dbPatientSetting = new PatientSettings();
            $dbPatientSetting->setPatient($this->patient);
            $dbPatientSetting->setService($this->service);
            $dbPatientSetting->setName($string);
        }

        $dbPatientSetting->setValue([$serviceBirth]);

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($dbPatientSetting);
        $entityManager->flush();
    }

    private function calcServicePullFromDate(\DateTime $serviceBirth)
    {
        $servicePullFrom = clone $serviceBirth;
        if (!$this->isHistoryUser()) {
            $servicePullFrom->setTimestamp(strtotime('now'));
            $servicePullFrom->modify('- 2 months');
            $premiumString = 'regular user';
        } else if ($this->patient->getMembership()->getTear() == "beta_user") {
            $servicePullFrom->setTimestamp(strtotime('now'));
            $servicePullFrom->modify('- 18 months');
            $premiumString = 'beta tester';
        } else if ($this->patient->getMembership()->getTear() == "alpha_user") {
            $servicePullFrom->setTimestamp(strtotime('now'));
            $servicePullFrom->modify('- 24 months');
            $premiumString = 'alpha tester';
        } else if ($this->patient->getMembership()->getTear() == "yearly_history") {
            $servicePullFrom->setTimestamp(strtotime('now'));
            $servicePullFrom->modify('- 12 months');
            $premiumString = 'yearly supporter';
        } else if ($this->patient->getMembership()->getTear() == "all_history" || $this->patient->getMembership()->getLifetime()) {
            $premiumString = 'full supporter';
        } else {
            $servicePullFrom->setTimestamp(strtotime('now'));
            $servicePullFrom->modify('- 3 months');
            $premiumString = 'supporter';
        }

        if ($servicePullFrom->format("U") < $serviceBirth->format("U")) {
            $servicePullFrom = clone $serviceBirth;
        }
        return [$servicePullFrom, $premiumString];
    }

    /**
     * @return bool
     */
    private function isHistoryUser()
    {
        if (is_null($this->hasHistoryMembership)) {
            if (
                !is_null($this->patient->getMembership()) &&
                (
                    $this->patient->getMembership()->getLifetime() ||
                    $this->patient->getMembership()->getLastPaid()->format("U") >= (new \DateTime())->modify('- 31 days')->format("U")
                )
            ) {
                $this->hasHistoryMembership = TRUE;
            } else {
                $this->hasHistoryMembership = FALSE;
            }
        }

        return $this->hasHistoryMembership;
    }

    private function saveFitStepsPeriodSummary(string $transformerClassName, \DateTime $servicePullFrom, array $supportedEndpoint, array $patientServiceProfile)
    {
        $processDataArray = [];
        $processDataArray[0] = [
            'uuid' => $this->patient->getUuid(),
            'tracker' => 'Historical',
        ];
        $processDataArray[1] = [];
        foreach ($patientServiceProfile->{"activities-steps"} as $item) {
            if (strtotime($item->dateTime) > $servicePullFrom->format("U") && $item->value > 0) {
                $processDataArray[1][] = [
                    "dateTime" => $item->dateTime,
                    "value" => $item->value,
                ];
            }
        }

        $transformerClass = new $transformerClassName($this->logger);
        /** @noinspection PhpUndefinedMethodInspection */
        $savedId = $transformerClass->transform($supportedEndpoint, $processDataArray, $this->doctrine, $this->awardManager);

        return $patientServiceProfile->{'activities-steps'}[0]->dateTime;
    }
}
