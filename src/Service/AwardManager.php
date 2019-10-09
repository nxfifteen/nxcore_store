<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2019. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;


use App\AppConstants;
use App\Entity\Patient;
use App\Entity\RpgRewards;
use App\Entity\RpgRewardsAwarded;
use App\Entity\RpgXP;
use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Swift_Message;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AwardManager
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(
        ManagerRegistry $doctrine,
        \Swift_Mailer $mailer,
        \Twig\Environment $twig)
    {
        $this->doctrine = $doctrine;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function test()
    {
        AppConstants::writeToLog('debug_transform.txt', __LINE__);
    }

    public function giveBadge(Patient $patient, array $options)
    {
        $entityManager = $this->doctrine->getManager();

        $dateTime = $options["dateTime"];
        $name = $options["badge_name"];
        $xp = $options["badge_xp"];
        $image = $options["badge_image"];
        $text = $options["badge_text"];
        $longtext = $options["badge_longtext"];

        /** @var RpgRewards $reward */
        $reward = $this->doctrine->getRepository(RpgRewards::class)->findOneBy(['name' => $name, 'text' => $text]);
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
        $rewards = $this->doctrine->getRepository(RpgRewardsAwarded::class)->findOneBy(['patient' => $patient, 'reward' => $reward, 'datetime' => new \DateTime($dateTime->format("Y-m-d 00:00:00"))]);
        if (!$rewards) {
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Awarding ' . $patient->getFirstName() . ' the ' . $name . ' badge');

            $rewarded = new RpgRewardsAwarded();
            $rewarded->setPatient($patient);
            $rewarded->setDatetime(new \DateTime($dateTime->format("Y-m-d 00:00:00")));
            $rewarded->setReward($reward);

            $patient->addReward($rewarded);

            if ($xp > 0) {
                $patient = $this->giveXp($patient, $xp, "Awarded the " . $name . " badge", new \DateTime($dateTime->format("Y-m-d 00:00:00")));
            }

            try {
                $this->sendUserEmail(
                    [
                        $patient->getEmail() => $patient->getFirstName() . ' ' . $patient->getSurName(),
                    ],
                    'award_badge',
                    $options
                );
            } catch (LoaderError $e) {
                return $patient;
            } catch (RuntimeError $e) {
                return $patient;
            } catch (SyntaxError $e) {
                return $patient;
            }

            $entityManager->persist($rewarded);
            $entityManager->persist($patient);
            $entityManager->flush();
        }

        return $patient;
    }

    public function giveXp(Patient $patient, float $xpAwarded, string $reasoning, DateTimeInterface $dateTime)
    {
        if ($xpAwarded > 0) {
            /** @var RpgXP $xpAlreadyAwarded */
            $xpAlreadyAwarded = $this->doctrine->getRepository(RpgXP::class)->findOneBy(['patient' => $patient, 'reason' => $reasoning, 'datetime' => $dateTime]);
            if (!$xpAlreadyAwarded) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Awarding ' . $patient->getFirstName() . ' ' . $xpAwarded . 'XP for ' . $reasoning);

                $currentXp = $patient->getXpTotal();
                $xpToAward = 0;
                for ($i = 1; $i <= $xpAwarded; $i++) {
                    $patient = $this->updateDifficultyFactor($patient, $patient->getRpgLevel());
                    $xpToAward = $xpToAward + round((1 * $patient->getRpgFactor()), 0, PHP_ROUND_HALF_DOWN);
                }

                $entityManager = $this->doctrine->getManager();

                $xpAward = new RpgXP();
                $xpAward->setDatetime($dateTime);
                $xpAward->setReason($reasoning);
                $xpAward->setValue($xpToAward);
                $xpAward->setPatient($patient);

                $entityManager->persist($xpAward);
                $entityManager->flush();

                $patient->addXp($xpAward);
            }
        }

        return $patient;
    }

    private function updateDifficultyFactor(Patient $patient, int $i)
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

    /**
     * @param array  $setTo
     * @param string $setTemplateName
     * @param array  $setTemplateVariables
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendUserEmail(array $setTo, string $setTemplateName, array $setTemplateVariables)
    {
        $setTemplateVariables = array_merge($setTemplateVariables,
            [
                'store_domain' => $_ENV['INSTALL_URL'],
                'ui_domain' => $_ENV['UI_URL'],
                'asset_domain' => $_ENV['ASSET_URL'],
            ]
        );

        // Create the message
        $message = (new Swift_Message())
            // Add subject
            ->setSubject($setTemplateVariables['html_title'])
            //Put the From address
            ->setFrom([$_ENV['SITE_EMAIL_NOREPLY'] => $_ENV['SITE_EMAIL_NAME']])
            ->setBody(
                $this->twig->render(
                    'emails/' . $setTemplateName . '.html.twig',
                    $setTemplateVariables
                ),
                'text/html'
            )
            // you can remove the following code if you don't define a text version for your emails
            ->addPart(
                $this->twig->render(
                    'emails/' . $setTemplateName . '.txt.twig',
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

        $this->mailer->send($message);
    }

}