<?php

namespace App\Command;

use App\AppConstants;
use App\Entity\PatientCredentials;
use App\Entity\ThirdPartyService;
use djchen\OAuth2\Client\Provider\Fitbit;
use Doctrine\Common\Persistence\ManagerRegistry;
use League\OAuth2\Client\Token\AccessToken;
use MyBuilder\Bundle\CronosBundle\Annotation\Cron;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for sending our email messages from the database.
 *
 * @Cron(minute="/5", noLogs=true, server="web")
 */
class UpdateAuthCredentialsFitbit extends Command {
    /**
     * @var string
     */
    protected static $defaultName = 'auth:refresh:fitbit';

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
    ): void {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->refreshFitbitTokens();
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

    private function refreshFitbitTokens()
    {
        $queryCallback = $_ENV['INSTALL_URL'] . '/auth/refresh/fitbit';

        /** @var ThirdPartyService $service */
        $service = $this->getThirdPartyService($this->doctrine, "Fitbit");

        /** @var PatientCredentials[] $patientCredentials */
        $patientCredentials = $this->doctrine
            ->getRepository(PatientCredentials::class)
            ->findExpired($service->getId());

        if (count($patientCredentials) > 0) {
            $provider = new Fitbit([
                'clientId' => $_ENV['FITBIT_ID'],
                'clientSecret' => $_ENV['FITBIT_SECRET'],
                'redirectUri' => $queryCallback,
            ]);

            $entityManager = $this->doctrine->getManager();

            foreach ($patientCredentials as $patientCredential) {
                try {
                    $existingAccessToken = new AccessToken([
                        'access_token' => $patientCredential->getToken(),
                        'refresh_token' => $patientCredential->getRefreshToken(),
                        'expires' => $patientCredential->getExpires()->format("U")
                    ]);

                    if ($existingAccessToken->hasExpired()) {
                        $newAccessToken = $provider->getAccessToken('refresh_token', [
                            'refresh_token' => $existingAccessToken->getRefreshToken()
                        ]);

                        $patientCredential->setToken($newAccessToken->getToken());
                        $patientCredential->setRefreshToken($newAccessToken->getRefreshToken());
                        $date = new \DateTime();
                        $date->setTimestamp($newAccessToken->getExpires());
                        $patientCredential->setExpires($date);

                        $entityManager->persist($patientCredential);

                        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $patientCredential->getPatient()->getUuid() . '\'s Fitbit authentication token has been refreshed');
                    }

                } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                    // Failed to get the access token or user details.
                    AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $e->getMessage());
                }
            }

            $entityManager->flush();
        } else {
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . 'No Fitbit authentication token need refreshed');
        }
    }
}
