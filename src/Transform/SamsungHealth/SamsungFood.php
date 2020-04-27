<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nxcore/
 * @link      https://gitlab.com/nx-core/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

/** @noinspection DuplicatedCode */

namespace App\Transform\SamsungHealth;

use App\AppConstants;
use App\Entity\FoodDatabase;
use App\Entity\FoodDiary;
use App\Entity\FoodMeals;
use App\Entity\FoodNutrition;
use App\Entity\Patient;
use App\Entity\ThirdPartyService;
use App\Entity\TrackingDevice;
use App\Entity\UnitOfMeasurement;
use App\Service\AwardManager;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class SamsungFood
 *
 * @package App\Transform\SamsungHealth
 */
class SamsungFood extends Constants
{

    /**
     * food
     *
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return FoodNutrition|null
     * @throws Exception
     */
    public static function translateFood(ManagerRegistry $doctrine, string $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " -  translateFood: " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {
            ///AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - New call too FoodNutrition for " . $jsonContent->remoteId);

            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent->uuid);
            if (is_null($patient)) {
                return null;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::SAMSUNGHEALTHSERVICE);
            if (is_null($thirdPartyService)) {
                return null;
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $jsonContent->device);
            if (is_null($deviceTracking)) {
                return null;
            }

            /** @var FoodMeals $mealType */
            $mealType = self::getMealType($doctrine, self::convertMeal($jsonContent->meal_type));
            if (is_null($mealType)) {
                return null;
            }

//            if (property_exists($jsonContent, "title") && !empty($jsonContent->title) && self::startsWith($jsonContent->title, "MyFitnessPal")) {
//                /** @var FoodNutrition $dataEntry */
//                $dataEntry = $doctrine->getRepository(FoodNutrition::class)->findOneBy(['RemoteId' => $jsonContent->remoteId, 'trackingDevice' => $deviceTracking]);
//            } else {
//                /** @var FoodNutrition $dataEntry */
//                $dataEntry = $doctrine->getRepository(FoodNutrition::class)->findOneBy(['RemoteId' => $jsonContent->remoteId, 'trackingDevice' => $deviceTracking]);
//            }
            /** @var FoodNutrition $dataEntry */
            $dataEntry = $doctrine->getRepository(FoodNutrition::class)->findOneBy([
                'RemoteId' => $jsonContent->remoteId,
                'trackingDevice' => $deviceTracking,
            ]);

            if (!$dataEntry) {
                $dataEntry = new FoodNutrition();
                $safeGuid = false;
                $i = 0;
                do {
                    $i++;
                    $dataEntry->createGuid(true);
                    $dataEntryGuidCheck = $doctrine
                        ->getRepository(FoodNutrition::class)
                        ->findByGuid($dataEntry->getGuid());
                    if (empty($dataEntryGuidCheck)) {
                        $safeGuid = true;
                    } else {
                        AppConstants::writeToLog('debug_transform.txt',
                            __FILE__ . '@' . __LINE__ . ': Added a GUID (' . $i . ')');
                    }
                } while (!$safeGuid);
            }

            $dataEntry->setPatient($patient);
            $dataEntry->setRemoteId($jsonContent->remoteId);

            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> (new DateTime($jsonContent->dateTime))->format("U")) {
                $dataEntry->setDateTime(new DateTime($jsonContent->dateTime));
            }
            $dataEntry->setTrackingDevice($deviceTracking);
            $dataEntry->setMeal($mealType);

