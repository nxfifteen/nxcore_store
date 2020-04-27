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
use App\Entity\PatientCredentials;
use App\Entity\PatientSettings;
use App\Entity\SyncQueue;
use App\Entity\ThirdPartyService;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use MyBuilder\Bundle\CronosBundle\Annotation\Cron;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for sending our email messages from the database.
 *
 * @Cron(minute="/5", noLogs=true, server="web")
 */
class QueuePopulateFitbit extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'queue:populate:fitbit';

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

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

        /** @var PatientCredentials[] $patientCredentials */
        $patientCredentials = $this->doctrine
            ->getRepository(PatientCredentials::class)
            ->findBy(["service" => $service]);

        if (count($patientCredentials) > 0) {
            $this->log(count($patientCredentials) . ' users are connected with Fitbit');
            foreach ($patientCredentials as $patientCredential) {
                /** @var SyncQueue[] $patientCredentials */
                $serviceSyncQueues = $this->doctrine
                    ->getRepository(SyncQueue::class)
                    ->findBy(['service' => $service, 'credentials' => $patientCredential]);
                if ($serviceSyncQueues) {
                    $this->log($patientCredential->getPatient()->getUsername() . ' already has something in the queue');
                } else {
                    /** @var PatientSettings $patientSettings */
                    $patientSettings = $this->doctrine
                        ->getRepository(PatientSettings::class)
                        ->findOneBy([
                            'patient' => $patientCredential->getPatient(),
                            'service' => $service,
                            'name' => 'untilOR',
                        ]);

                    if (!$patientSettings) {
                        /** @var PatientSettings $patientSettings */
                        $patientSettings = $this->doctrine
                            ->getRepository(PatientSettings::class)
                            ->findOneBy([
                                'patient' => $patientCredential->getPatient(),
                                'service' => $service,
                                'name' => 'enabledEndpoints',
                            ]);
                    }

                    if ($patientSettings) {
                        foreach ($patientSettings->getValue() as $patientSetting) {
                            $this->log('... ' . $patientSetting);

                            /** @var ApiAccessLog $patient */
                            $apiAccessLog = $this->doctrine
                                ->getRepository(ApiAccessLog::class)
                                ->findLastAccess($patientCredential->getPatient(), $patientCredential->getService(),
                                    $patientSetting);

                            if (!is_null($apiAccessLog)) {
                                if ($apiAccessLog->getCooldown()->format("U") < strtotime("+1 day")) {
                                    $this->log('Refreshing ' . $patientSetting . ' for ' . $patientCredential->getPatient()->getUsername());

                                    $entityManager = $this->doctrine->getManager();
                                    $serviceSyncQueue = new SyncQueue();
                                    $serviceSyncQueue->setService($service);
                                    $serviceSyncQueue->setDatetime(new DateTime());
                                    $serviceSyncQueue->setCredentials($patientCredential);
                                    $serviceSyncQueue->setEndpoint($patientSetting);
                                    $entityManager->persist($serviceSyncQueue);

                                    $subscriptionSyncQueue = new SyncQueue();
                                    $subscriptionSyncQueue->setService($service);
                                    $subscriptionSyncQueue->setDatetime(new DateTime());
                                    $subscriptionSyncQueue->setCredentials($patientCredential);
                                    $subscriptionSyncQueue->setEndpoint("subscriptions");
                                    $entityManager->persist($subscriptionSyncQueue);

                                    $entityManager->flush();
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $this->log('There are not Fitbit users');
        }
    }

    /**
     * @required
     *
     * @param ManagerRegistry $doctrine
     */
    public function dependencyInjection(
        ManagerRegistry $doctrine
    ): void {
        $this->doctrine = $doctrine;
    }

}
