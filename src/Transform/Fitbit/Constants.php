<?php

namespace App\Transform\Fitbit;

use App\Transform\Transform;

define("FITBIT_COM", "https://api.fitbit.com");

class Constants extends Transform
{
    const FITBITSERVICE = "Fitbit";

    const FITBITEPBODYWEIGHT = "BodyWeight";
    const FITBITHEPDAILYSTEPS = "TrackingDevice::FitStepsDailySummary";

    public static function getPath(string $endpoint)
    {
        switch ( $endpoint ) {
            case 'BodyWeight':
                $path = '/body/date/{date}';
                break;

            case 'FitStepsDailySummary':
                $path = '/activities/date/{date}';
                break;

            case 'PatientGoals':
                $path = '/activities/goals/daily';
                break;

            case 'TrackingDevice':
                $path = '/devices';
                break;

            default:
                return null;
        }

        return FITBIT_COM . "/1/user/-$path";
    }
}