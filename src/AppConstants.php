<?php
namespace App;


use App\Entity\Patient;
use App\Entity\RpgRewards;
use App\Entity\RpgRewardsAwarded;
use App\Entity\RpgXP;
use App\Entity\ThirdPartyService;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class AppConstants
{
    static function writeToLog(String $fileName, String $body) {
        try {
            $path = sys_get_temp_dir() . '/sync_upload_post';
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        if (!empty($path)) {
            $file = $path . '/' . $fileName;

            $fileSystem = new Filesystem();
            try {
                $fileSystem->mkdir($path);
                if ($fileSystem->exists($file)) {
                    $fileSystem->appendToFile($file, date("Y-m-d H:i:s") . ":: " . $body . "\n");
                } else {
                    $fileSystem->dumpFile($file, date("Y-m-d H:i:s") . ":: " . $body . "\n");
                }

            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at " . $exception->getPath();
            }
        }
    }

    static function awardPatientReward(ManagerRegistry $doctrine, Patient $patient, DateTimeInterface $dateTime, string $name, float $xp, string $image, string $text, string $longtext)
    {
        $entityManager = $doctrine->getManager();

        /** @var RpgRewards $reward */
        $reward = $doctrine->getRepository(RpgRewards::class)->findOneBy(['name' => $name, 'text' => $text]);
        if (!$reward) {
            $reward = new RpgRewards();
            $reward->setName($name);
            $reward->setImage($image);
            $reward->setText($text);
            $reward->setTextLong($longtext);
            $reward->setXp($xp);
            $entityManager->persist($reward);
            $entityManager->flush();
        }

        /** @var RpgRewardsAwarded $rewards */
        $rewards = $doctrine->getRepository(RpgRewardsAwarded::class)->findOneBy(['patient' => $patient, 'reward' => $reward, 'datetime' => new DateTime($dateTime->format("Y-m-d 00:00:00"))]);
        if (!$rewards) {
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Awarding ' . $patient->getFirstName() . ' the ' . $name . ' badge');

            $rewarded = new RpgRewardsAwarded();
            $rewarded->setPatient($patient);
            $rewarded->setDatetime(new DateTime($dateTime->format("Y-m-d 00:00:00")));
            $rewarded->setReward($reward);
            $entityManager->persist($rewarded);

            $patient->addReward($rewarded);

            if ($xp > 0) {
                $patient = AppConstants::awardPatientXP($doctrine, $patient, $xp, "Awarded the " . $name . " badge on " . $dateTime->format("l jS F, Y"), new DateTime($dateTime->format("Y-m-d 00:00:00")));
            }
        }

        return $patient;
    }

    static function awardPatientXP(ManagerRegistry $doctrine, Patient $patient, float $xpAwarded, string $reasoning, DateTimeInterface $dateTime)
    {
        if ($xpAwarded > 0) {
            /** @var RpgXP $xpAlreadyAwarded */
            $xpAlreadyAwarded = $doctrine->getRepository(RpgXP::class)->findOneBy(['patient' => $patient, 'reason' => $reasoning, 'datetime' => $dateTime]);
            if (!$xpAlreadyAwarded) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Awarding ' . $patient->getFirstName() . ' ' . $xpAwarded . 'XP for ' . $reasoning);

                $currentXp = $patient->getXpTotal();
                $xpToAward = 0;
                for ($i = 1; $i <= ($xpAwarded * 1000); $i++) {
//                    $patient = self::awardPatientXPUpdateFactor($patient, ($currentXp + $xpToAward));
                    $patient = AppConstants::awardPatientXPUpdateFactor($patient, $patient->getRpgLevel());
                    $xpToAward = $xpToAward + ((1 / 1000) * $patient->getRpgFactor());
                }

                $entityManager = $doctrine->getManager();

                $xpAward = new RpgXP();
                $xpAward->setDatetime($dateTime);
                $xpAward->setReason($reasoning);
                $xpAward->setValue($xpToAward);
                $xpAward->setPatient($patient);
                $patient->addXp($xpAward);

                $entityManager->persist($xpAward);
                $entityManager->persist($patient);
                $entityManager->flush();
            }
        }

        return $patient;
    }

    static function awardPatientXPUpdateFactor(Patient $patient, int $i)
    {
        $x = $patient->getRpgFactor();
        if ($i == 10) {
            $patient->setRpgFactor($x - 0.01);
        } else if ($i == 20) {
            $patient->setRpgFactor($x - 0.02);
        } else if ($i == 30) {
            $patient->setRpgFactor($x - 0.03);
        } else if ($i == 40) {
            $patient->setRpgFactor($x - 0.05);
        } else if ($i == 50) {
            $patient->setRpgFactor($x - 0.08);
        } else if ($i == 60) {
            $patient->setRpgFactor($x - 0.10);
        } else if ($i == 70) {
            $patient->setRpgFactor($x - 0.15);
        } else if ($i == 80) {
            $patient->setRpgFactor($x - 0.20);
        } else if ($i == 90) {
            $patient->setRpgFactor($x - 0.30);
        }/* else if ($i == 100) {
            $patient->setRpgFactor($x - 0.60);
        }*/
        return $patient;
    }

    static function startsWith($haystack, $needle)
    {
        $len = strlen($needle);
        return (substr($haystack, 0, $len) === $needle);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String          $serviceName
     *
     * @return ThirdPartyService|null
     */
    static function getThirdPartyService(ManagerRegistry $doctrine, String $serviceName)
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