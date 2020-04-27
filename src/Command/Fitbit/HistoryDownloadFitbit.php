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
use App\Entity\Patient;
use App\Entity\PatientCredentials;
use App\Entity\PatientSettings;
use App\Entity\ThirdPartyService;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\CommsManager;
use App\Transform\Fitbit\CommonFitbit;
use App\Transform\Fitbit\Constants;
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
 * @Cron(minute="/5", noLogs=true, server="web")
 */
class HistoryDownloadFitbit extends Command
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
     * @param DateTime $serviceBirth
     *
     * @return array
     * @throws Exception
     */
    private function calcServicePullFromDate(DateTime $serviceBirth)
    {
        $servicePullFrom = clone $serviceBirth;
        if (!$this->getHasHistoryMembership()) {
            $servicePullFrom->setTimestamp(strtotime('now'));
            $servicePullFrom->modify('- 2 months');
            $premiumString = 'regular user';
        } else {
            if ($this->patient->getMembership()->getTear() == "beta_user") {
                $servicePullFrom->setTimestamp(strtotime('now'));
                $servicePullFrom->modify('- 18 months');
                $premiumString = 'beta tester';
            } else {
                if ($this->patient->getMembership()->getTear() == "alpha_user") {
                    $servicePullFrom->setTimestamp(strtotime('now'));
                    $servicePullFrom->modify('- 24 months');
                    $premiumString = 'alpha tester';
                } else {
                    if ($this->patient->getMembership()->getTear() == "yearly_history") {
                        $servicePullFrom->setTimestamp(strtotime('now'));
                        $servicePullFrom->modify('- 12 months');
                        $premiumString = 'yearly supporter';
                    } else {
                        if ($this->patient->getMembership()->getTear() == "all_history" || $this->patient->getMembership()->getLifetime()) {
                            $premiumString = 'full supporter';
                        } else {
                            $servicePullFrom->setTimestamp(strtotime('now'));
                            $servicePullFrom->modify('- 3 months');
                            $premiumString = 'supporter';
                        }
                    }
                }
            }
        }

        if ($servicePullFrom->format("U") < $serviceBirth->format("U")) {
            $servicePullFrom = clone $serviceBirth;
        }
        return [$servicePullFrom, $premiumString];
    }

    /**
     * @param $syncDate
     *
     * @return float|string
     */
    private function calculateLastSyncPeriod($syncDate)
    {
        $daysSince = round((date("U") - strtotime($syncDate)) / (60 * 60 * 24), PHP_ROUND_HALF_UP);
        if ($daysSince < 8) {
            $daysSince = "7d";
        } else {
            if ($daysSince < 30) {
                $daysSince = "30d";
            } else {
                if ($daysSince < 90) {
                    $daysSince = "3m";
                } else {
                    if ($daysSince < 180) {
                        $daysSince = "6m";
                    } else {
                        if ($daysSince < 364) {
                            $daysSince = "1y";
                        } else {
                            $daysSince = "1y";
                        }
                    }
                }
            }
        }

        return $daysSince;
    }

    /**
     * @param string $endpoint
     *
     * @return string|null
     */
    private function getFitbitApiPathFromEndpoint(string $endpoint)
    {
        switch ($endpoint) {
            case 'BodyWeight':
                $path = '/body/log/weight/date/{date}/1m{ext}';
                break;

            default:
                return null;
        }

        return Constants::FITBIT_COM . "/1/user/-$path";
    }

    /**
     * @param AccessToken $accessToken
     * @param DateTime    $referenceTodayDate
     * @param DateTime    $apiAccessLog
     * @param string      $requestedEndpoint
     *
     * @return array[string, object]
     */
    private function getFitbitApiResponce(
        AccessToken $accessToken,
        DateTime $referenceTodayDate,
        DateTime $apiAccessLog,
        string $requestedEndpoint
    ) {
        if (!$accessToken->hasExpired()) {
            $path = $this->getFitbitApiUrl($requestedEndpoint, $referenceTodayDate, $apiAccessLog);
            //$this->log('$path is ' . $path);

            try {
                $fitbitApp = CommonFitbit::getLibrary();
                $request = $fitbitApp->getAuthenticatedRequest('GET', $path, $accessToken);
                $response = $fitbitApp->getParsedResponse($request);

                $responseObject = json_decode(json_encode($response), false);

                return [$path, $responseObject];
            } catch (IdentityProviderException $e) {
                AppConstants::writeToLog('debug_transform.txt',
                    "[" . HistoryDownloadFitbit::$defaultName . "] - " . ' ' . $e->getMessage());
            }
        } else {
            $this->log("Token Expired, will retry later");
        }

        return [null, null];
    }

    /**
     * @param                   $requestedEndpoint
     * @param DateTime          $referenceTodayDate
     * @param DateTime          $apiAccessLog
     *
     * @return string|null
     */
    private function getFitbitApiUrl($requestedEndpoint, DateTime $referenceTodayDate, DateTime $apiAccessLog)
    {
        $path = $this->getFitbitApiPathFromEndpoint($requestedEndpoint);

        if (is_null($path)) {
            return null;
        }

        if (strpos($path, "{date}") !== false || strpos($path, "{period}") !== false) {
            $daysSince = round(($referenceTodayDate->format("U") - $apiAccessLog->format("U")) / (60 * 60 * 24), 0,
                PHP_ROUND_HALF_UP);
            $syncDate = clone $apiAccessLog;
            if ($daysSince > 0) {
                $syncDate->modify("+ " . $daysSince . " days");
                if ($syncDate->format("Y-m-d") > $syncDate->format("Y-m-d")) {
                    $syncDate = $syncDate->setTimestamp(strtotime('now'));
                }
            }

            if (strpos($path, "{period}") !== false) {
                $syncPeriod = $this->calculateLastSyncPeriod($apiAccessLog->format("Y-m-d"));
                $path = str_replace("{period}", $syncPeriod, $path);
            }

            if (strpos($path, "{date}") !== false) {
                $path = str_replace("{date}", $syncDate->format("Y-m-d"), $path);
            }
        }

        if (strpos($path, "{ext}") !== false) {
            $path = str_replace("{ext}", '.json', $path);
        }

        return $path;
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function getHasHistoryMembership()
    {
        if (is_null($this->hasHistoryMembership)) {
            if (
                !is_null($this->patient->getMembership()) &&
                (
                    $this->patient->getMembership()->getLifetime() ||
                    $this->patient->getMembership()->getLastPaid()->format("U") >= (new DateTime())->modify('- 31 days')->format("U")
                )
            ) {
                $this->hasHistoryMembership = true;
            } else {
                $this->hasHistoryMembership = false;
            }
        }

        return $this->hasHistoryMembership;
    }

    /**
     * @param $patientServiceProfile
     */
    private function log($patientServiceProfile)
    {
        if (!is_string($patientServiceProfile)) {
            AppConstants::writeToLog(
                'debug_transform.txt',
                "[" . HistoryDownloadFitbit::$defaultName . "] - " . print_r($patientServiceProfile, true)
            );
            echo "[" . HistoryDownloadFitbit::$defaultName . "] - " . print_r($patientServiceProfile, true) . "\n";
        } else {
            AppConstants::writeToLog(
                'debug_transform.txt',
                "[" . HistoryDownloadFitbit::$defaultName . "] - " . $patientServiceProfile
            );
            echo "[" . HistoryDownloadFitbit::$defaultName . "] - " . $patientServiceProfile . "\n";
        }
    }

    /**
     *
     */
    private function restUserLoop()
    {
        $this->hasHistoryMembership = null;
        $this->patientSettings = null;
        $this->patient = null;
    }

    /**
     * @param string $settingKey
     *
     * @param bool   $returnDateTime
     *
     * @return mixed|null
     * @throws Exception
     */
    private function userSettingRetreave(string $settingKey, bool $returnDateTime = false)
    {
        if (!is_array($this->patientSettings)) {
            /** @var PatientSettings[] $dbPatientSettings */
            $dbPatientSettings = $this->doctrine
                ->getRepository(PatientSettings::class)
                ->findBy(['patient' => $this->patient, 'service' => $this->service]);

            if (!$dbPatientSettings) {
                AppConstants::writeToLog('debug_transform.txt',
                    "[" . HistoryDownloadFitbit::$defaultName . "] - No Settings");
                return null;
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
                        $returnArray[] = new DateTime($setting);
                    }
                    return $returnArray;
                } else {
                    return new DateTime($this->patientSettings[$settingKey]);
                }
            } else {
                return $this->patientSettings[$settingKey];
            }
        } else {
            return null;
        }
    }

    /**
     * @param string $string
     * @param string $serviceBirth
     */
    private function userSettingSave(string $string, string $serviceBirth)
    {
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

    /**
     * {@inheritdoc}
     * @throws Exception
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

                /** @var DateTime $serviceBirth */
                try {
                    $serviceBirth = $this->userSettingRetreave("from", true);
                } catch (Exception $e) {
                    $this->restUserLoop();
                    break;
                }

                /** @var DateTime $serviceDeath */
                try {
                    $serviceDeath = $this->userSettingRetreave("until", true);
                } catch (Exception $e) {
                    $serviceDeath = null;
                }

                if (is_null($serviceBirth)) {
                    /** @noinspection PhpUnusedLocalVariableInspection */
                    [$patientServiceUrl, $patientServiceProfile] = $this->getFitbitApiResponce(
                        CommonFitbit::getAccessToken($patientCredential),
                        new DateTime(),
                        new DateTime(),
                        'serviceProfile'
                    );
                    if (!is_null($patientServiceProfile)) {
                        try {
                            $serviceBirth = new DateTime($patientServiceProfile->user->memberSince);
                        } catch (Exception $e) {
                            $this->restUserLoop();
                            break;
                        }

                        $this->userSettingSave("from", $serviceBirth->format("Y-m-d H:i:s"));
                    }
                }

                if (!is_null($serviceBirth)) {
                    try {
                        $supportedEndpoints = $this->userSettingRetreave("enabledEndpoints");
                    } catch (Exception $e) {
                        break;
                    }

                    [$servicePullFrom, $premiumString] = $this->calcServicePullFromDate($serviceBirth);
                    $this->log("Downloading history for " . $premiumString . " " . $this->patient->getUsername());
                    foreach ($supportedEndpoints as $supportedEndpoint) {
                        $supportedEndpoint = str_ireplace("Daily", "Period", $supportedEndpoint);

                        /** @var DateTime $serviceOldestPull */
                        try {
                            $serviceOldestPull = $this->userSettingRetreave("oldestPull" . $supportedEndpoint, true);
                            if (is_null($serviceOldestPull)) {
                                if (!is_null($serviceDeath)) {
                                    $serviceOldestPull = $serviceDeath;
                                } else {
                                    $serviceOldestPull = new DateTime();
                                }
                            }
                        } catch (Exception $e) {
                            if (!is_null($serviceDeath)) {
                                $serviceOldestPull = $serviceDeath;
                            } else {
                                $serviceOldestPull = new DateTime();
                            }
                        }

                        if ($supportedEndpoint !== "TrackingDevice") {
                            if ($serviceOldestPull->format("U") > $servicePullFrom->format("U")) {
                                $transformerClassName = 'App\\Transform\\Fitbit\\' . $supportedEndpoint;
                                if (!class_exists($transformerClassName)) {
                                    $this->log(" Couldn't find the Transformer class: " . $transformerClassName);
                                } else {
                                    $this->log(" Downloading " . $supportedEndpoint . " since " . $servicePullFrom->format("Y-m-d"));

                                    $loopWatchCount = 0;
                                    $loopWatchCountMax = 100;
                                    $loopWatchRef = strtotime('now');
                                    $loopWatchRefMax = $servicePullFrom->format('U');

                                    $foundGap = 0;

                                    while ($loopWatchCount < $loopWatchCountMax && $loopWatchRef > $loopWatchRefMax) {
                                        $loopWatchCount++;
                                        $this->log("  Pulling $supportedEndpoint since " . $serviceOldestPull->format("Y-m-d") . ", loop " . $loopWatchCount);

                                        [$apiEndPointCalled, $apiEndPointResult] = $this->getFitbitApiResponce(
                                            CommonFitbit::getAccessToken($patientCredential),
                                            $serviceOldestPull,
                                            $serviceBirth,
                                            $supportedEndpoint
                                        );

                                        if (is_null($apiEndPointCalled) || is_null($apiEndPointResult)) {
                                            $loopWatchCount = $loopWatchCountMax + 1; // Abort the loop by making $loopWatchCount too high
                                            $this->log("  An API error occurred");
                                            if (is_null($apiEndPointCalled)) {
                                                $this->log('   $apiEndPointCalled is NULL');
                                            }
                                            if (is_null($apiEndPointResult)) {
                                                $this->log('   $apiEndPointResult is NULL');
                                            }
                                        } else {
                                            $transformerClass = new $transformerClassName($this->doctrine,
                                                $this->logger, $this->awardManager, $this->challengePve,
                                                $this->commsManager);
                                            $transformerClass->setPatientEntity($patientCredential->getPatient());
                                            $transformerClass->setCalledUrl($apiEndPointCalled);
                                            $transformerClass->setApiReturn($apiEndPointResult);
                                            $lastDateTime = $transformerClass->processData();

                                            if (is_null($lastDateTime)) {
                                                $loopWatchCount = $loopWatchCountMax + 1;
                                                $this->log("  An DB error occurred");
                                            } else {
                                                if ($lastDateTime == "nill") {
                                                    $lastDateTime = $serviceOldestPull->format("Y-m-d");
                                                }

                                                if (
                                                    $lastDateTime == $serviceOldestPull->format("Y-m-d H:i:s") &&
                                                    $serviceOldestPull->format("U") > $serviceBirth->format("U")
                                                ) {
                                                    $loopWatchRef = strtotime($lastDateTime);
                                                    $serviceOldestPull->setTimestamp($loopWatchRef);

                                                    $foundGap++;
                                                    $serviceOldestPull->sub(new DateInterval('P' . ($foundGap * 5) . 'D'));
                                                    if ($serviceOldestPull->format("U") > $serviceBirth->format("U")) {
                                                        $this->log('That pull didnt get enough records, which probably means there\'s a gap');
                                                        $this->log('Last retreaved was ' . date("Y-m-d H:i:s",
                                                                $loopWatchRef));
                                                        $this->log('Next time we\'ll try skilling ' . ($foundGap * 5) . ' days - ' . $serviceOldestPull->format("Y-m-d H:i:s"));
                                                    } else {
                                                        $this->log('Looks like we should set the last call to birth time');
                                                        $serviceOldestPull->setTimestamp(strtotime($serviceBirth->format("Y-m-d")));
                                                        $loopWatchCount = $loopWatchCountMax + 1;
                                                    }
                                                } else {
                                                    $loopWatchRef = strtotime($lastDateTime);
                                                    $serviceOldestPull->setTimestamp($loopWatchRef);
                                                    $foundGap = 0;
                                                }
                                            }

                                            $this->userSettingSave("oldestPull" . $supportedEndpoint,
                                                date("Y-m-d", $serviceOldestPull->format("U")));
                                        }
                                    }
                                }
                            } else {
                                $this->log(" History for " . $premiumString . " " . $this->patient->getUsername() . " is fully up to date ");
                            }
                        }
                    }
                }

                $this->restUserLoop();
            }
        }
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
}
