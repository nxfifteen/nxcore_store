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
use App\Entity\FitDistanceDailySummary;
use App\Entity\FitStepsDailySummary;
use App\Entity\Patient;
use App\Entity\RpgRewards;
use App\Entity\RpgRewardsAwarded;
use App\Entity\RpgXP;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
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
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        ManagerRegistry $doctrine,
        Swift_Mailer $mailer,
        Environment $twig)
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
        try {
            $patient = $this->giveReward($patient, $reward, new DateTime($dateTime));
        } catch (Exception $e) {
            AppConstants::writeToLog('debug_transform.txt', __FILE__ . '' . __LINE__ . ' = ' . $e->getMessage());
        }
        return $patient;
    }

    public function giveReward(Patient $patient, RpgRewards $reward, DateTimeInterface $dateTime = NULL)
    {
        $entityManager = $this->doctrine->getManager();
        if (!is_null($dateTime)) {
            try {
                $dateTime = new DateTime(date("Y-m-d 00:00:00"));
            } catch (Exception $e) {
                AppConstants::writeToLog('debug_transform.txt', __FILE__ . '' . __LINE__ . ' = ' . $e->getMessage());
            }
        }
        /** @var RpgRewardsAwarded $rewards */
        $rewards = $this->doctrine->getRepository(RpgRewardsAwarded::class)->findOneBy(['patient' => $patient, 'reward' => $reward, 'datetime' => $dateTime]);
        if (!$rewards) {
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Awarding ' . $patient->getFirstName() . ' the ' . $reward->getName() . ' badge');

            $rewarded = new RpgRewardsAwarded();
            $rewarded->setPatient($patient);
            $rewarded->setDatetime($dateTime);
            $rewarded->setReward($reward);

            $patient->addReward($rewarded);

            if ($reward->getXp() > 0) {
                $patient = $this->giveXp($patient, $reward->getXp(), "Awarded the " . $reward->getName() . " badge", $dateTime);
            }

//            try {
//                $this->sendUserEmail(
//                    [
//                        $patient->getEmail() => $patient->getFirstName() . ' ' . $patient->getSurName(),
//                    ],
//                    'award_badge',
//                    $options
//                );
//            } catch (LoaderError $e) {
//                return $patient;
//            } catch (RuntimeError $e) {
//                return $patient;
//            } catch (SyntaxError $e) {
//                return $patient;
//            }

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

                // $currentXp = $patient->getXpTotal();
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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

    /**
     * @param FitStepsDailySummary|FitDistanceDailySummary $dataEntry
     *
     * @throws \Exception
     */
    public function checkForGoalAwards($dataEntry)
    {
        if ($dataEntry->getValue() >= $dataEntry->getGoal()->getGoal()) {
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $dataEntry->getPatient()->getFirstName() . ' beat ' . $dataEntry->getPatient()->getPronounTheir() . ' goal, so an award is due');
            $goalCriteria = str_ireplace("App\\Entity\\", "", get_class($dataEntry));
            $goalCriteriaShort = $this->getCriteriaShortName($goalCriteria);
            $reward = $this->findAnAward($goalCriteria, $goalCriteriaShort, $dataEntry->getValue(), $dataEntry->getGoal()->getGoal());
            if ($reward) {
                $this->giveReward($dataEntry->getPatient(), $reward, new DateTime(date("Y-m-d 00:00:00")));
            }
        } else {
            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' ' . $dataEntry->getPatient()->getFirstName() . ' has not beaten ' . $dataEntry->getPatient()->getPronounTheir() . ' goal');
        }
    }

    private function getCriteriaShortName($goalCriteria)
    {
        switch ($goalCriteria) {
            case "FitStepsDailySummary":
                return "trg_steps";

            case "FitDistanceDailySummary":
                return "trg_distance";

            default:
                return "";
        }
    }

    private function findAnAward(string $goalCriteria, string $goalCriteriaShort, ?int $getValue, ?float $getGoal)
    {
//        AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Looking for ' . $goalCriteria . ' awards, taged as ' . $goalCriteriaShort);

        $reward = NULL;
        if ($getValue >= ($getGoal * 2)) {
//            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Look for a smashed award');
            $reward = $this->findAwardEntity($goalCriteriaShort . "_smashed");
        }

        if (!$reward) {
//            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Look for an achieved award');
            $reward = $this->findAwardEntity($goalCriteriaShort . "_achieved");
            if ($reward) {
//                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Found an achieved award');
            }
        } else {
//            AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Found a smashed award');
        }

//        if (!$reward) AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' No award found');
        return $reward;
    }

    private function findAwardEntity(string $string)
    {
        //AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Look for an ' . $string . ' award');
        $reward = $this->doctrine->getRepository(RpgRewards::class)->findOneBy(['image' => $string]);
        if (!$reward) {
            $rewardDefault = $this->findAwardInDefault($string);
            if (!is_null($rewardDefault)) {
//                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Found in defaults');
//                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' $name = ' . print_r($rewardDefault, TRUE));

                $reward = new RpgRewards();
                $reward->setName($rewardDefault['name']);
                $reward->setImage($rewardDefault['image']);
                $reward->setText($rewardDefault['text']);
                $reward->setTextLong($rewardDefault['longtext']);
                $reward->setXp($rewardDefault['xp']);

                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($reward);
                $entityManager->flush();
            }
        }

        if (!$reward) {
            return NULL;
        } else {
            return $reward;
        }

    }

    private function findAwardInDefault(string $string)
    {
        $standardBadges = [
            'trg_steps_achieved' => [
                'name' => 'Step Target',
                'image' => 'trg_steps_achieved',
                'text' => "Reached your step goal today",
                'longtext' => "Today you did it! You reached your step goal",
                'citation' => "Today you did it! You reached your step goal",
                'xp' => 5,
            ],
            'trg_steps_smashed' => [
                'name' => 'Step Target Smashed',
                'image' => 'trg_steps_smashed',
                'text' => "You walked twice your step goal",
                'longtext' => "Wow! I mean, WOW! You walked twice your step goal today",
                'citation' => "Wow! I mean, WOW! You walked twice your step goal today",
                'xp' => 10,
            ],
            'trg_distance_achieved' => [
                'name' => 'Distance Target Achieved',
                'image' => 'trg_distance_achieved',
                'text' => "Reached your distance goal today",
                'longtext' => "Today you did it! Walked the full way",
                'citation' => "Today you did it! Walked the full way",
                'xp' => 5,
            ],
            'trg_distance_smashed' => [
                'name' => 'Distance Target Smashed',
                'image' => 'trg_distance_smashed',
                'text' => "You walked twice your distance goal",
                'longtext' => "Wow! I mean, WOW! You walked twice your distance goal today",
                'citation' => "Wow! I mean, WOW! You walked twice your distance goal today",
                'xp' => 10,
            ],
        ];

        if (array_key_exists($string, $standardBadges)) {
            return $standardBadges[$string];
        } else {
            return NULL;
        }
    }

}