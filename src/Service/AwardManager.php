<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

namespace App\Service;


use App\AppConstants;
use App\Entity\FitDistanceDailySummary;
use App\Entity\FitStepsDailySummary;
use App\Entity\Patient;
use App\Entity\PatientMembership;
use App\Entity\RpgIndicator;
use App\Entity\RpgRewards;
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

    /**
     * @var Patient
     */
    private $patient;

    /**
     * @var DateTimeInterface
     */
    private $dateTime;

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
        if (!is_null($patient)) {
            $this->patient = $patient;
        } else if (get_class($dataEntry) == "App\Entity\FitStepsDailySummary" || get_class($dataEntry) == "App\Entity\FitDistanceDailySummary" || get_class($dataEntry) == "App\Entity\PatientMembership") {
            $this->patient = $dataEntry->getPatient();
        } else {
            AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Cant get a patient class from = ' . get_class($dataEntry));
        }

        if (!is_null($dateTime)) {
            $this->dateTime = $dateTime;
        } else {
            $this->dateTime = new DateTime();
        }

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
     * @param PatientMembership $dataEntry
     */
    private function checkForMembershipAwards(PatientMembership $dataEntry)
    {
        //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Origin class = ' . get_class($dataEntry));
        $indicatorDataSet = "membership";
        $indicatorType = $dataEntry->getTear();
        $indicatorComparator = $dataEntry->getActive();

        $this->findAndDeliveryRewards($indicatorDataSet, $indicatorType, $indicatorComparator);
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
            'membership' => [
                'all_history' => [
                    '1' => [
                        "name" => "Full Member",
                    ],
                ],
                'beta_user' => [
                    '1' => [
                        "name" => "Beta Tester",
                    ],
                ],
                'alpha_user' => [
                    '1' => [
                        "name" => "Alpha Tester",
                    ],
                ],
            ],
        ];

        if (array_key_exists($indicatorDataSet, $standard) &&
            array_key_exists($indicatorType, $standard[$indicatorDataSet]) &&
            array_key_exists($indicatorComparator, $standard[$indicatorDataSet][$indicatorType])) {
            return $standard[$indicatorDataSet][$indicatorType][$indicatorComparator];
        } else {
            //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': ' . $indicatorDataSet);
            //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': ' . $indicatorType);
            //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': ' . $indicatorComparator);
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
            'membership' => [
                'all_history' => [
                    '1' => [
                        [
                            'name' => 'Patreon Supporter XP',
                            'text' => 'patreon xp',
                            'text_long' => 'patreon xp longtext',
                            'type' => "xp",
                            'payload' => 10,
                        ],
                        [
                            'name' => 'Patreon Supporter',
                            'text' => 'patreon badge',
                            'text_long' => 'patreon badge_longtext',
                            'type' => "badge",
                            'payload' => json_encode(
                                [
                                    'html_title' => "Patreon",
                                    'header_image' => '../badges/patreon_header.png',
                                    "name" => "patreon name",
                                    "repeat" => FALSE,
                                    'badge_name' => 'patreon badge_name',
                                    'badge_image' => 'patreon',
                                    'badge_text' => "patreon badge_text",
                                    'badge_longtext' => "patreon badge_longtext",
                                    'badge_citation' => "patreon badge_citation",
                                ]
                            ),
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

    /**
     * @param array $dataEntry
     */
    private function checkForLoginAwards(array $dataEntry)
    {
        //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Origin class = ' . print_r($dataEntry, TRUE));

//        $this->findAndDeliveryRewards($indicatorDataSet, $indicatorType, $indicatorComparator);
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
        //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Origin class = ' . get_class($dataEntry));
        //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': ' . $citation);

//        $this->findAndDeliveryRewards($indicatorDataSet, $indicatorType, $indicatorComparator);

    }

    /**
     * @param FitStepsDailySummary|FitDistanceDailySummary $dataEntry
     *
     * @throws Exception
     */
    private function checkForGoalAwards($dataEntry)
    {
        //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Origin class = ' . get_class($dataEntry));

        if ($dataEntry->getValue() >= $dataEntry->getGoal()->getGoal()) {
            //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Value is greater than Goal');
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

            $this->findAndDeliveryRewards($indicatorDataSet, $indicatorType, $indicatorComparator);
        }
    }

    private function findAndDeliveryRewards(string $indicatorDataSet, string $indicatorType, string $indicatorComparator, DateTimeInterface $dateTime = null)
    {
        if (!is_null($dateTime)) {
            $this->dateTime = $dateTime;
        }

        $indicatorObject = $this->findAnIndicator($indicatorDataSet, $indicatorType, $indicatorComparator);
        if (is_null($indicatorObject)) {
            //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': No indicator was found');
        } else {
            //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Indicator found = ' . $indicatorObject->getName());
            if (count($indicatorObject->getRewards()) == 0) {
                //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Indicator has no awards');
                $awardDefaultArray = $this->findAwardInDefault($indicatorDataSet, $indicatorType, $indicatorComparator);
                if (is_array($awardDefaultArray)) {
                    foreach ($awardDefaultArray as $item) {
                        $rewardObject = $this->installReward($item, $indicatorObject);
                        $indicatorObject->addReward($rewardObject);
                    }
                }
            }

            foreach ($indicatorObject->getRewards() as $reward) {
                //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': ' . $reward->getType());
                $transformerClassName = 'App\\AwardDelivery\\' . ucwords($reward->getType());
                if (!class_exists($transformerClassName)) {
                    //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': ' . ucwords($reward->getType()));
                } else {
                    if (is_null($dateTime)) {
                        $dateTime = new DateTime();
                    }

                    //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__);
                    $rewardDelivery = new $transformerClassName($this->doctrine, $this->patient, $reward);
                    /** @noinspection PhpUndefinedMethodInspection */
                    $rewardDelivery->deliveryReward($this->dateTime);
                }
            }
        }
    }

}
