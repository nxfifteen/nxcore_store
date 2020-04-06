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


namespace App\Transform\Fitbit;

use App\Transform\Transform;

define("FITBIT_COM", "https://api.fitbit.com");

class Constants extends Transform
{
    const FITBITSERVICE = "Fitbit";

    const FITBITEPBODYWEIGHT = "TrackingDevice::BodyWeight";
    const FITBITHEPDAILYSTEPS = "TrackingDevice::FitStepsDailySummary";
    const FITBITHEPPERIODSTEPS = "FitStepsPeriodSummary";
    const FITBITHEPDAILYSTEPSEXERCISE = "TrackingDevice::FitStepsDailySummary::Exercise";
    const FITBITEXERCISE = "TrackingDevice::Exercise";

    public static function getPath(string $endpoint)
    {
        switch ( $endpoint ) {
            case 'BodyWeight':
                $path = '/body/log/weight/date/{date}/{period}{ext}';
                break;

            case 'serviceProfile':
                $path = '/profile';
                break;

            case 'Exercise':
                $path = '/activities/list{ext}?afterDate={date}&offset=0&limit=20&sort=asc';
                break;

            case 'FitStepsDailySummary':
                $path = '/activities/date/{date}';
                break;

            case 'FitStepsPeriodSummary':
                $path = '/activities/steps/date/{date}/{period}';
                break;

            case 'PatientGoals':
                $path = '/activities/goals/daily';
                break;

            case 'TrackingDevice':
                $path = '/devices';
                break;

            case 'apiSubscriptions':
                $path = '/apiSubscriptions';
                break;

            default:
                return null;
        }

        return FITBIT_COM . "/1/user/-$path";
    }

    public static function convertSubscriptionToClass($endpoint) {
        switch ( $endpoint ) {
            case 'activities':
                return [
                    "TrackingDevice",
                    "FitStepsDailySummary",
                    "Exercise",
                ];
                break;
            case 'body':
                return [
                    "TrackingDevice",
                    "BodyWeight"
                ];
                break;

            default:
                return null;
        }
    }

    protected static function convertExerciseType(int $serviceId)
    {
        switch ($serviceId) {
            case 90013:
                return "Walking";
                break;
            default:
                return "Custom type";
                break;
        }
    }
}
