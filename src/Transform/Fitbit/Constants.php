<?php

namespace App\Transform\Fitbit;

use App\Transform\Transform;

define("FITBIT_COM", "https://api.fitbit.com");

class Constants extends Transform
{
    const FITBITSERVICE = "Fitbit";

    const FITBITEPBODYWEIGHT = "TrackingDevice::BodyWeight";
    const FITBITHEPDAILYSTEPS = "TrackingDevice::FitStepsDailySummary";
    const FITBITHEPPERIODSTEPS = "TrackingDevice::FitStepsPeriodSummary";

    public static function getPath(string $endpoint)
    {
        switch ( $endpoint ) {
            case 'BodyWeight':
                $path = '/body/date/{date}';
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
                    "FitStepsDailySummary"
                ];
                break;

            default:
                return null;
        }
    }
}