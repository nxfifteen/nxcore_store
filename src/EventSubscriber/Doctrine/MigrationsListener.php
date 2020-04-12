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

namespace App\EventSubscriber\Doctrine;

use App\AppConstants;
use App\Entity\RpgChallengeGlobal;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Migrations\Event\MigrationsEventArgs;
use Doctrine\Migrations\Event\MigrationsVersionEventArgs;
use Doctrine\Migrations\Events;

/**
 * Class MigrationsListener
 *
 * @package App\EventSubscriber\Doctrine
 */
class MigrationsListener implements EventSubscriber
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * MigrationsListener constructor.
     *
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param array $searchRules
     * @param array $newData
     *
     * @return array
     */
    private function buildDataSetCriteria(array $searchRules, array $newData)
    {
        if (array_key_exists("complex", $searchRules)) {
            $searchArray = [];
            foreach ($searchRules['complex'] as $key => $source) {
                $searchArray[$key] = $newData[$source];
            }
            return $searchArray;
        } else {
            return [$searchRules['identifier'] => $newData[$searchRules['comparitor']]];
        }
    }

    /**
     * @param string $entityClass
     * @param array  $entitySearchFilter
     * @param bool   $returnNewIfMissing
     *
     * @return RpgChallengeGlobal|null
     */
    private function getNewEntityIfMissing(
        string $entityClass,
        array $entitySearchFilter,
        bool $returnNewIfMissing = true
    ) {
        $entityClassName = "App\Entity\\" . $entityClass;
        /** @var RpgChallengeGlobal $dbRpgChallengeGlobal */
        $dbRpgChallengeGlobal = $this->managerRegistry
            ->getRepository($entityClassName)
            ->findOneBy($entitySearchFilter);

        if ($returnNewIfMissing && is_null($dbRpgChallengeGlobal)) {
            $dbRpgChallengeGlobal = new $entityClassName();
        } else {
            if ($returnNewIfMissing && !is_null($dbRpgChallengeGlobal)) {
                $dbRpgChallengeGlobal = null;
            }
        }

        return $dbRpgChallengeGlobal;
    }

    /**
     * @param array $newDataSet
     */
    private function installDataSet(array $newDataSet)
    {
        $i = 0;

        if (array_key_exists("linked", $newDataSet)) {
            $this->write("Installing new " . $newDataSet['searchRules']['class'] . "'s and their children");
            $hasChildRelationShip = true;
        } else {
            $this->write("Installing new " . $newDataSet['searchRules']['class'] . "'s");
            $hasChildRelationShip = false;
        }

        $entityManager = $this->managerRegistry->getManager();
        foreach ($newDataSet['data'] as $newData) {
            $i++;

            $newDbEntity = $this->getNewEntityIfMissing($newDataSet['searchRules']['class'],
                $this->buildDataSetCriteria($newDataSet['searchRules'], $newData));
            $entityClassName = "App\Entity\\" . $newDataSet['searchRules']['class'];
            if (!is_null($newDbEntity) && $newDbEntity instanceof $entityClassName) {
                $this->write(" Installing item " . $i . " of " . count($newDataSet['data']));
                foreach ($newData as $key => $newDatum) {
                    if (method_exists($newDbEntity, $key)) {
                        if ($hasChildRelationShip && $key == $newDataSet['linked']['comparitor'] && $newDatum != $newDataSet['linked']['igored']) {
                            $childDbEntity = $this->getNewEntityIfMissing($newDataSet['searchRules']['class'],
                                $this->buildDataSetCriteria([
                                    'identifier' => $newDataSet['searchRules']['identifier'],
                                    'comparitor' => 'setChildOf',
                                    'class' => $newDataSet['searchRules']['class'],
                                ], $newData), false);
                            if ($newDataSet['linked']['use'] == "object") {
                                $newDbEntity->$key($childDbEntity);
                            } else {
                                if (method_exists($childDbEntity, $newDataSet['linked']['use'])) {
                                    $methodName = $newDataSet['linked']['use'];
                                    $newDbEntity->$key($childDbEntity->$methodName());
                                }
                            }
                        } else {
                            if (!is_null($newDatum)) {
                                $newDbEntity->$key($newDatum);
                            }
                        }
                    } else {
                        $this->write(" Missing method " . $key . " to give it a value of " . gettype($newDatum));
                    }
                }
                $entityManager->persist($newDbEntity);
                $entityManager->flush();
            } else {
                $this->write(" Skipped item " . $i . " of " . count($newDataSet['data']));
            }
        }
    }

    /**
     *
     */
    private function installDefaultContributionLicense()
    {
        $newDataSet = [
            'searchRules' => [
                'identifier' => 'name',
                'comparitor' => 'setName',
                'class' => 'ContributionLicense',
            ],
            'data' => [
                [
                    'setName' => 'CC-BY-SA 3',
                    "setType" => "Free license",
                    "setDescription" => "Creative Commons Attribution Share Alike 3 (CC-BY-SA 3)",
                    "setLink" => "https://creativecommons.org/licenses/by-sa/3.0/deed.en",
                ],
            ],
        ];

        $this->installDataSet($newDataSet);
    }

    /**
     *
     */
    private function installDefaultFrontEndMenu()
    {
        $newDataSet = [
            'searchRules' => [
                'identifier' => 'name',
                'comparitor' => 'setName',
                'class' => 'SiteNavItem',
            ],
            'linked' => [
                'class' => 'SiteNavItem',
                'identifier' => 'name',
                'comparitor' => 'setChildOf',
                'use' => 'getId',
                'igored' => '0',
            ],
            'data' => [
                [
                    'setName' => 'Dashboard',
                    'setUrl' => '/dashboard',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-dashboard',
                    'setDisplayOrder' => -1000,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Workouts',
                    'setUrl' => null,
                    'setDivider' => false,
                    'setTitle' => true,
                    'setIcon' => null,
                    'setDisplayOrder' => 1000,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Exercise',
                    'setUrl' => '/exercises',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fitnessIcons-silhouette-1',
                    'setDisplayOrder' => 1100,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Exercises',
                    'setUrl' => '/exercises/overview',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fitnessIcons-person',
                    'setDisplayOrder' => 1101,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Exercise',
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Muscle Overview',
                    'setUrl' => '/exercises/muscle/overview',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fitnessIcons-silhouette-2',
                    'setDisplayOrder' => 1102,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Exercise',
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Equipment Overview',
                    'setUrl' => '/exercises/equipment/overview',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fitnessIcons-silhouette-4',
                    'setDisplayOrder' => 1103,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Exercise',
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Category Overview',
                    'setUrl' => '/exercises/category/overview',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fitnessIcons-people-1',
                    'setDisplayOrder' => 1104,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Exercise',
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Activity Tracker',
                    'setUrl' => '/activities/tracker',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-percent',
                    'setDisplayOrder' => 1200,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => 'disabled',
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Activity Log',
                    'setUrl' => '/activities/log',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-archive',
                    'setDisplayOrder' => 1300,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => ['App\Entity\Exercise'],
                ],
                [
                    'setName' => 'Stats',
                    'setUrl' => null,
                    'setDivider' => false,
                    'setTitle' => true,
                    'setIcon' => null,
                    'setDisplayOrder' => 2000,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Body Weight',
                    'setUrl' => '/body/weight',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'medicalIcons-scale-tool-to-control-body-weight-standing-on-it',
                    'setDisplayOrder' => 2100,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => ['App\Entity\BodyWeight'],
                ],
                [
                    'setName' => 'Fun',
                    'setUrl' => null,
                    'setDivider' => false,
                    'setTitle' => true,
                    'setIcon' => null,
                    'setDisplayOrder' => 3000,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Awards',
                    'setUrl' => '/achievements/awards',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-diamond',
                    'setDisplayOrder' => 3100,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Leaderboard',
                    'setUrl' => '/rpg/leaderboard',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-users',
                    'setDisplayOrder' => 3200,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Challenges',
                    'setUrl' => '/rpg/pve/challenges',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'adventureIcons-sports-10',
                    'setDisplayOrder' => 3500,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => '1:1 Challenges',
                    'setUrl' => '/rpg/challenges',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-trophy',
                    'setDisplayOrder' => 3400,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Administration',
                    'setUrl' => null,
                    'setDivider' => false,
                    'setTitle' => true,
                    'setIcon' => null,
                    'setDisplayOrder' => 4000,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Exercises Admin',
                    'setUrl' => '/exercises/admin',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'ico-cogs',
                    'setDisplayOrder' => 4100,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => 'ROLE_CONTRIBUTOR',
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'New Exercise',
                    'setUrl' => '/exercises/add',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fitnessIcons-person',
                    'setDisplayOrder' => 4101,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Exercises Admin',
                    'setAccessLevel' => 'ROLE_CONTRIBUTOR',
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'New Muscle',
                    'setUrl' => '/exercises/muscle/add',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fitnessIcons-silhouette-2',
                    'setDisplayOrder' => 4102,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Exercises Admin',
                    'setAccessLevel' => 'ROLE_CONTRIBUTOR',
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'New Equipment',
                    'setUrl' => '/exercises/equipment/add',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fitnessIcons-silhouette-4',
                    'setDisplayOrder' => 4103,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Exercises Admin',
                    'setAccessLevel' => 'ROLE_CONTRIBUTOR',
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'New Category',
                    'setUrl' => '/exercises/category/add',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fitnessIcons-people-1',
                    'setDisplayOrder' => 4104,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Exercises Admin',
                    'setAccessLevel' => 'ROLE_CONTRIBUTOR',
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Your Account',
                    'setUrl' => '/setup',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-cogs',
                    'setDisplayOrder' => 4200,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Profile',
                    'setUrl' => '/setup/profile',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-user',
                    'setDisplayOrder' => 4201,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Your Account',
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Select Your Account',
                    'setUrl' => '/setup/oauth',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-external-link',
                    'setDisplayOrder' => 4202,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Your Account',
                    'setAccessLevel' => null,
                    'setInDevelopment' => false,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Help & Support',
                    'setUrl' => null,
                    'setDivider' => true,
                    'setTitle' => true,
                    'setIcon' => null,
                    'setDisplayOrder' => 5000,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Help',
                    'setUrl' => '/help',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-life-saver',
                    'setDisplayOrder' => 5001,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 0,
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Patreon',
                    'setUrl' => '/help/patreon',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-dollar',
                    'setDisplayOrder' => 5002,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Help',
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Privacy Policy',
                    'setUrl' => '/help/privacy',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-low-vision',
                    'setDisplayOrder' => 5003,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Help',
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
                [
                    'setName' => 'Terms of Service',
                    'setUrl' => '/help/terms',
                    'setDivider' => false,
                    'setTitle' => false,
                    'setIcon' => 'fa fa-legal',
                    'setDisplayOrder' => 5004,
                    'setBadgeVariant' => null,
                    'setBadgeText' => null,
                    'setChildOf' => 'Help',
                    'setAccessLevel' => null,
                    'setInDevelopment' => true,
                    'setRequireService' => null,
                ],
            ],
        ];

        $this->installDataSet($newDataSet);
    }

    /**
     *
     */
    private function installDefaultGlobalChallenges()
    {
        $this->installDefaultGlobalChallengesMunros();
    }

    /**
     *
     */
    private function installDefaultGlobalChallengesMunros()
    {
        $newDataSet = [
            'searchRules' => [
                'identifier' => 'name',
                'comparitor' => 'setName',
                'class' => 'RpgChallengeGlobal',
            ],
            'linked' => [
                'class' => 'RpgChallengeGlobal',
                'identifier' => 'name',
                'comparitor' => 'setChildOf',
                'use' => 'object',
                'igored' => null,
            ],
            'data' => [
                [
                    'setName' => 'The Munro Step Challenge ' . date("Y"),
                    'setDescripton' => 'Shamlessly taken from https://mypeakchallenge.com, we invite you to take on the '
                        . 'Munro Step Challenge.  Scale the highest peaks of each continent from the comfort of the '
                        . 'pavement, treadmill, or even your living room!',
                    'setActive' => true,
                    'setCriteria' => 'FitStepsDailySummary',
                    'setTarget' => 292486,
                    'setProgression' => 'stage',
                ],
                [
                    'setName' => 'Everest',
                    'setChildOf' => 'The Munro Step Challenge ' . date("Y"),
                    'setDescripton' => 'The tallest peak of Asia',
                    'setActive' => true,
                    'setCriteria' => 'FitStepsDailySummary',
                    'setTarget' => 58044,
                    'setProgression' => null,
                ],
                [
                    'setName' => 'Mount Aconcagua',
                    'setChildOf' => 'The Munro Step Challenge ' . date("Y"),
                    'setDescripton' => 'The tallest peak of South America',
                    'setActive' => true,
                    'setCriteria' => 'FitStepsDailySummary',
                    'setTarget' => 45638,
                    'setProgression' => null,
                ],
                [
                    'setName' => 'Denali',
                    'setChildOf' => 'The Munro Step Challenge ' . date("Y"),
                    'setDescripton' => 'The tallest peak of North America',
                    'setActive' => true,
                    'setCriteria' => 'FitStepsDailySummary',
                    'setTarget' => 40462,
                    'setProgression' => null,
                ],
                [
                    'setName' => 'Kilimanjaro',
                    'setChildOf' => 'The Munro Step Challenge ' . date("Y"),
                    'setDescripton' => 'The tallest peak of Africa',
                    'setActive' => true,
                    'setCriteria' => 'FitStepsDailySummary',
                    'setTarget' => 38671,
                    'setProgression' => null,
                ],
                [
                    'setName' => 'Mount Elbrus',
                    'setChildOf' => 'The Munro Step Challenge ' . date("Y"),
                    'setDescripton' => 'The tallest peak of Europe',
                    'setActive' => true,
                    'setCriteria' => 'FitStepsDailySummary',
                    'setTarget' => 37012,
                    'setProgression' => null,
                ],
                [
                    'setName' => 'Vinson Massif',
                    'setChildOf' => 'The Munro Step Challenge ' . date("Y"),
                    'setDescripton' => 'The tallest peak of Antarctica',
                    'setActive' => true,
                    'setCriteria' => 'FitStepsDailySummary',
                    'setTarget' => 58043,
                    'setProgression' => null,
                ],
                [
                    'setName' => 'Mount Kosciuszko',
                    'setChildOf' => 'The Munro Step Challenge ' . date("Y"),
                    'setDescripton' => 'The tallest peak of Australia',
                    'setActive' => true,
                    'setCriteria' => 'FitStepsDailySummary',
                    'setTarget' => 14616,
                    'setProgression' => null,
                ],
            ],
        ];

        $this->installDataSet($newDataSet);
    }

    /**
     *
     */
    private function installDefaultMileStones()
    {
        $newDataSet = [
            'searchRules' => [
                'identifier' => 'msgLess',
                'comparitor' => 'setMsgLess',
                'class' => 'RpgMilestones',
            ],
            'data' => [
                [
                    'setCategory' => 'distance',
                    'setValue' => 20.5996,
                    'setMsgLess' => 'from Dundee to St.Andrews',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 35.4056,
                    'setMsgLess' => 'length of Loch Ness',
                    'setMsgMore' => 'around Loch Ness',
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 90.767,
                    'setMsgLess' => 'from Dundee to Edinburgh',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 120.8359,
                    'setMsgLess' => 'from Dundee to Glasgow',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 255.9919,
                    'setMsgLess' => 'from Dundee to Newcastle',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 396.4576,
                    'setMsgLess' => 'from Dundee to Leeds',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 5565.531,
                    'setMsgLess' => 'from Dundee to Paris',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 5565.531,
                    'setMsgLess' => 'from London to New York',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 12426.307,
                    'setMsgLess' => 'the UK coastline',
                    'setMsgMore' => 'around the UK coastline',
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 40065.709,
                    'setMsgLess' => 'around the world',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 384317.695,
                    'setMsgLess' => 'to the moon',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'distance',
                    'setValue' => 394848.6,
                    'setMsgLess' => 'the length of the UK motorways',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'floors',
                    'setValue' => 1710,
                    'setMsgLess' => 'to the top of The Eiffel Tower',
                    'setMsgMore' => 'The Eiffel Tower',
                ],
                [
                    'setCategory' => 'floors',
                    'setValue' => 1860,
                    'setMsgLess' => 'reached the stop of The Empire State Building',
                    'setMsgMore' => 'The Empire State Building',
                ],
                ['setCategory' => 'elevation', 'setValue' => 96, 'setMsgLess' => 'Big Ben', 'setMsgMore' => null],
                [
                    'setCategory' => 'elevation',
                    'setValue' => 135,
                    'setMsgLess' => 'the London Eye',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'elevation',
                    'setValue' => 301,
                    'setMsgLess' => 'the Eiffel Tower',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'elevation',
                    'setValue' => 609.6,
                    'setMsgLess' => 'into a hot air balloon',
                    'setMsgMore' => null,
                ],
                ['setCategory' => 'elevation', 'setValue' => 1085, 'setMsgLess' => 'Snowdon', 'setMsgMore' => null],
                [
                    'setCategory' => 'elevation',
                    'setValue' => 1344,
                    'setMsgLess' => 'to the top of Ben Nevis',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'elevation',
                    'setValue' => 4478,
                    'setMsgLess' => 'the Matterhorn',
                    'setMsgMore' => null,
                ],
                ['setCategory' => 'elevation', 'setValue' => 4810, 'setMsgLess' => 'Mont Blanc', 'setMsgMore' => null],
                [
                    'setCategory' => 'elevation',
                    'setValue' => 5895,
                    'setMsgLess' => 'Mount Kilimanjaro',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'elevation',
                    'setValue' => 18288,
                    'setMsgLess' => 'into Concorde, in flight',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'elevation',
                    'setValue' => 214042,
                    'setMsgLess' => 'as high a Sputnik',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'elevation',
                    'setValue' => 400000,
                    'setMsgLess' => 'aboard the International Space Station',
                    'setMsgMore' => null,
                ],
                [
                    'setCategory' => 'elevation',
                    'setValue' => 402336,
                    'setMsgLess' => 'to Mir space station orbit',
                    'setMsgMore' => null,
                ],
            ],
        ];

        $this->installDataSet($newDataSet);
    }

    /**
     *
     */
    private function installDefaultMuscles()
    {
        $newDataSet = [
            'searchRules' => [
                'identifier' => 'name',
                'comparitor' => 'setName',
                'class' => 'WorkoutMuscle',
            ],
            'data' => [
                ['setName' => 'Triceps Brochii', "setIsFront" => false],
                ['setName' => 'Anterior Deltoid', "setIsFront" => true],
                ['setName' => 'Pectoralis Major', "setIsFront" => true],
                ['setName' => 'Pectus Abdominis', "setIsFront" => true],
                ['setName' => 'Biceps Femoris', "setIsFront" => false],
                ['setName' => 'Gastrocnemius', "setIsFront" => false],
                ['setName' => 'Gluteus Maximus', "setIsFront" => false],
                ['setName' => 'Latissimus Dorsi', "setIsFront" => false],
                ['setName' => 'Soleus', "setIsFront" => false],
                ['setName' => 'Trapezius', "setIsFront" => false],
                ['setName' => 'Biceps Brochii', "setIsFront" => true],
                ['setName' => 'Brachialis', "setIsFront" => true],
                ['setName' => 'Obliquus Externus Abdominis', "setIsFront" => true],
                ['setName' => 'Quadriceps Femoris', "setIsFront" => true],
                ['setName' => 'Serratus Anterior', "setIsFront" => true],
            ],
        ];

        $this->installDataSet($newDataSet);
    }

    /**
     *
     */
    private function installDefaultPartOfDays()
    {
        $newDataSet = [
            'searchRules' => [
                'identifier' => 'name',
                'comparitor' => 'setName',
                'class' => 'PartOfDay',
            ],
            'data' => [
                ['setName' => 'morning'],
                ['setName' => 'afternoon'],
                ['setName' => 'evening'],
                ['setName' => 'night'],
            ],
        ];

        $this->installDataSet($newDataSet);
    }

    /**
     *
     */
    private function installDefaultUnitOfMeasurement()
    {
        $newDataSet = [
            'searchRules' => [
                'identifier' => 'name',
                'comparitor' => 'setName',
                'class' => 'UnitOfMeasurement',
            ],
            'data' => [
                ['setName' => 'Not Defined'],
                ['setName' => 'Default'],
                ['setName' => 'meter'],
                ['setName' => 'km'],
                ['setName' => 'mile'],
                ['setName' => 'ml'],
                ['setName' => 'ltr'],
                ['setName' => 'mg'],
                ['setName' => 'g'],
                ['setName' => 'kg'],
                ['setName' => '%'],
                ['setName' => 'Calorie'],
                ['setName' => 'Kilocalorie'],
            ],
        ];

        $this->installDataSet($newDataSet);
    }

    /**
     * @param string $printMessage
     */
    private function write(string $printMessage)
    {
        $indent = "  +++";
        echo $indent . " " . $printMessage . "\n";
        AppConstants::writeToLog('debug_transform.txt', "MigrationsListener:: " . $printMessage);
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onMigrationsMigrating,
            Events::onMigrationsMigrated,
            Events::onMigrationsVersionExecuting,
            Events::onMigrationsVersionExecuted,
            Events::onMigrationsVersionSkipped,
        ];
    }

    /** @noinspection PhpUnused */
    /**
     * @param MigrationsEventArgs $args
     */
    public function onMigrationsMigrated(MigrationsEventArgs $args): void
    {
        //
    }

    /** @noinspection PhpUnused */
    /**
     * @param MigrationsEventArgs $args
     */
    public function onMigrationsMigrating(MigrationsEventArgs $args): void
    {
        //
    }

    /** @noinspection PhpUnused */
    /**
     * @param MigrationsVersionEventArgs $args
     */
    public function onMigrationsVersionExecuted(MigrationsVersionEventArgs $args): void
    {
        $customUpgradeMethod = "postMigration" . ucwords($args->getDirection()) . $args->getConfiguration()->getCurrentVersion();
        if (method_exists($this, $customUpgradeMethod)) {
            $this->write("Running customer upgrade method for version " . $args->getConfiguration()->getCurrentVersion());
            $this->$customUpgradeMethod();
        } else {
            $this->write("No post-flight method named " . $customUpgradeMethod);
        }
    }

    /** @noinspection PhpUnused */
    /**
     * @param MigrationsVersionEventArgs $args
     */
    public function onMigrationsVersionExecuting(MigrationsVersionEventArgs $args): void
    {
        $customUpgradeMethod = "preMigration" . ucwords($args->getDirection()) . $args->getConfiguration()->getNextVersion();
        if (method_exists($this, $customUpgradeMethod)) {
            $this->write("Running customer upgrade method for version " . $args->getConfiguration()->getNextVersion());
            $this->$customUpgradeMethod();
        } else {
            $this->write("No pre-flight method named " . $customUpgradeMethod);
        }
    }

    /** @noinspection PhpUnused */
    /**
     * @param MigrationsVersionEventArgs $args
     */
    public function onMigrationsVersionSkipped(MigrationsVersionEventArgs $args): void
    {
        //
    }

    /**
     *
     */
    public function postMigrationUp20200411184303()
    {
        $this->installDefaultMuscles();
        $this->installDefaultPartOfDays();
        $this->installDefaultContributionLicense();
        $this->installDefaultMileStones();
        $this->installDefaultUnitOfMeasurement();
        $this->installDefaultFrontEndMenu();
        $this->installDefaultGlobalChallenges();
    }
}
