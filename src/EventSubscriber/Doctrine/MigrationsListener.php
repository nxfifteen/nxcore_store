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

        $this->write(__LINE__ . ' +++');
        if ($returnNewIfMissing && is_null($dbRpgChallengeGlobal)) {
            $this->write(__LINE__);
            $dbRpgChallengeGlobal = new $entityClassName();
        } else {
            if ($returnNewIfMissing && !is_null($dbRpgChallengeGlobal)) {
                $this->write(__LINE__);
                $dbRpgChallengeGlobal = null;
            }
        }
        $this->write(__LINE__ . ' ---');

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
                                $this->buildDataSetCriteria($newDataSet['searchRules'], $newData), false);
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
        $this->installDefaultGlobalChallengesTheHobbit();
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
                    'setDescripton' => 'Shamlessly taken from https://mypeakchallenge.com, we invite you to take on the Munro Step Challenge.  Scale the highest peaks of each continent from the comfort of the pavement, treadmill, or even your living room!',
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
    private function installDefaultGlobalChallengesTheHobbit()
    {

        $uomMile = $this->getNewEntityIfMissing('UnitOfMeasurement', ['name' => 'mile'], false);

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
                    'setName' => 'The Hobbit',
                    'setUnitOfMeasurement' => $uomMile,
                    'setDescripton' => 'Walk, run, hike, bike, blade, swim - if you can measure the distance, you can do this challenge.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 967,
                    'setProgression' => 'flow',
                ],
                [
                    'setName' => 'Bag End to Rivendell',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'The Hobbit',
                    'setDescripton' => '',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 397,
                    'setProgression' => 'flow',
                ],
                [
                    'setName' => 'April 27 - Day 1: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Bilbo leaves Bag End and runs south down the lane toward Hobbiton.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 0,
                ],
                [
                    'setName' => 'April 27 - Day 1: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Crosses bridge across the Water. Turns east on the road to Bywater.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 0.5,
                ],
                [
                    'setName' => 'April 27 - Day 1: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Reaches The Green Dragon in Bywater by 11 a.m. [Note: an impossible feat, as he would have had to run 2 minute miles!]. The dwarves have a pony ready and they leave almost immediately. Gandalf soon joins them. Turn SE on Bywater Road. High banks with hedges rise on each side.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 5,
                ],
                [
                    'setName' => 'April 27 - Day 1: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Reach the Great East Road. Turn east.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 7,
                ],
                [
                    'setName' => 'April 27 - Day 1: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Pass Three-Farthing Stone.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 9,
                ],
                [
                    'setName' => 'April 27 - Day 1: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 11,
                ],
                [
                    'setName' => 'April 28 - Day 2: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Frogmorton. Stay at The Floating Log.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 23,
                ],
                [
                    'setName' => 'April 29 - Day 3: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'See the trees of Woody End across the fields to the south. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 34,
                ],
                [
                    'setName' => 'April 30 - Day 4: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Reach the Brandywine Bridge. Stay at the Bridge Inn.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 45,
                ],
                [
                    'setName' => 'May 1 - Day 5: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Stop for the night. ',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 55,
                ],
                [
                    'setName' => 'May 2 - Day 6: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'The Old Forest is now quite close.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 61,
                ],
                [
                    'setName' => 'May 2 - Day 6: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp next to the Road.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 65,
                ],
                [
                    'setName' => 'May 3 - Day 7: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'To the south the Old Forest ends.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 69,
                ],
                [
                    'setName' => 'May 3 - Day 7: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 75,
                ],
                [
                    'setName' => 'May 4 - Day 8: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Barrow-downs continue. Between the Road and the Downs is a hedge.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 81,
                ],
                [
                    'setName' => 'May 4 - Day 8: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Reach Bree. Stay at The Prancing Pony. Spend a late evening.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 85,
                ],
                [
                    'setName' => 'May 5 - Day 9: 6 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Reach the South-gate of Bree. Leave village and ride east on the road.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 86,
                ],
                [
                    'setName' => 'May 5 - Day 9: 6 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp next to a trail leading north into the Chetwood on Bree-hill.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 91,
                ],
                [
                    'setName' => 'May 6 - Day 10: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Continue downhill along road next to the heart of the Chetwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 96,
                ],
                [
                    'setName' => 'May 6 - Day 10: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Stay at The Forsaken Inn: the last inn along the Great East Road.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 101,
                ],
                [
                    'setName' => 'May 7 - Day 11: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Reach the east edge of the Chetwood. Scattered farms to the south.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 107,
                ],
                [
                    'setName' => 'May 7 - Day 11: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Begin to see the Midgewater Marshes off to the northeast.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 111,
                ],
                [
                    'setName' => 'May 7 - Day 11: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 113,
                ],
                [
                    'setName' => 'May 8 - Day 12: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road runs gently downhill.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 117,
                ],
                [
                    'setName' => 'May 8 - Day 12: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Due north lie the western edge of the Midgewater Marshes.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 125,
                ],
                [
                    'setName' => 'May 9 - Day 13: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Meet a traveller who hurries by.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 133,
                ],
                [
                    'setName' => 'May 9 - Day 13: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp. Marshes to the north are now closer to the road.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 137,
                ],
                [
                    'setName' => 'May 10 - Day 14: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road curves more to the east. Marshes fill all the northern horizon.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 143,
                ],
                [
                    'setName' => 'May 10 - Day 14: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp due south of the Midgewater Marshes.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 149,
                ],
                [
                    'setName' => 'May 11 - Day 15: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road continues east around southern edge of the Marshes.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 155,
                ],
                [
                    'setName' => 'May 11 - Day 15: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp south of the road, away from the Marshes.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 160,
                ],
                [
                    'setName' => 'May 12 - Day 16: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road now turns slightly more to the north.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 165,
                ],
                [
                    'setName' => 'May 12 - Day 16: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Reach the southeast tip of the Midgewater Marshes. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 170,
                ],
                [
                    'setName' => 'May 13 - Day 17: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Weathertop is visible ahead, with the Weather Hills to its north.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 175,
                ],
                [
                    'setName' => 'May 13 - Day 17: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp. Open, rough lands on both sides of the road. Weather pleasant.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 180,
                ],
                [
                    'setName' => 'May 14 - Day 18: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Land now slopes slowly uphill toward Weathertop.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 185,
                ],
                [
                    'setName' => 'May 14 - Day 18: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Weathertop rises more clearly ahead. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 190,
                ],
                [
                    'setName' => 'May 15 - Day 19: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Weathertop looms ahead and fills all the view.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 195,
                ],
                [
                    'setName' => 'May 15 - Day 19: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Weathertop rises immediately north of the Road. Camp at its foot.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 200,
                ],
                [
                    'setName' => 'May 16 - Day 20: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road reaches southeastern foot of Weathertop.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 205,
                ],
                [
                    'setName' => 'May 16 - Day 20: 10 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Come to the last foothill below Weathertop. Camp at its foot.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 210,
                ],
                [
                    'setName' => 'May 17 - Day 21: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'South of the road are more rugged areas, covered with thickets.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 215,
                ],
                [
                    'setName' => 'May 17 - Day 21: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp. Weather still is warm and clear.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 219,
                ],
                [
                    'setName' => 'May 18 - Day 22: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 228,
                ],
                [
                    'setName' => 'May 19 - Day 23: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road deteriorates still more. Weathertop now appears lower. Camp. ',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 237,
                ],
                [
                    'setName' => 'May 20 - Day 24: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road turns more east. Weathertop is no longer straight behind.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 242,
                ],
                [
                    'setName' => 'May 20 - Day 24: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Continue east. Camp. Weather Hills still visible on the western horizon.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 246,
                ],
                [
                    'setName' => 'May 21 - Day 25: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Continue east on the Road. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 255,
                ],
                [
                    'setName' => 'May 22 - Day 26: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Continue east. Far behind, the Weather Hills show less and less. Ahead, company begins to see the tops of the Trollshaws. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 264,
                ],
                [
                    'setName' => 'May 23 - Day 27: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Continue east. Road is rough. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 273,
                ],
                [
                    'setName' => 'May 24 - Day 28: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Continue east. Behind, Weather Hills have almost disappeared. Ahead, dark wooded hills appear higher. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 282,
                ],
                [
                    'setName' => 'May 25 - Day 29: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Open country. No streams. Camp. ',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 291,
                ],
                [
                    'setName' => 'May 26 - Day 30: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road curves more to southeast. South of the road are bushes and stunted trees: wild and pathless. Camp north of the road.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 300,
                ],
                [
                    'setName' => 'May 27 - Day 31: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Weather pleasant. Ahead, Trollshaws show clearly. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 309,
                ],
                [
                    'setName' => 'May 28 - Day 32: 8 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road swings southeast through open country. Ahead on hills of the Trollshaws, can now see old castles with an evil look. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 317,
                ],
                [
                    'setName' => 'May 29 - Day 33: 15 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Reach northeast end of a valley south of the road. Continue on. Road now is only a muddy track.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 323,
                ],
                [
                    'setName' => 'May 29 - Day 33: 15 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Nearly dark: cross The Last Bridge. Stop to camp in clump of trees. Realize Gandalf is missing. Pony bolts into river and loses baggage with food. See fire in trees on hillside to east. Decide to investigate.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 325,
                ],
                [
                    'setName' => 'May 29 - Day 33: 15 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'No path through trees to fire. Reach fire: Trolls. Company captured. Note: This location is the most difficult of the entire journey to reconcile with LOTR. While logically, the Company probably went a mile or two at most, this does not work at all with the LOTR pathways. Even at a minimum, it should be at least 7 miles from the bridge to the Trolls. See detailed discussion and maps in The Atlas.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 332,
                ],
                [
                    'setName' => 'May 30 - Day 34: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Gandalf and company follow trail to Troll-hole. Find swords for Thorin and Gandalf. Bilbo takes blade. Collect gold. Return to fire.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 334,
                ],
                [
                    'setName' => 'May 30 - Day 34: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Continue down path and reach the Road. Bury gold and continue east.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 339,
                ],
                [
                    'setName' => 'May 30 - Day 34: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road now turns southeast. Skirts hills of the Trollshaws.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 340,
                ],
                [
                    'setName' => 'May 30 - Day 34: 12 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 344,
                ],
                [
                    'setName' => 'June 1 - Day 35: 16 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Enter woods. Continue east on road through woods.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 348,
                ],
                [
                    'setName' => 'June 1 - Day 35: 16 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Woods run into the valley on north.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 350,
                ],
                [
                    'setName' => 'June 1 - Day 35: 16 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Pass out-thrust toe of a hill.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 355,
                ],
                [
                    'setName' => 'June 1 - Day 35: 16 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp near valley from the north.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 360,
                ],
                [
                    'setName' => 'June 2 - Day 36: 13 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Can see ruins on hilltop to the north.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 365,
                ],
                [
                    'setName' => 'June 2 - Day 36: 13 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'South of the Road, ravine of the Bruinen comes close. Road turns NE.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 368,
                ],
                [
                    'setName' => 'June 2 - Day 36: 13 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Pass a valley from the north.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 370,
                ],
                [
                    'setName' => 'June 2 - Day 36: 13 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 373,
                ],
                [
                    'setName' => 'June 3 - Day 37: 16 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Cross a small stream. Road bends more northeast. The steep ravine of the Bruinen also runs east-northeast not far to the south.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 378,
                ],
                [
                    'setName' => 'June 3 - Day 37: 16 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Continue east-northeast along road . Ride very quickly.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 380,
                ],
                [
                    'setName' => 'June 3 - Day 37: 16 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road runs gently downhill. Much grass on sides. Hills still on the north.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 385,
                ],
                [
                    'setName' => 'June 3 - Day 37: 16 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Road drops through a cutting of red stone topped with tall pines, then crosses a long flat mile toward the river.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 388,
                ],
                [
                    'setName' => 'June 3 - Day 37: 16 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Reach the Ford of Bruinen. Can see Misty Mountains clearly. Camp. ',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 389,
                ],
                [
                    'setName' => 'June 4 - Day 38: 8 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Gandalf searches out the path. Must go carefully.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 390,
                ],
                [
                    'setName' => 'June 4 - Day 38: 8 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Path runs next to a steep gully on the left.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 392,
                ],
                [
                    'setName' => 'June 4 - Day 38: 8 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'On the east, a deep ravine holds a waterfall.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 394,
                ],
                [
                    'setName' => 'June 4 - Day 38: 8 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Come to the sudden cliff into the valley of Rivendell. Ride slowly down the zig-zag path. Pines cling to the upper slopes, beech and oak below.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 396,
                ],
                [
                    'setName' => 'June 4 - Day 38: 8 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Bag End to Rivendell',
                    'setDescripton' => 'Lead the ponies across the narrow bridge over the upper Bruinen. Reach the Last Homely House in Rivendell.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 397,
                ],
                [
                    'setName' => 'Rivendell to the Lonely Mountain',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'The Hobbit',
                    'setDescripton' => '',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 967,
                    'setProgression' => 'flow',
                ],
                [
                    'setName' => 'Midyear’s Day - Day 1: 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Leave Rivendell, camp in foothills - Ponies.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 401,
                ],
                [
                    'setName' => '2 Lithe - Day 2: 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Still in foothills - Ponies.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 405,
                ],
                [
                    'setName' => 'July 1-15 - Day 3-17: 4 miles per day = 68 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Climbing steadily - Ponies. 4 miles per day',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 465,
                ],
                [
                    'setName' => 'July 16 - Day 18: 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Tremendous Thunder-battle in afternoon. Shelter in a cave. Goblins capture them.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 469,
                ],
                [
                    'setName' => 'July 17/18/19 - Nt/Day/Nt/Day 19/20/a.m.21: 26 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Thorin questioned in Great Goblin’s Cavern. Gandalf rescues them.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 474,
                ],
                [
                    'setName' => 'July 17/18/19 - Nt/Day/Nt/Day 19/20/a.m.21: 26 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Company pauses. Gandalf makes light. Dwarves carry Bilbo.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 476,
                ],
                [
                    'setName' => 'July 17/18/19 - Nt/Day/Nt/Day 19/20/a.m.21: 26 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Gandalf and Thorin fight off Goblins.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 482,
                ],
                [
                    'setName' => 'July 17/18/19 - Nt/Day/Nt/Day 19/20/a.m.21: 26 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Goblins Attack. ',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 494,
                ],
                [
                    'setName' => 'July 17/18/19 - Nt/Day/Nt/Day 19/20/a.m.21: 26 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'End of Goblin attack. In rush, Bilbo accidentally left behind.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 495,
                ],
                [
                    'setName' => 'Bilbo Continues Alone: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo, while crawling, finds the ONE RING. Pulling out his sword for light, he begins trotting along passage.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 496,
                ],
                [
                    'setName' => 'Bilbo Continues Alone: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo unknowingly passes passage to ‘Back Door’',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 503.5,
                ],
                [
                    'setName' => 'Bilbo Continues Alone: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo reaches Gollum’s Lake. Riddle Contest.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 504.5,
                ],
                [
                    'setName' => 'Bilbo Continues Alone: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo follows Gollum back up passage. Side passages begin to appear.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 505,
                ],
                [
                    'setName' => 'Bilbo Continues Alone: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Gollum blocks entrance to tunnel to Back Door. Bilbo jumps over him.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 505.5,
                ],
                [
                    'setName' => 'Bilbo Continues Alone: 11 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo reaches the ‘Back Door’ and escapes.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 506,
                ],
                [
                    'setName' => 'Continuing July 19 - Day 21: 41 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo leaves upland valley on trail with cliff on left, drop-off on right.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 514,
                ],
                [
                    'setName' => 'Continuing July 19 - Day 21: 41 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo finds Dwarves and Gandalf in dell below trail. They continue on.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 518,
                ],
                [
                    'setName' => 'Continuing July 19 - Day 21: 41 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Cross a stream.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 522,
                ],
                [
                    'setName' => 'Continuing July 19 - Day 21: 41 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Trail leads to top of a landslide. They slip sideways in the stones.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 526,
                ],
                [
                    'setName' => 'Continuing July 19 - Day 21: 41 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Reach the bottom of the landslide and go east. Pine forest.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 527,
                ],
                [
                    'setName' => 'Continuing July 19 - Day 21: 41 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Clearing. Hear wolves. Climb trees. WARG ATTACK. Eagles Rescue.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 535,
                ],
                [
                    'setName' => 'Continuing July 19 - Day 21: 41 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Eagles take Company to their Eyrie. Sleep there.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 547,
                ],
                [
                    'setName' => 'July 20 - Day 22: 58 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Eagles fly Company to The Carrock.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 599,
                ],
                [
                    'setName' => 'July 20 - Day 22: 58 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Company reaches Beorn’s house.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 605,
                ],
                [
                    'setName' => 'July 22 - Day 24: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 614,
                ],
                [
                    'setName' => 'July 23 - Day 25: 20 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Ride through grasslands west of Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 634,
                ],
                [
                    'setName' => 'July 24 - Day 26: 25 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bright, fair, chill fall-like mist. Bilbo sees Beorn. Press on under moon.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 649,
                ],
                [
                    'setName' => 'July 25 - Day 27: 18 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Start before dawn. Land slopes up as they near the forest. Reach trees in afternoon. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 667,
                ],
                [
                    'setName' => 'July 26 - Day 28 (Day 1 in Mirkwood): 8 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'First camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 675,
                ],
                [
                    'setName' => 'July 27',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 684,
                ],
                [
                    'setName' => 'July 28',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 691,
                ],
                [
                    'setName' => 'July 29',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 698,
                ],
                [
                    'setName' => 'July 30',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 705,
                ],
                [
                    'setName' => 'July 31',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 712,
                ],
                [
                    'setName' => 'August 1',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 719,
                ],
                [
                    'setName' => 'August 2',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 726,
                ],
                [
                    'setName' => 'August 3',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 733,
                ],
                [
                    'setName' => 'August 4',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 740,
                ],
                [
                    'setName' => 'August 5',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 747,
                ],
                [
                    'setName' => 'August 6',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 754,
                ],
                [
                    'setName' => 'August 7',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 761,
                ],
                [
                    'setName' => 'August 8',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 768,
                ],
                [
                    'setName' => 'August 9',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 775,
                ],
                [
                    'setName' => 'August 10',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 782,
                ],
                [
                    'setName' => 'August 11',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 789,
                ],
                [
                    'setName' => 'August 12',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 796,
                ],
                [
                    'setName' => 'August 13',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Day 2 On Forest Trail in Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 803,
                ],
                [
                    'setName' => 'Aug. 16 - Day 48 (Day 21 in Mirkwood): 5 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'After 143 miles on Forest Trail, reach the Enchanted River. Cross by boat. Bombur falls in water and immediately falls asleep and must be carried.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 810,
                ],
                [
                    'setName' => 'Aug. 16 - Day 48 (Day 21 in Mirkwood): 5 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 813,
                ],
                [
                    'setName' => 'Aug. 17 - Day 49 (Day 22 in Mirkwood): 6 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'On Forest Trail.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 819,
                ],
                [
                    'setName' => 'Aug. 18 - Day 50 (Day 23 in Mirkwood): 6 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'On Forest Trail.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 825,
                ],
                [
                    'setName' => 'Aug. 19 - Day 51 (Day 24 in Mirkwood): 6 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Walk through open beech-woods much of the day.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 831,
                ],
                [
                    'setName' => 'Aug. 20 - Day 52 (Day 25 in Mirkwood): 6 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo climbs a tree (in an oak-wood), but can see nothing as it is in bottom of a bowl.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 835,
                ],
                [
                    'setName' => 'Aug. 20 - Day 52 (Day 25 in Mirkwood): 6 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Camp. Eat last of food at supper.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 837,
                ],
                [
                    'setName' => 'Aug. 21 - Day 53 (Day 26 in Mirkwood): 7 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'About to camp when see a fire. LEAVE PATH.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 843,
                ],
                [
                    'setName' => 'Aug. 21 - Day 53 (Day 26 in Mirkwood): 7 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Reach first Elves’ fire. Lights put out. Frantically search for each other.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 843.5,
                ],
                [
                    'setName' => 'Aug. 21 - Day 53 (Day 26 in Mirkwood): 7 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Reach second Elves’ fire. Lights put out again. Stay huddled together.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 844,
                ],
                [
                    'setName' => 'Aug. 22 - Day 54 (Day 27 in Mirkwood): 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Lights put out. Thorin captured by Elves. Dwarves scatter and are captured by spiders. Bilbo left alone. Kills spider. Swoons. Wakes during the morning and names his sword: Sting.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 844.5,
                ],
                [
                    'setName' => 'Aug. 22 - Day 54 (Day 27 in Mirkwood): 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo finds dwarves cocooned and rescues them.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 846,
                ],
                [
                    'setName' => 'Aug. 22 - Day 54 (Day 27 in Mirkwood): 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo draws spiders away. Dwarves almost recaptured. Bilbo returns. ',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 847,
                ],
                [
                    'setName' => 'Aug. 22 - Day 54 (Day 27 in Mirkwood): 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'After more fighting against spiders, spiders give up. Camp there.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 848,
                ],
                [
                    'setName' => 'Aug. 23 - Day 55 (Day 28 in Mirkwood): 7 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Struggle on all day. Dwarves surrender when surrounded by Elves. Bilbo uses ring and disappears.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 853,
                ],
                [
                    'setName' => 'Aug. 23 - Day 55 (Day 28 in Mirkwood): 7 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Dwarves imprisoned in Elvenking Thranduil’s Caverns. Bilbo follows',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 855,
                ],
                [
                    'setName' => 'Sept. 21 - Day 83: 14 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Leave trees of Mirkwood.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 857,
                ],
                [
                    'setName' => 'Sept. 21 - Day 83: 14 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Dusk - Reach huts of the Raft-elves. Dwarves still in barrels. Bilbo huddles nearby wearing Ring.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 869,
                ],
                [
                    'setName' => 'Sept. 22 - Day 84: 32 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Reach marshy area. Bilbo sees the Lonely Mountain.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 879,
                ],
                [
                    'setName' => 'Sept. 22 - Day 84: 32 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'End of the marshes. River rushes along.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 899,
                ],
                [
                    'setName' => 'Sept. 22 - Day 84: 32 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Reach Lake-town after sunset. Bilbo frees dwarves. They enter the town.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 901,
                ],
                [
                    'setName' => 'Oct. 9 - Day 101: 5 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Camp at the mouth of the River Running.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 906,
                ],
                [
                    'setName' => 'Oct. 10 - Day 102: 5 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Row upstream against current. Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 911,
                ],
                [
                    'setName' => 'Oct. 11 - Day 103: 5 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Third day in boats. Camp on west shore. Met there by ponies and supplies.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 916,
                ],
                [
                    'setName' => 'Oct. 12 - Day 104: 13 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Reach the Desolation of the Dragon.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 921,
                ],
                [
                    'setName' => 'Oct. 12 - Day 104: 13 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => '1st Camp - West of the south tip of the west spur of Lonely Mountain.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 929,
                ],
                [
                    'setName' => 'Oct. 13 - Day 105: 7 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo, Fili, Kili and Balin scout to River Running and go part way toward the Front Gate.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 936,
                ],
                [
                    'setName' => 'Oct. 14 - Day 106: 5 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Move to 2nd camp - In narrower valley due west of the mountain. ',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 941,
                ],
                [
                    'setName' => 'Oct. 19 - Day 111: 1 mile',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Move to upland bay by Hidden Door = 3rd Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 942,
                ],
                [
                    'setName' => 'Oct. 30 - Day 122: 4 miles Durin’s Day!',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo descends to Smaug’s Cellar, steals cup, returns. Smaug searches ANGRILY.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 946,
                ],
                [
                    'setName' => 'Nov. 1 - Day 123: 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo returns to Smaug’s Cellar. Riddles. ',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 948,
                ],
                [
                    'setName' => 'Nov. 1 - Day 123: 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'After Bilbo returns, company moves into passage. Smaug smashes mountainside, then attacks Lake-town. Bard kills Smaug.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 950,
                ],
                [
                    'setName' => 'Nov. 2 - Day 124: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Company goes to Smaug’s Cellar. Bilbo finds Arkenstone. Dwarves search hoard.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 952,
                ],
                [
                    'setName' => 'Nov. 2 - Day 124: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Go through Chamber of Thror.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 954.5,
                ],
                [
                    'setName' => 'Nov. 2 - Day 124: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Leave via Front Gate of the mountain.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 955,
                ],
                [
                    'setName' => 'Nov. 2 - Day 124: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Ford stream at the fallen bridge. Take the road into the valley of Dale.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 955.5,
                ],
                [
                    'setName' => 'Nov. 2 - Day 124: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Take a hill path from the road onto the western spur of the mountain.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 956.5,
                ],
                [
                    'setName' => 'Nov. 2 - Day 124: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Path reaches steep drop-off.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 958,
                ],
                [
                    'setName' => 'Nov. 2 - Day 124: 9 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Path ends at Guard-post. 4th Camp.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 959,
                ],
                [
                    'setName' => 'Nov. 3 - Day 125: 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'They learn of Smaug’s death and return to Front Gate. Fortify entrance. ',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 963,
                ],
                [
                    'setName' => 'Nov. 22 - Day 144: 4 miles',
                    'setUnitOfMeasurement' => $uomMile,
                    'setChildOf' => 'Rivendell to the Lonely Mountain',
                    'setDescripton' => 'Bilbo goes to Bard’s camp and gives him the Arkenstone. Returns to the gate of the Lonely Mountain.',
                    'setActive' => true,
                    'setCriteria' => 'FitDistanceDailySummary',
                    'setTarget' => 967,
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
