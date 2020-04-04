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
use App\Entity\RpgIndicator;
use App\Entity\RpgRewards;
use App\Entity\RpgRewardsAwarded;
use App\Entity\RpgXP;
use App\Entity\SiteNews;
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

    /**
     * @var TweetManager
     */
    private $tweetManager;

    public function __construct(
        ManagerRegistry $doctrine,
        Swift_Mailer $mailer,
        Environment $twig,
        TweetManager $tweetManager)
    {
        $this->doctrine = $doctrine;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->tweetManager = $tweetManager;
    }

    public function test()
    {
        AppConstants::writeToLog('debug_transform.txt', __LINE__);
    }

    public function giveBadge(Patient $patient, array $options)
    {
        return $patient;

        // @TODO: RPG
//        $entityManager = $this->doctrine->getManager();
//        $dateTime = $options["dateTime"];
//        $name = $options["badge_name"];
//        $xp = $options["badge_xp"];
//        $image = $options["badge_image"];
//        $text = $options["badge_text"];
//        $longtext = $options["badge_longtext"];
//        /** @var RpgRewards $reward */
//        $reward = $this->doctrine->getRepository(RpgRewards::class)->findOneBy(['name' => $name, 'text' => $text]);
//        if (!$reward) {
//            $reward = new RpgRewards();
//            $reward->setName($name);
//            $reward->setImage($image);
//            $reward->setText($text);
//            $reward->setTextLong($longtext);
//            $reward->setXp($xp);
//            $entityManager->persist($reward);
//            $entityManager->flush();
//        }
//        if (is_object($dateTime)) {
//            $patient = $this->giveReward($patient, $reward, $dateTime);
//        } else {
//            try {
//                $patient = $this->giveReward($patient, $reward, new DateTime($dateTime));
//            } catch (Exception $e) {
//                AppConstants::writeToLog('debug_transform.txt', __FILE__ . '' . __LINE__ . ' = ' . $e->getMessage());
//            }
//        }
//        return $patient;
    }

    public function giveReward(Patient $patient, RpgRewards $reward, DateTimeInterface $dateTime = NULL, string $citation = NULL, bool $tweeted = FALSE)
    {
        $entityManager = $this->doctrine->getManager();
        if (is_null($dateTime)) {
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

            $notification = new SiteNews();
            $notification->setPatient($patient);
            $notification->setPublished(new \DateTime());
            $notification->setTitle("You just won the " . $reward->getName() . " badge");
            if (is_null($citation) || $citation == "") {
                $notification->setText($reward->getText());
            } else {
                $notification->setText($citation);
            }
            $notification->setAccent('success');
            $notification->setImage($reward->getImage());
            $notification->setExpires(new \DateTime(date("Y-m-d 23:59:59")));
            $notification->setLink('/achievements/awards/info/' . $reward->getId());
            $notification->setPriority(3);

            if (!$tweeted) {
                $this->tweetManager->sendNotification(
                    "@" . $patient->getUuid() . " just #won the " . $reward->getName() . " #badge! :heavy_check_mark:",
                    "Congratulations!!",
                    $patient,
                    FALSE,
                    "https://core.nxfifteen.me.uk/assets/badges/" . $reward->getImage() . ".png"
                );

                $this->tweetManager->sendNotification(
                    "You just #won the " . $reward->getName() . " #badge! :heavy_check_mark:",
                    $notification->getTitle() . " - " . $notification->getText(),
                    $patient,
                    TRUE,
                    "https://core.nxfifteen.me.uk/assets/badges/" . $reward->getImage() . ".png"
                );
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

            $entityManager->persist($notification);
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
        //$x = $patient->getRpgFactor();
        // @var $x Base RPG factor
        $x = 1;
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
     * @param string                                       $citation
     *
     * @throws Exception
     */
    public function checkForGoalAwards($dataEntry, string $citation = NULL)
    {
        AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Origin class = ' . get_class($dataEntry));

        if ($dataEntry->getValue() >= $dataEntry->getGoal()->getGoal()) {
            AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Value is greater than Goal');
            $indicatorDataSet = str_ireplace("App\\Entity\\", "", get_class($dataEntry));
            $indicatorType = "goal";
            if ($dataEntry->getValue() >= ($dataEntry->getGoal()->getGoal() * 3)) {
                $indicatorComparator = ">300%";
            } else if ($dataEntry->getValue() >= ($dataEntry->getGoal()->getGoal() * 2.5)) {
                $indicatorComparator = ">250%";
            } else if ($dataEntry->getValue() >= ($dataEntry->getGoal()->getGoal() * 2)) {
                $indicatorComparator = ">200%";
            } else if ($dataEntry->getValue() >= ($dataEntry->getGoal()->getGoal() * 1.5)) {
                $indicatorComparator = ">150%";
            } else {
                $indicatorComparator = ">100%";
            }
            $indicatorObject = $this->findAnIndicator($indicatorDataSet, $indicatorType, $indicatorComparator);
            if (is_null($indicatorObject)) {
                AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': No indicator was found');
            } else {
                AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Indicator found = ' . $indicatorObject->getName());
                if (count($indicatorObject->getRewards()) == 0) {
                    AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Indicator has no awards');
                    $awardDefaultArray = $this->findAwardInDefault($indicatorDataSet, $indicatorType, $indicatorComparator);
                    if (is_array($awardDefaultArray)) {
                        foreach ($awardDefaultArray as $item) {
                            $rewardObject = new RpgRewards();
                            $rewardObject->setName($item['name']);
                            $rewardObject->setText($item['text']);
                            $rewardObject->setTextLong($item['text_long']);
                            $rewardObject->setIndicator($indicatorObject);
                            $rewardObject->setType($item['type']);
                            $rewardObject->setPayload($item['payload']);

                            $entityManager = $this->doctrine->getManager();
                            $entityManager->persist($rewardObject);
                            $entityManager->flush();

                            $indicatorObject->addReward($rewardObject);
                        }
                    }
                }
                AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Indicator has ' . count($indicatorObject->getRewards()) . ' awards');
            }


//            $goalCriteria = str_ireplace("App\\Entity\\", "", get_class($dataEntry));
//            $goalCriteriaShort = $this->getCriteriaShortName($goalCriteria);
//            $reward = $this->findAnAward($goalCriteria, $goalCriteriaShort, $dataEntry->getValue(), $dataEntry->getGoal()->getGoal());
//            if ($reward) {
//                if (is_null($citation) || $citation == "") {
//                    if ($goalCriteriaShort == "trg_steps") {
//                        $citation = "You took " . $dataEntry->getValue() . " steps today, beating your goal of only " . $dataEntry->getGoal()->getGoal();
//                    } else if ($goalCriteriaShort == "trg_distance") {
//                        $citation = "You moved " .
//                            number_format(AppConstants::convertUnitOfMeasurement($dataEntry->getValue(), $dataEntry->getUnitOfMeasurement()->getName(), 'km'), 2) .
//                            "km today, beating your goal of only " .
//                            number_format(AppConstants::convertUnitOfMeasurement($dataEntry->getGoal()->getGoal(), $dataEntry->getGoal()->getUnitOfMeasurement()->getName(), 'km'), 2) . "km";
//                    }
//                }
//
//                $this->giveReward($dataEntry->getPatient(), $reward, new DateTime(date($dataEntry->getDateTime()->format("Y-m-d") . " 00:00:00")), $citation);
//            }
        }
    }

    /**
     * @param string $indicatorDataSet
     * @param string $indicatorType
     * @param string $indicatorComparator
     *
     * @return RpgIndicator|null
     */
    private function findAnIndicator(string $indicatorDataSet, string $indicatorType, string $indicatorComparator)
    {
        $indicatorObject = $this->doctrine->getRepository(RpgIndicator::class)->findOneBy([
            'dataSet' => $indicatorDataSet,
            'type' => $indicatorType,
            'comparator' => $indicatorComparator,
        ]);

        if (is_null($indicatorObject)) {
            $indicatorArray = $this->findIndicatorInDefault($indicatorDataSet, $indicatorType, $indicatorComparator);
            if (is_null($indicatorArray)) {
                return NULL;
            } else {
                $indicatorObject = new RpgIndicator();
                $indicatorObject->setName($indicatorArray['name']);
                $indicatorObject->setDataSet($indicatorDataSet);
                $indicatorObject->setType($indicatorType);
                $indicatorObject->setComparator($indicatorComparator);

                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($indicatorObject);
                $entityManager->flush();
            }
        }

        return $indicatorObject;
    }

    /**
     * @param string $indicatorDataSet
     * @param string $indicatorType
     * @param string $indicatorComparator
     *
     * @return array|null
     */
    private function findIndicatorInDefault(string $indicatorDataSet, string $indicatorType, string $indicatorComparator)
    {
        $standard = [
            'FitStepsDailySummary' => [
                'goal' => [
                    '>300%' => [
                        "name" => "300% of Step Goal",
                    ],
                    '>250%' => [
                        "name" => "250% of Step Goal",
                    ],
                    '>200%' => [
                        "name" => "200% of Step Goal",
                    ],
                    '>150%' => [
                        "name" => "150% of Step Goal",
                    ],
                    '>100%' => [
                        "name" => "100% of Step Goal",
                    ],
                ],
            ],
            'FitDistanceDailySummary' => [
                'goal' => [
                    '>300%' => [
                        "name" => "300% of Distance Goal",
                    ],
                    '>250%' => [
                        "name" => "250% of Distance Goal",
                    ],
                    '>200%' => [
                        "name" => "200% of Distance Goal",
                    ],
                    '>150%' => [
                        "name" => "150% of Distance Goal",
                    ],
                    '>100%' => [
                        "name" => "100% of Distance Goal",
                    ],
                ],
            ],
        ];

        if (array_key_exists($indicatorDataSet, $standard) &&
            array_key_exists($indicatorType, $standard[$indicatorDataSet]) &&
            array_key_exists($indicatorComparator, $standard[$indicatorDataSet][$indicatorType])) {
            return $standard[$indicatorDataSet][$indicatorType][$indicatorComparator];
        } else {
            return NULL;
        }
    }

    /**
     * @param string $indicatorDataSet
     * @param string $indicatorType
     * @param string $indicatorComparator
     *
     * @return array|null
     */
    private function findAwardInDefault(string $indicatorDataSet, string $indicatorType, string $indicatorComparator)
    {
        $standard = [
            'FitStepsDailySummary' => [
                'goal' => [
                    '>300%' => [
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "badge",
                            'payload' => "pve_placeholder",
                        ],
                    ],
                    '>250%' => [
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "badge",
                            'payload' => "pve_placeholder",
                        ],
                    ],
                    '>200%' => [
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "badge",
                            'payload' => "pve_placeholder",
                        ],
                    ],
                    '>150%' => [
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "badge",
                            'payload' => "pve_placeholder",
                        ],
                    ],
                    '>100%' => [
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "badge",
                            'payload' => "pve_placeholder",
                        ],
                    ],
                ],
            ],
            'FitDistanceDailySummary' => [
                'goal' => [
                    '>300%' => [
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "badge",
                            'payload' => "pve_placeholder",
                        ],
                    ],
                    '>250%' => [
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "badge",
                            'payload' => "pve_placeholder",
                        ],
                    ],
                    '>200%' => [
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "badge",
                            'payload' => "pve_placeholder",
                        ],
                    ],
                    '>150%' => [
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "badge",
                            'payload' => "pve_placeholder",
                        ],
                    ],
                    '>100%' => [
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorDataSet . ' Target',
                            'text' => $indicatorDataSet . '/' . $indicatorType,
                            'text_long' => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator,
                            'type' => "badge",
                            'payload' => "pve_placeholder",
                        ],
                    ],
                ],
            ],
        ];

        if (array_key_exists($indicatorDataSet, $standard) &&
            array_key_exists($indicatorType, $standard[$indicatorDataSet]) &&
            array_key_exists($indicatorComparator, $standard[$indicatorDataSet][$indicatorType])) {
            return $standard[$indicatorDataSet][$indicatorType][$indicatorComparator];
        } else {
            return NULL;
        }
    }

}