            if (property_exists($jsonContent, "carbohydrate") && $jsonContent->carbohydrate > 0) {
                $dataEntry->setCarbohydrate($jsonContent->carbohydrate);
            }
            if (property_exists($jsonContent, "calcium") && $jsonContent->calcium > 0) {
                $dataEntry->setCalcium($jsonContent->calcium);
            }
            if (property_exists($jsonContent, "calorie") && $jsonContent->calorie > 0) {
                $dataEntry->setCalorie($jsonContent->calorie);
            }
            if (property_exists($jsonContent, "cholesterol") && $jsonContent->cholesterol > 0) {
                $dataEntry->setCholesterol($jsonContent->cholesterol);
            }
            if (property_exists($jsonContent, "dietary_fiber") && $jsonContent->dietary_fiber > 0) {
                $dataEntry->setDietaryFiber($jsonContent->dietary_fiber);
            }
            if (property_exists($jsonContent, "iron") && $jsonContent->iron > 0) {
                $dataEntry->setIron($jsonContent->iron);
            }
            if (property_exists($jsonContent, "monosaturated_fat") && $jsonContent->monosaturated_fat > 0) {
                $dataEntry->setMonosaturatedFat($jsonContent->monosaturated_fat);
            }
            if (property_exists($jsonContent, "polysaturated_fat") && $jsonContent->polysaturated_fat > 0) {
                $dataEntry->setPolysaturatedFat($jsonContent->polysaturated_fat);
            }
            if (property_exists($jsonContent, "potassium") && $jsonContent->potassium > 0) {
                $dataEntry->setPotassium($jsonContent->potassium);
            }
            if (property_exists($jsonContent, "protein") && $jsonContent->protein > 0) {
                $dataEntry->setProtein($jsonContent->protein);
            }
            if (property_exists($jsonContent, "saturated_fat") && $jsonContent->saturated_fat > 0) {
                $dataEntry->setSaturatedFat($jsonContent->saturated_fat);
            }
            if (property_exists($jsonContent, "sodium") && $jsonContent->sodium > 0) {
                $dataEntry->setSodium($jsonContent->sodium);
            }
            if (property_exists($jsonContent, "sugar") && $jsonContent->sugar > 0) {
                $dataEntry->setSugar($jsonContent->sugar);
            }
            if (property_exists($jsonContent, "title")) {
                $dataEntry->setTitle($jsonContent->title);
            }
            if (property_exists($jsonContent, "total_fat") && $jsonContent->total_fat > 0) {
                $dataEntry->setTotalFat($jsonContent->total_fat);
            }
            if (property_exists($jsonContent, "trans_fat") && $jsonContent->trans_fat > 0) {
                $dataEntry->setTransFat($jsonContent->trans_fat);
            }
            if (property_exists($jsonContent, "vitamin_a") && $jsonContent->vitamin_a > 0) {
                $dataEntry->setVitA($jsonContent->vitamin_a);
            }
            if (property_exists($jsonContent, "vitamin_c") && $jsonContent->vitamin_c > 0) {
                $dataEntry->setVitC($jsonContent->vitamin_c);
            }

            if (is_null($deviceTracking->getLastSynced()) || $deviceTracking->getLastSynced()->format("U") < $dataEntry->getDateTime()->format("U")) {
                $deviceTracking->setLastSynced($dataEntry->getDateTime());
            }

            if (property_exists($jsonContent,
                    "title") && !empty($jsonContent->title) && !self::startsWith($jsonContent->title, "MyFitnessPal")) {
                self::updateApi($doctrine, "FoodNutrition", $patient, $thirdPartyService, $dataEntry->getDateTime());
            }

