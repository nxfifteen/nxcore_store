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
use App\Entity\PatientMembership;
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
     * @param mixed                  $dataEntry
     * @param string|NULL            $criteria
     * @param Patient|NULL           $patient
     * @param string|NULL            $citation
     * @param DateTimeInterface|null $dateTime
     */
    public function checkForAwards($dataEntry, string $criteria = NULL, Patient $patient = NULL, string $citation = NULL, DateTimeInterface $dateTime = NULL)
    {
        switch ($criteria) {
            case "membership":
                $this->checkForMembershipAwards($dataEntry);
                break;
            case "login":
                $this->checkForLoginAwards($dataEntry);
                break;
            case "pve":
            case "challenge":
                $this->checkForChallengeAwards($dataEntry, $criteria, $patient, $citation, $dateTime);
                break;
            default:
                if (get_class($dataEntry) == "FitStepsDailySummary" || get_class($dataEntry) == "FitDistanceDailySummary") {
                    try {
                        $this->checkForGoalAwards($dataEntry);
                    } catch (Exception $e) {
                    }
                }
                break;

        }
    }

    /**
     * @param array $dataEntry
     */
    private function checkForLoginAwards(array $dataEntry)
    {
        AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Origin class = ' . print_r($dataEntry, true));
    }

    /**
     * @param PatientMembership $dataEntry
     */
    private function checkForMembershipAwards(PatientMembership $dataEntry)
    {
        AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Origin class = ' . get_class($dataEntry));
    }

    /**
     * @param mixed                  $dataEntry
     * @param string|NULL            $criteria
     * @param Patient|NULL           $patient
     * @param string|NULL            $citation
     * @param DateTimeInterface|null $dateTime
     */
    private function checkForChallengeAwards($dataEntry, string $criteria = NULL, Patient $patient = NULL, string $citation = NULL, DateTimeInterface $dateTime = NULL)
    {
        AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Origin class = ' . get_class($dataEntry));
        AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': ' . $citation);

    }

    /**
     * @param FitStepsDailySummary|FitDistanceDailySummary $dataEntry
     *
     * @throws Exception
     */
    private function checkForGoalAwards($dataEntry)
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
                            $rewardObject = $this->installReward($item, $indicatorObject);
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
                $indicatorObject = $this->installIndicator($indicatorArray, $indicatorDataSet, $indicatorType, $indicatorComparator);
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

    /**
     * @param array  $indicatorArray
     * @param string $indicatorDataSet
     * @param string $indicatorType
     * @param string $indicatorComparator
     *
     * @return RpgIndicator
     */
    private function installIndicator(array $indicatorArray, string $indicatorDataSet, string $indicatorType, string $indicatorComparator)
    {
        $indicatorObject = new RpgIndicator();
        $indicatorObject->setName($indicatorArray['name']);
        if (array_key_exists("description", $indicatorArray)) $indicatorObject->setDescription($indicatorArray['description']);
        $indicatorObject->setDataSet($indicatorDataSet);
        $indicatorObject->setType($indicatorType);
        $indicatorObject->setComparator($indicatorComparator);

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($indicatorObject);
        $entityManager->flush();

        return $indicatorObject;
    }

    /**
     * @param array        $item
     * @param RpgIndicator $indicatorObject
     *
     * @return RpgRewards
     */
    private function installReward(array $item, RpgIndicator $indicatorObject)
    {
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

        return $rewardObject;
    }

}
