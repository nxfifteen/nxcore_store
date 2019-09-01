<?php

namespace App\Transform\SamsungHealth;


use App\Transform\Transform;

class Constants extends Transform
{
    const FITBITSERVICE = "Fitbit";
    const SAMSUNGHEALTHSERVICE = "Samsung Health";
    const SAMSUNGHEALTHEPDAILYSTEPS = "count_daily_steps";
    const SAMSUNGHEALTHEPDEVICES = "tracking_devices";
    const SAMSUNGHEALTHEPINTRADAYSTEPS = "intraday_steps";
    const SAMSUNGHEALTHEPINTRADAYFLOORS = "count_daily_floors";
    const SAMSUNGHEALTHEPCONSUMWATER = "water_intakes";
    const SAMSUNGHEALTHEPCONSUMCAFFINE = "caffeine_intakes";
    const SAMSUNGHEALTHEPBODYWEIGHT = "body_weights";
    const SAMSUNGHEALTHEPEXERCISE = "exercises";
    const SAMSUNGHEALTHCALORIES = "count_daily_calories";
    const SAMSUNGHEALTHDISTNACE = "count_daily_distances";

    protected static function convertExerciseType(int $serviceId)
    {
        switch ($serviceId) {
            case 1001:
                return "Walking";
                break;
            case 1002:
                return "Running";
                break;
            case 2001:
                return "Baseball";
                break;
            case 2002:
                return "Softball";
                break;
            case 2003:
                return "Cricket";
                break;
            case 3001:
                return "Golf";
                break;
            case 3002:
                return "Billiards";
                break;
            case 3003:
                return "Bowling, alley";
                break;
            case 4001:
                return "Hockey";
                break;
            case 4002:
                return "Rugby, touch";
                break;
            case 4003:
                return "Basketball";
                break;
            case 4004:
                return "Football";
                break;
            case 4005:
                return "Handball";
                break;
            case 4006:
                return "Soccer, touch";
                break;
            case 5001:
                return "Volleyball";
                break;
            case 5002:
                return "Beach volleyball";
                break;
            case 6001:
                return "Squash";
                break;
            case 6002:
                return "Tennis";
                break;
            case 6003:
                return "Badminton";
                break;
            case 6004:
                return "Table tennis";
                break;
            case 6005:
                return "Racquetball";
                break;
            case 7001:
                return "T'ai chi";
                break;
            case 7002:
                return "Boxing";
                break;
            case 7003:
                return "Martial arts, moderate pace";
                break;
            case 8001:
                return "Ballet";
                break;
            case 8002:
                return "Dancing";
                break;
            case 8003:
                return "Ballroom dancing";
                break;
            case 9001:
                return "Pilates";
                break;
            case 9002:
                return "Yoga";
                break;
            case 10001:
                return "Stretching";
                break;
            case 10002:
                return "Jump rope, moderate pace (100~120 skips/min)";
                break;
            case 10003:
                return "Hula-hooping";
                break;
            case 10004:
                return "Push-ups";
                break;
            case 10005:
                return "Pull-ups";
                break;
            case 10006:
                return "Sit-ups";
                break;
            case 10007:
                return "Circuit training";
                break;
            case 10008:
                return "Mountain climbers";
                break;
            case 10009:
                return "Jumping Jacks";
                break;
            case 10010:
                return "Burpee";
                break;
            case 10011:
                return "Bench press";
                break;
            case 10012:
                return "Squats";
                break;
            case 10013:
                return "Lunges";
                break;
            case 10014:
                return "Leg presses";
                break;
            case 10015:
                return "Leg extensions";
                break;
            case 10016:
                return "Leg curls";
                break;
            case 10017:
                return "Back extensions";
                break;
            case 10018:
                return "Lat pull-downs";
                break;
            case 10019:
                return "Deadlifts";
                break;
            case 10020:
                return "Shoulder presses";
                break;
            case 10021:
                return "Front raises";
                break;
            case 10022:
                return "Lateral raises";
                break;
            case 10023:
                return "Crunches";
                break;
            case 10024:
                return "Leg raises";
                break;
            case 10025:
                return "Plank";
                break;
            case 10026:
                return "Arm curls";
                break;
            case 10027:
                return "Arm extensions";
                break;
            case 11001:
                return "Inline skating, moderate pace";
                break;
            case 11002:
                return "Hang gliding";
                break;
            case 11003:
                return "Pistol shooting";
                break;
            case 11004:
                return "Archery";
                break;
            case 11005:
                return "Horseback riding";
                break;
            case 11007:
                return "Cycling";
                break;
            case 11008:
                return "Flying disc";
                break;
            case 11009:
                return "Roller skating";
                break;
            case 12001:
                return "Aerobics";
                break;
            case 13001:
                return "Hiking";
                break;
            case 13002:
                return "Rock climbing, low to moderate difficulty";
                break;
            case 13003:
                return "Backpacking";
                break;
            case 13004:
                return "Mountain biking";
                break;
            case 13005:
                return "Orienteering";
                break;
            case 14001:
                return "Swimming, leisurely, not lap swimming";
                break;
            case 14002:
                return "Aquarobics";
                break;
            case 14003:
                return "Canoeing";
                break;
            case 14004:
                return "Sailing";
                break;
            case 14005:
                return "Scuba diving";
                break;
            case 14006:
                return "Snorkeling";
                break;
            case 14007:
                return "Kayaking, moderate effort";
                break;
            case 14008:
                return "Kitesurfing";
                break;
            case 14009:
                return "Rafting";
                break;
            case 14010:
                return "Rowing machine";
                break;
            case 14011:
                return "Windsurfing";
                break;
            case 14012:
                return "Yachting";
                break;
            case 14013:
                return "Water skiing";
                break;
            case 15001:
                return "Step machine";
                break;
            case 15002:
                return "Weight machine";
                break;
            case 15003:
                return "Exercise bike, Moderate to vigorous effort";
                break;
            case 15004:
                return "Rowing machine";
                break;
            case 15005:
                return "Treadmill, combination of jogging and walking";
                break;
            case 15006:
                return "Elliptical trainer, moderate effort";
                break;
            case 16001:
                return "Cross-country skiing, moderate speed (4.0~4.9 mph)";
                break;
            case 16002:
                return "Skiing, downhill, moderate effort";
                break;
            case 16003:
                return "Ice dancing";
                break;
            case 16004:
                return "Ice skating";
                break;
            case 16006:
                return "Ice hockey";
                break;
            case 16007:
                return "Snowboarding, moderate effort";
                break;
            case 16008:
                return "Alpine skiing, moderate effort";
                break;
            case 16009:
                return "Snowshoeing, moderate effort";
                break;
            default:
                return "Custom type";
                break;
        }
    }
}