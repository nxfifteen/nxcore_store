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

/** @noinspection DuplicatedCode */

namespace App\Service;


use App\AppConstants;
use App\Entity\BodyWeight;
use App\Entity\ConsumeCaffeine;
use App\Entity\ConsumeWater;
use App\Entity\Exercise;
use App\Entity\FitDistanceDailySummary;
use App\Entity\FitStepsDailySummary;
use App\Entity\FitStepsIntraDay;
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
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AwardManager
 *
 * @package App\Service
 */
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
     * @var KernelInterface $appKernel
     */
    private $appKernel;

    /**
     * @var DateTimeInterface
     */
    private $dateTime;

    public function __construct(
        ManagerRegistry $doctrine,
        Swift_Mailer $mailer,
        Environment $twig,
        TweetManager $tweetManager,
        KernelInterface $appKernel)
    {
        $this->doctrine = $doctrine;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->tweetManager = $tweetManager;
        $this->appKernel = $appKernel;
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
     *
     * @throws \Exception
     */
    public function checkForAwards($dataEntry, string $criteria = NULL, Patient $patient = NULL, string $citation = NULL, DateTimeInterface $dateTime = NULL)
    {
        if (!is_null($patient)) {
            $this->patient = $patient;
        } else if (
            get_class($dataEntry) == "App\Entity\FitStepsDailySummary" ||
            get_class($dataEntry) == "App\Entity\FitDistanceDailySummary" ||
            get_class($dataEntry) == "App\Entity\PatientMembership" ||
            get_class($dataEntry) == "App\Entity\FitStepsIntraDay" ||
            get_class($dataEntry) == "App\Entity\ConsumeWater" ||
            get_class($dataEntry) == "App\Entity\ConsumeCaffeine" ||
            get_class($dataEntry) == "App\Entity\Exercise" ||
            get_class($dataEntry) == "App\Entity\BodyWeight"
        ) {
            $this->patient = $dataEntry->getPatient();
        } else {
            AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Cant get a patient class from = ' . get_class($dataEntry));
        }

        if (!is_null($dateTime)) {
            $this->dateTime = $dateTime;
        } else {
            $this->dateTime = new DateTime();
        }

        switch (strtolower($criteria)) {
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
                $inputClass = str_ireplace("App\Entity\\", "", get_class($dataEntry));
                $methodName = "checkFor" . $inputClass;
                if (method_exists($this, $methodName)) {
//                    AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': ' . " Found checkFor" . $inputClass);
                    $this->$methodName($dataEntry);
                } else {
                    AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': ' . " Missin checkFor" . $inputClass);
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

    private function findAndDeliveryRewards(string $indicatorDataSet, string $indicatorType, string $indicatorComparator, DateTimeInterface $dateTime = NULL)
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
            'dataSet' => strtolower($indicatorDataSet),
            'type' => strtolower($indicatorType),
            'comparator' => strtolower($indicatorComparator),
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
        //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ' getProjectDir::' . $this->appKernel->getProjectDir());
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
            AppConstants::writeToLog('debug_transform.txt', __METHOD__ . ' ' . $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator);
            return ["name" => $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator, "description" => "AUTO"];
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
        //AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ' getProjectDir::' . $this->appKernel->getProjectDir());
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
            'login' => [
                'first' => [
                    '0' => [
                        [
                            'name' => 'First Login Today',
                            'text' => 'Daily Login Award',
                            'text_long' => 'First login for {DATE}',
                            'type' => "xp",
                            'payload' => 5,
                        ],
                    ],
                ],
                'streak' => [
                    '5' => [
                        [
                            'name' => $indicatorComparator . ' day streak',
                            'text' => $indicatorComparator . ' day login streak',
                            'text_long' => "You've logged in " . $indicatorComparator . " days in a row!",
                            'type' => "xp",
                            'payload' => 5,
                        ],
                    ],
                    '10' => [
                        [
                            'name' => $indicatorComparator . ' day streak',
                            'text' => $indicatorComparator . ' day login streak',
                            'text_long' => "You've logged in " . $indicatorComparator . " days in a row!",
                            'type' => "xp",
                            'payload' => 5,
                        ],
                    ],
                    '15' => [
                        [
                            'name' => $indicatorComparator . ' day streak',
                            'text' => $indicatorComparator . ' day login streak',
                            'text_long' => "You've logged in " . $indicatorComparator . " days in a row!",
                            'type' => "xp",
                            'payload' => 5,
                        ],
                    ],
                    '20' => [
                        [
                            'name' => $indicatorComparator . ' day streak',
                            'text' => $indicatorComparator . ' day login streak',
                            'text_long' => "You've logged in " . $indicatorComparator . " days in a row!",
                            'type' => "xp",
                            'payload' => 5,
                        ],
                    ],
                    '25' => [
                        [
                            'name' => $indicatorComparator . ' day streak',
                            'text' => $indicatorComparator . ' day login streak',
                            'text_long' => "You've logged in " . $indicatorComparator . " days in a row!",
                            'type' => "xp",
                            'payload' => 5,
                        ],
                    ],
                    '30' => [
                        [
                            'name' => $indicatorComparator . ' day streak',
                            'text' => $indicatorComparator . ' day login streak',
                            'text_long' => "You've logged in " . $indicatorComparator . " days in a row!",
                            'type' => "xp",
                            'payload' => 5,
                        ],
                        [
                            'name' => $indicatorComparator . ' day streak badge',
                            'text' => $indicatorComparator . ' day login streak',
                            'text_long' => "You've logged in " . $indicatorComparator . " days in a row!",
                            'type' => "badge",
                            'payload' => json_encode(
                                [
                                    'html_title' => "Awarded the Full Month badge",
                                    'header_image' => '../badges/streak_month_header.png',
                                    "name" => "Full Month",
                                    "repeat" => FALSE,
                                    'badge_name' => 'Full Month',
                                    'badge_image' => 'streak_month',
                                    'badge_text' => "31 Day Streak",
                                    'badge_longtext' => "You've logged in every day for a full month",
                                    'badge_citation' => "You've logged in every day for a full month",
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
            AppConstants::writeToLog('debug_transform.txt', __METHOD__ . ' ' . $indicatorDataSet . '/' . $indicatorType . '/' . $indicatorComparator);
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
     *
     * @throws Exception
     */
    private function checkForLoginAwards(array $dataEntry)
    {
        if (array_key_exists("reason", $dataEntry) && array_key_exists("length", $dataEntry)) {
            $this->findAndDeliveryRewards('login', $dataEntry['reason'], $dataEntry['length'], new DateTime(date("Y-m-d 00:00:00")));
        } else {
            AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Origin class = ' . print_r($dataEntry, TRUE));
        }
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
        $this->findAndDeliveryRewards(
            "pvp_" . strtolower($dataEntry['criteria']),
            strtolower($dataEntry['result']),
            round($dataEntry['target'] / $dataEntry['duration'], 0, PHP_ROUND_HALF_DOWN)
        );
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param FitStepsDailySummary $dataEntry
     *
     * @throws \Exception
     */
    private function checkForFitStepsDailySummary(FitStepsDailySummary $dataEntry)
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

            AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Patient::' . $dataEntry->getPatient()->getFirstName());
            AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Provided DateTime::' . $dataEntry->getDateTime()->format("Y-m-d H:i:s"));
            AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ': Current  DateTime::' . date("Y-m-d H:i:s"));
            $this->findAndDeliveryRewards($indicatorDataSet, $indicatorType, $indicatorComparator, new DateTime($dataEntry->getDateTime()->format("Y-m-d 00:00:00")));
        }
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param FitDistanceDailySummary $dataEntry
     *
     * @throws \Exception
     */
    private function checkForFitDistanceDailySummary(FitDistanceDailySummary $dataEntry)
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

            $this->findAndDeliveryRewards($indicatorDataSet, $indicatorType, $indicatorComparator, new DateTime(date("Y-m-d 00:00:00")));
        }
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param FitStepsIntraDay $dataEntry
     *
     * @throws \Exception
     */
    private function checkForFitStepsIntraDay(FitStepsIntraDay $dataEntry)
    {
        /** @var FitStepsIntraDay[] $product */
        $product = $this->doctrine
            ->getRepository(FitStepsIntraDay::class)
            ->findByForHour($dataEntry->getPatient()->getUuid(), $dataEntry->getDateTime()->format("Y-m-d"), $dataEntry->getHour(), $dataEntry->getTrackingDevice()->getId());

        $value = 0;
        foreach ($product as $item) {
            $value = $value + $item->getValue();
        }

        if ($value > 5000) {
            $this->findAndDeliveryRewards("intraday", "steps", "5000", new DateTime($dataEntry->getDateTime()->format("Y-m-d " . $dataEntry->getHour() . ":00:00" )));
        } else if ($value > 3000) {
            $this->findAndDeliveryRewards("intraday", "steps", "3000", new DateTime($dataEntry->getDateTime()->format("Y-m-d " . $dataEntry->getHour() . ":00:00" )));
        } else if ($value > 2500) {
            $this->findAndDeliveryRewards("intraday", "steps", "2500", new DateTime($dataEntry->getDateTime()->format("Y-m-d " . $dataEntry->getHour() . ":00:00" )));
        } else if ($value > 1500) {
            $this->findAndDeliveryRewards("intraday", "steps", "1500", new DateTime($dataEntry->getDateTime()->format("Y-m-d " . $dataEntry->getHour() . ":00:00" )));
        }else if ($value > 250) {
            $this->findAndDeliveryRewards("intraday", "steps", "250", new DateTime($dataEntry->getDateTime()->format("Y-m-d " . $dataEntry->getHour() . ":00:00" )));
        }
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param BodyWeight $dataEntry
     *
     * @throws \Exception
     */
    private function checkForBodyWeight(BodyWeight $dataEntry)
    {
        //
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param Exercise $dataEntry
     *
     * @throws \Exception
     */
    private function checkForExercise(Exercise $dataEntry)
    {
        $this->findAndDeliveryRewards("exercise_" . strtolower($dataEntry->getExerciseType()->getTag()), "duration", round ($dataEntry->getDuration(), -2), $dataEntry->getDateTimeStart());
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param ConsumeWater $dataEntry
     *
     * @throws \Exception
     */
    private function checkForConsumeWater(ConsumeWater $dataEntry)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var ConsumeWater[] $product */
        $product = $this->doctrine
            ->getRepository(ConsumeWater::class)
            ->findByDateRange($dataEntry->getPatient()->getUuid(), date("Y-m-d"));

        $waterSum = 0;
        foreach ($product as $item) {
            $waterSum = $waterSum + $item->getMeasurement();
        }

        if ($waterSum >= $dataEntry->getPatientGoal()->getGoal()) {
            $indicatorDataSet = "ConsumeWater";
            $indicatorType = "goal";
            if ($waterSum >= ($dataEntry->getPatientGoal()->getGoal() * 3)) {
                $indicatorComparator = ">300%";
            } else if ($waterSum >= ($dataEntry->getPatientGoal()->getGoal() * 2.5)) {
                $indicatorComparator = ">250%";
            } else if ($waterSum >= ($dataEntry->getPatientGoal()->getGoal() * 2)) {
                $indicatorComparator = ">200%";
            } else if ($waterSum >= ($dataEntry->getPatientGoal()->getGoal() * 1.5)) {
                $indicatorComparator = ">150%";
            } else {
                $indicatorComparator = ">100%";
            }

            $this->findAndDeliveryRewards($indicatorDataSet, $indicatorType, $indicatorComparator, new DateTime(date("Y-m-d 00:00:00")));
        }
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param ConsumeCaffeine $dataEntry
     *
     * @throws \Exception
     */
    private function checkForConsumeCaffeine(ConsumeCaffeine $dataEntry)
    {
//        AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ' ' . $dataEntry->getMeasurement());
//        AppConstants::writeToLog('debug_transform.txt', __METHOD__ . '@' . __LINE__ . ' ' . $dataEntry->getPatientGoal()->getGoal());
    }

}
