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
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig_Environment;

class AppConstants
{
    static function awardPatientReward(ManagerRegistry $doctrine, Patient $patient, DateTimeInterface $dateTime, string $name, float $xp, string $image, string $text, string $longtext)
    {
        return $patient;
    }

    static function writeToLog(String $fileName, String $body)
    {
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

    static function awardPatientXP(ManagerRegistry $doctrine, Patient $patient, float $xpAwarded, string $reasoning, DateTimeInterface $dateTime)
    {
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

    static function sendUserEmail(\Twig\Environment $twig, Swift_Mailer $mailer, array $setTo, string $setTemplateName, array $setTemplateVariables)
    {
        // Create the message
        $message = (new Swift_Message())
            // Add subject
            ->setSubject($setTemplateVariables['html_title'])
            //Put the From address
            ->setFrom([$_ENV['SITE_EMAIL_NOREPLY'] => $_ENV['SITE_EMAIL_NAME']])
            ->setBody(
                $twig->render(
                    'emails/'.$setTemplateName.'.html.twig',
                    $setTemplateVariables
                ),
                'text/html'
            )
            // you can remove the following code if you don't define a text version for your emails
            ->addPart(
                $twig->render(
                    'emails/'.$setTemplateName.'.txt.twig',
                    $setTemplateVariables
                ),
                'text/plain'
            );

        if (count($setTo) > 1) {
            // Include several To addresses
            $message->setTo([$_ENV['SITE_EMAIL_NOREPLY'] => $_ENV['SITE_EMAIL_NAME']]);
            // Include several To addresses
            $message->setBcc($setTo);
        } else {
            // Include several To addresses
            $message->setTo($setTo);
        }

        $mailer->send($message);
    }

    static function convertCriteriaEnglish($criteria)
    {
        switch ($criteria) {
            case "FitStepsDailySummary":
                return "steps";
                break;
            default:
                return $criteria;
                break;
        }
    }

    static function compressString($stringToCompress)
    {
        $compressedString = "\x1f\x8b\x08\x00" . gzcompress($stringToCompress);

        return $compressedString;
    }

    static function uncompressString($stringToUncompress)
    {
        $uncompressedString = gzuncompress(substr($stringToUncompress, 4));

        return $uncompressedString;
    }

    static function formatSeconds($seconds)
    {
        $hours = 0;
        $milliseconds = str_replace("0.", '', $seconds - floor($seconds));

        if ($seconds > 3600) {
            $hours = floor($seconds / 3600);
        }
        $seconds = $seconds % 3600;


        return str_pad($hours, 2, '0', STR_PAD_LEFT)
            . gmdate(':i:s', $seconds)
            . ($milliseconds ? ".$milliseconds" : '');
    }
}