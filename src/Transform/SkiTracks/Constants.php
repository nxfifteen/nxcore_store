<?php
/**
 * DONE This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

namespace App\Transform\SkiTracks;


use App\Transform\Transform;

class Constants extends Transform
{
    const SKITRACKSSERVICE = "SkiTracks";
    const SKITRACKSEXERCISE = "exercises";

    const ACTIVITY_UNKNOWN = 0;
    const ACTIVITY_SNOW_SKIING = 1;
    const ACTIVITY_SNOW_SNOWBOARDING = 2;
    const ACTIVITY_SNOW_SNOWMOBILE = 3;
    const ACTIVITY_SNOW_X_COUNTRY = 4;
    const ACTIVITY_SNOW_SNOWSHOE = 5;
    const ACTIVITY_SNOW_TELEMARK = 6;
    const ACTIVITY_SNOW_MONOSKI = 7;
    const ACTIVITY_SLEDDING = 8;
    const ACTIVITY_SITSKI = 9;
    const ACTIVITY_SNOW_KITING = 10;
    const ACTIVITY_SNOW_BIKE = 11;
    const ACTIVITY_DOG_SLEDDING = 12;
    const ACTIVITY_SKI_TOURING = 13;
    const ACTIVITY_SKI_MOUNTAINEERING = 14;
    const ACTIVITY_ICE_YACHTING = 15;
    const ACTIVITY_SPLIT_BOARDING = 16;
    const ACTIVITY_FAT_BIKE = 17;

    protected static function convertExerciseType(int $service)
    {
        switch ($service) {
            case self:: ACTIVITY_UNKNOWN:
                return "Unknown";
                break;
            case self:: ACTIVITY_SNOW_SKIING:
                return "Skiing";
                break;
            case self:: ACTIVITY_SNOW_SNOWBOARDING:
                return "Snowboarding";
                break;
            case self:: ACTIVITY_SNOW_SNOWMOBILE:
                return "Snowmobiling";
                break;
            case self:: ACTIVITY_SNOW_X_COUNTRY:
                return "Cross Country Skiing";
                break;
            case self:: ACTIVITY_SNOW_SNOWSHOE:
                return "Snowshoeing";
                break;
            case self:: ACTIVITY_SNOW_TELEMARK:
                return "Telemark";
                break;
            case self:: ACTIVITY_SNOW_MONOSKI:
                return "Mono Ski";
                break;
            case self:: ACTIVITY_SLEDDING:
                return "Sledding, tobogganing, bobsledding, luge";
                break;
            case self:: ACTIVITY_SITSKI:
                return "Sit Ski";
                break;
            case self:: ACTIVITY_SNOW_KITING:
                return "Snow Kiting";
                break;
            case self:: ACTIVITY_SNOW_BIKE:
                return "Snow Bike";
                break;
            case self:: ACTIVITY_DOG_SLEDDING:
                return "Dog Sledding";
                break;
            case self:: ACTIVITY_SKI_TOURING:
                return "Ski Touring";
                break;
            case self:: ACTIVITY_SKI_MOUNTAINEERING:
                return "Ski Mountaineering";
                break;
            case self:: ACTIVITY_ICE_YACHTING:
                return "Ice Yachting";
                break;
            case self:: ACTIVITY_SPLIT_BOARDING:
                return "Split Boarding";
                break;
            case self:: ACTIVITY_FAT_BIKE:
                return "Fat Bike";
                break;
            default:
                return "Custom type";
                break;
        }
    }
}
