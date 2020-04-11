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

namespace App\EventSubscriber\Migrations;

use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\ApiAccessLog;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\ContributionLicense;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\FitFloorsIntraDay;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\FoodNutrition;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\PatientGoals;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\RpgIndicator;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\SiteNews;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\WorkoutCategories;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\BodyComposition;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\Exercise;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\FitStepsDailySummary;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\PartOfDay;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\PatientMembership;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\RpgMilestones;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\SyncQueue;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\WorkoutEquipment;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\BodyFat;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\ExerciseSummary;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\FitStepsIntraDay;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\Patient;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\PatientSettings;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\RpgRewards;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\ThirdPartyService;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\WorkoutExercise;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\BodyWeight;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\ExerciseType;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\FoodDatabase;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\PatientCredentials;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\RpgChallengeFriends;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\RpgRewardsAwarded;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\TrackingDevice;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\WorkoutMuscle;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\ConsumeCaffeine;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\FitCaloriesDailySummary;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\FoodDiary;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\PatientDevice;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\RpgChallengeGlobal;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\RpgXP;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\UnitOfMeasurement;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\WorkoutMuscleRelation;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\ConsumeWater;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\FitDistanceDailySummary;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\FoodMeals;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\PatientFriends;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\RpgChallengeGlobalPatient;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\SiteNavItem;
use /** @noinspection PhpUnusedAliasInspection */
    App\Entity\UploadedFile;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Migrations\Event\MigrationsEventArgs;
use Doctrine\Migrations\Event\MigrationsVersionEventArgs;
use Doctrine\Migrations\Events;

class MigrationsListener implements EventSubscriber
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

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

    public function onMigrationsMigrating(MigrationsEventArgs $args): void
    {
        //
    }

    public function onMigrationsMigrated(MigrationsEventArgs $args): void
    {
        //
    }

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

    public function onMigrationsVersionSkipped(MigrationsVersionEventArgs $args): void
    {
        //
    }

    private function write(string $printMessage)
    {
        $indent = "  +++";
        echo $indent . " " . $printMessage . "\n";
    }

    private function preMigrationUp20200411092158()
    {
        $this->postMigrationUp20200411072934();
    }

    private function postMigrationUp20200411072934()
    {
        $entityClasses = [
            'ApiAccessLog','ContributionLicense','FitFloorsIntraDay','FoodNutrition','PatientGoals','RpgIndicator',
            'SiteNews','WorkoutCategories','BodyComposition','Exercise','FitStepsDailySummary','PartOfDay',
            'PatientMembership','RpgMilestones','SyncQueue','WorkoutEquipment','BodyFat','ExerciseSummary',
            'FitStepsIntraDay','Patient','PatientSettings','RpgRewards','ThirdPartyService','WorkoutExercise',
            'BodyWeight','ExerciseType','FoodDatabase','PatientCredentials','RpgChallengeFriends','RpgRewardsAwarded',
            'TrackingDevice','WorkoutMuscle','ConsumeCaffeine','FitCaloriesDailySummary','FoodDiary','PatientDevice',
            'RpgChallengeGlobal','RpgXP','UnitOfMeasurement','WorkoutMuscleRelation','ConsumeWater',
            'FitDistanceDailySummary','FoodMeals','PatientFriends','RpgChallengeGlobalPatient','SiteNavItem','UploadedFile'
        ];

        foreach ($entityClasses as $entityClass) {
            $entityClassName = "App\Entity\\" . $entityClass;

            $objectTest = new $entityClassName();
            if (method_exists($objectTest, "getGuid")) {
                unset($objectTest);

                $dbPatients = $this->managerRegistry
                    ->getRepository($entityClassName)
                    ->findByGuid('');

                if (!is_null($dbPatients) && count($dbPatients) > 0) {
                    $entityManager = $this->managerRegistry->getManager();
                    $this->write("There are " . count($dbPatients) . " " . $entityClass . " records to update");
                    foreach ($dbPatients as $dbPatient) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $this->write(" New GUID for record " . $dbPatient->getId() . " -> " . $dbPatient->getGuid()->toString());
                        $entityManager->persist($dbPatient);
                    }
                    $entityManager->flush();
                    unset($entityManager);
                }

                unset($dbPatients);
            } else {
                unset($objectTest);
            }
        }
    }
}