            return $dataEntry;

        }

        return null;
    }

    /**
     * food_info
     *
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return FoodDatabase|FoodNutrition|null
     * @throws Exception
     */
    public static function translateFoodInfo(ManagerRegistry $doctrine, string $getContent, AwardManager $awardManager)
    {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - translateFoodInfo : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid")) {

            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent->uuid);
            if (is_null($patient)) {
                return null;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::SAMSUNGHEALTHSERVICE);
            if (is_null($thirdPartyService)) {
                return null;
            }

            if (property_exists($jsonContent,
                    "name") && !empty($jsonContent->name) && self::startsWith($jsonContent->name, "MyFitnessPal")) {
                ///AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - New call too FoodDatabase for " . $jsonContent->remoteId);

                self::updateApi($doctrine, "FoodDatabase", $patient, $thirdPartyService, new DateTime());

                $jsonContent->title = $jsonContent->name;
                $jsonContent->remoteId = $jsonContent->provider_food_id;

                switch ($jsonContent->name) {
                    case "MyFitnessPal Breakfast":
                        $jsonContent->meal_type = 100001;
                        break;
                    case "MyFitnessPal Lunch":
                        $jsonContent->meal_type = 100002;
                        break;
                    case "MyFitnessPal Dinner":
                        $jsonContent->meal_type = 100003;
                        break;
                    case "MyFitnessPal Snacks":
                        $jsonContent->meal_type = 900001;
                        break;
                }

                if (preg_match('/^(?P<meal_id>[\d]+)-(?P<date>[\w ]+) 00:00:00 GMT([+\-]\d+:\d+|) (?P<year>[\d]+)/m',
                    $jsonContent->remoteId, $regs)) {
                    $jsonContent->meal_type = $regs['meal_id'];
                    $jsonContent->dateTime = $regs['date'] . " " . $regs['year'];
                } else {
                    ///AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - " . $jsonContent->remoteId);
                    $jsonContent->dateTime = str_ireplace($jsonContent->meal_type . "-", "", $jsonContent->remoteId);
                    ///AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - " . $jsonContent->dateTime);
                    $jsonContent->dateTime = str_ireplace("GMT+01:00 ", "", $jsonContent->dateTime);
                    $jsonContent->dateTime = str_ireplace("GMT ", "", $jsonContent->dateTime);
                    ///AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - " . $jsonContent->dateTime);
                    $jsonContent->dateTime = date('Y-m-d H:i:s', strtotime($jsonContent->dateTime));
                }
                ///AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - " . $jsonContent->remoteId);
                ///AppConstants::writeToLog('debug_transform.txt', "    - " . $jsonContent->meal_type);
                ///AppConstants::writeToLog('debug_transform.txt', "    - " . $jsonContent->dateTime);

                return self::translateFood($doctrine, self::encodeJson($jsonContent));
            } else {
                /** @var UnitOfMeasurement $unitOfMeasurement */
                if (property_exists($jsonContent, "metric_serving_unit")) {
                    $unitOfMeasurement = self::getUnitOfMeasurement($doctrine, $jsonContent->metric_serving_unit);
                    if (is_null($unitOfMeasurement)) {
                        return null;
                    }
                } else {
                    $unitOfMeasurement = null;
                }

                /** @var FoodDatabase $dataEntry */
                $dataEntry = $doctrine->getRepository(FoodDatabase::class)->findOneBy([
                    'providerId' => $jsonContent->provider_food_id,
                    'service' => $thirdPartyService,
                ]);
                if (!$dataEntry) {
                    $dataEntry = new FoodDatabase();
                }

                $dataEntry->setProviderId($jsonContent->provider_food_id);
                $dataEntry->setServingUnit($unitOfMeasurement);
                $dataEntry->setService($thirdPartyService);

                if (property_exists($jsonContent, "calorie") && $jsonContent->calorie > 0) {
                    $dataEntry->setCalorie($jsonContent->calorie);
                }
                if (property_exists($jsonContent, "carbohydrate") && $jsonContent->carbohydrate > 0) {
                    $dataEntry->setCarbohydrate($jsonContent->carbohydrate);
                }
                if (property_exists($jsonContent, "dietary_fiber") && $jsonContent->dietary_fiber > 0) {
                    $dataEntry->setDietaryFiber($jsonContent->dietary_fiber);
                }
                if (property_exists($jsonContent, "name") && !empty($jsonContent->name)) {
                    $dataEntry->setName($jsonContent->name);
                }
                if (property_exists($jsonContent, "protein") && $jsonContent->protein > 0) {
                    $dataEntry->setProtein($jsonContent->protein);
                }
                if (property_exists($jsonContent, "saturated_fat") && $jsonContent->saturated_fat > 0) {
                    $dataEntry->setSaturatedFat($jsonContent->saturated_fat);
                }
                if (property_exists($jsonContent, "metric_serving_amount") && $jsonContent->metric_serving_amount > 0) {
                    $dataEntry->setServingAmount($jsonContent->metric_serving_amount);
                }
                if (property_exists($jsonContent, "serving_description") && !empty($jsonContent->serving_description)) {
                    $dataEntry->setServingDescription($jsonContent->serving_description);
                }
                if (property_exists($jsonContent,
                        "default_number_of_serving_unit") && $jsonContent->default_number_of_serving_unit > 0) {
                    $dataEntry->setServingNumberDefault($jsonContent->default_number_of_serving_unit);
                }
                if (property_exists($jsonContent, "sugar") && $jsonContent->sugar > 0) {
                    $dataEntry->setSugar($jsonContent->sugar);
                }
                if (property_exists($jsonContent, "total_fat") && $jsonContent->total_fat > 0) {
                    $dataEntry->setTotalFat($jsonContent->total_fat);
                }

                $currentRemoteIds = $dataEntry->getRemoteIds();
                if (!in_array($jsonContent->remoteId, $currentRemoteIds)) {
                    array_push($currentRemoteIds, $jsonContent->remoteId);
                    $dataEntry->setRemoteIds($currentRemoteIds);
                }

                self::updateApi($doctrine, "FoodDatabase", $patient, $thirdPartyService, new DateTime());

                return $dataEntry;
            }

        }

        return null;
    }

    /**
     * food_intake
     *
     * @param ManagerRegistry $doctrine
     * @param String          $getContent
     *
     * @param AwardManager    $awardManager
     *
     * @return FoodDiary|null
     * @throws Exception
     */
    public static function translateFoodIntake(
        ManagerRegistry $doctrine,
        string $getContent,
        AwardManager $awardManager
    ) {
        $jsonContent = self::decodeJson($getContent);
        //AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - translateFoodIntake : " . print_r($jsonContent, TRUE));

        if (property_exists($jsonContent, "uuid") &&
            property_exists($jsonContent, "name") &&
            !empty($jsonContent->food_info_id) &&
            $jsonContent->food_info_id != "meal_removed" &&
            $jsonContent->food_info_id != "meal_auto_filled") {
            ///AppConstants::writeToLog('debug_transform.txt', __CLASS__ . '::' . __FUNCTION__ . '|' .__LINE__ . " - New call too FoodIntake for " . $jsonContent->remoteId);

            /** @var Patient $patient */
            $patient = self::getPatient($doctrine, $jsonContent->uuid);
            if (is_null($patient)) {
                return null;
            }

            /** @var ThirdPartyService $thirdPartyService */
            $thirdPartyService = self::getThirdPartyService($doctrine, self::SAMSUNGHEALTHSERVICE);
            if (is_null($thirdPartyService)) {
                return null;
            }

            /** @var TrackingDevice $deviceTracking */
            $deviceTracking = self::getTrackingDevice($doctrine, $patient, $thirdPartyService, $jsonContent->device);
            if (is_null($deviceTracking)) {
                return null;
            }

            /** @var UnitOfMeasurement $unitOfMeasurement */
            if (property_exists($jsonContent, "unit")) {
                $unitOfMeasurement = self::getUnitOfMeasurement($doctrine, self::convertMealUnit($jsonContent->unit));
                if (is_null($unitOfMeasurement)) {
                    return null;
                }
            } else {
                $unitOfMeasurement = null;
            }

            /** @var FoodMeals $mealType */
            $mealType = self::getMealType($doctrine, self::convertMeal($jsonContent->meal_type));
            if (is_null($mealType)) {
                return null;
            }

            /** @var FoodDatabase $mealFoodItem */
            $mealFoodItem = self::getMealFoodItem($doctrine, $jsonContent->food_info_id);
            if (is_null($mealFoodItem)) {
                return null;
            }

            /** @var FoodDiary $dataEntry */
            $dataEntry = $doctrine->getRepository(FoodDiary::class)->findOneBy([
                'remoteId' => $jsonContent->remoteId,
                'patient' => $patient,
            ]);
            if (!$dataEntry) {
                $dataEntry = new FoodDiary();
            }

            $dataEntry->setRemoteId($jsonContent->remoteId);

            if (is_null($dataEntry->getDateTime()) || $dataEntry->getDateTime()->format("U") <> (new DateTime($jsonContent->dateTime))->format("U")) {
                $dataEntry->setDateTime(new DateTime($jsonContent->dateTime));
            }

            $dataEntry->setMeal($mealType);
            $dataEntry->setPatient($patient);
            $dataEntry->setTrackingDevice($deviceTracking);
            if (property_exists($jsonContent, "amount") && $jsonContent->amount > 0) {
                $dataEntry->setAmount($jsonContent->amount);
            }
            $dataEntry->setFoodItem($mealFoodItem);
            if (property_exists($jsonContent, "comment") && !empty($jsonContent->comment)) {
                $dataEntry->setComment($jsonContent->comment);
            }
            $dataEntry->setUnit($unitOfMeasurement);

            self::updateApi($doctrine, "FoodIntake", $patient, $thirdPartyService, $dataEntry->getDateTime());

            return $dataEntry;

        }

        return null;
    }
}
