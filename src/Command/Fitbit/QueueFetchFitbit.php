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
use App\Entity\SyncQueue;
use App\Entity\ThirdPartyService;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\CommsManager;
use App\Transform\Fitbit\CommonFitbit;
use Doctrine\Common\Persistence\ManagerRegistry;
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
                $accessToken = CommonFitbit::getAccessToken($serviceSyncQueue->getCredentials());
                if (!$accessToken->hasExpired()) {
                    $this->patient = $serviceSyncQueue->getCredentials()->getPatient();
                    $this->log("Downloading " . $serviceSyncQueue->getEndpoint() . " for " . $this->patient->getUsername());

                } else {
                    $this->log('Credentials have expired for ' . $serviceSyncQueue->getCredentials()->getPatient()->getFirstName() . '. Will retry later');
                }
            }
        } else {
            $this->log('No Fitbit jobs in the sync queue');
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
