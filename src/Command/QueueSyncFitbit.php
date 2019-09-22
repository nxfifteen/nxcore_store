<?php
/**
 * Created by IntelliJ IDEA.
 * User: stuar
 * Date: 18/09/2019
 * Time: 22:43
 */

namespace App\Command;

use App\AppConstants;
use App\Entity\PatientCredentials;
use App\Entity\SyncQueue;
use App\Entity\ThirdPartyService;
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
class QueueSyncFitbit extends Command
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
     * @required
     *
     * @param ManagerRegistry $doctrine
     */
    public function dependencyInjection(
        ManagerRegistry $doctrine
    ): void
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var ThirdPartyService $service */
        $service = $this->getThirdPartyService($this->doctrine, "Fitbit");

        /** @var PatientCredentials[] $patientCredentials */
        $patientCredentials = $this->doctrine
            ->getRepository(PatientCredentials::class)
            ->findBy(["service" => $service]);

        if (count($patientCredentials) > 0) {
            AppConstants::writeToLog('debug_transform.txt', "[" . QueueSyncFitbit::$defaultName . "] - " . ' ' . count($patientCredentials) . ' users are connected with Fitbit');
            foreach ($patientCredentials as $patientCredential) {
                /** @var SyncQueue[] $patientCredentials */
                $serviceSyncQueues = $this->doctrine
                    ->getRepository(SyncQueue::class)
                    ->findBy(['service' => $service, 'credentials' => $patientCredential, 'endpoint' => 'TrackingDevice::FitStepsDailySummary']);
                if ($serviceSyncQueues) {
                    AppConstants::writeToLog('debug_transform.txt', "[" . QueueSyncFitbit::$defaultName . "] - " .  ' ' . $patientCredential->getPatient()->getUsername() . ' already has steps in the queue');
                } else {
                    AppConstants::writeToLog('debug_transform.txt', "[" . QueueSyncFitbit::$defaultName . "] - " . ' ' . $patientCredential->getPatient()->getUsername() . '\' steps queuing');

                    $serviceSyncQueue = new SyncQueue();
                    $serviceSyncQueue->setService($service);
                    $serviceSyncQueue->setDatetime(new \DateTime());
                    $serviceSyncQueue->setCredentials($patientCredential);
                    $serviceSyncQueue->setEndpoint('TrackingDevice::FitStepsDailySummary');

                    $entityManager = $this->doctrine->getManager();
                    $entityManager->persist($serviceSyncQueue);
                    $entityManager->flush();
                }
            }
        } else {
            AppConstants::writeToLog('debug_transform.txt', "[" . QueueSyncFitbit::$defaultName . "] - " . ' ' . 'There are not Fitbit users');
        }
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return ThirdPartyService|null
     */
    private static function getThirdPartyService(ManagerRegistry $doctrine, String $serviceName)
    {
        /** @var ThirdPartyService $thirdPartyService */
        $thirdPartyService = $doctrine->getRepository(ThirdPartyService::class)->findOneBy(['name' => $serviceName]);
        if ($thirdPartyService) {
            return $thirdPartyService;
        } else {
            $entityManager = $doctrine->getManager();
            $thirdPartyService = new ThirdPartyService();
            $thirdPartyService->setName($serviceName);
            $entityManager->persist($thirdPartyService);
            $entityManager->flush();

            return $thirdPartyService;
        }
    }

}