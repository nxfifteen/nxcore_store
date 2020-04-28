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

namespace App\Transform\Fitbit;


use App\AppConstants;
use App\Entity\PatientCredentials;
use DateTime;
use djchen\OAuth2\Client\Provider\Fitbit;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;

class CommonFitbit
{

    /**
     *
     */
    const FITBITSERVICE = "Fitbit";

    /**
     *
     */
    const FITBIT_COM = "https://api.fitbit.com";

    /**
     * @param PatientCredentials $credentials
     *
     * @return AccessToken
     */
    public static function getAccessToken(PatientCredentials $credentials)
    {
        return new AccessToken([
            'access_token' => $credentials->getToken(),
            'refresh_token' => $credentials->getRefreshToken(),
            'expires' => $credentials->getExpires()->format("U"),
        ]);
    }

    /**
     * @return Fitbit
     */
    public static function getLibrary()
    {
        return new Fitbit([
            'clientId' => $_ENV['FITBIT_ID'],
            'clientSecret' => $_ENV['FITBIT_SECRET'],
            'redirectUri' => $_ENV['INSTALL_URL'] . '/auth/refresh/fitbit',
        ]);
    }

    /**
     * @param $endpoint
     *
     * @return string|null
     */
    public static function convertEndpointToSubscription($endpoint)
    {
        switch ($endpoint) {
            case "BodyWeight":
                return "body";
                break;
            case "FitStepsDailySummary":
                return "activities";
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * @param AccessToken $accessToken
     * @param DateTime    $referenceTodayDate
     * @param DateTime    $apiAccessLog
     * @param string      $requestedEndpoint
     *
     * @return array[string, object]
     */
    public static function getResponceFromApi(
        AccessToken $accessToken,
        DateTime $referenceTodayDate,
        DateTime $apiAccessLog,
        string $requestedEndpoint
    ) {
        if (!$accessToken->hasExpired()) {
            $path = self::getApiEndpointUrl($requestedEndpoint, $referenceTodayDate, $apiAccessLog);
            AppConstants::writeToLog('debug_transform.txt', '$path is ' . $path);

            try {
                $fitbitApp = CommonFitbit::getLibrary();
                $request = $fitbitApp->getAuthenticatedRequest('GET', $path, $accessToken);
                $response = $fitbitApp->getParsedResponse($request);

                $responseObject = json_decode(json_encode($response), false);

                return [$path, $responseObject];
            } catch (IdentityProviderException $e) {
                AppConstants::writeToLog('debug_transform.txt', $e->getMessage());
            }
        } else {
            AppConstants::writeToLog('debug_transform.txt', "Token Expired, will retry later");
        }

        return [null, null];
    }

    /**
     * @param                   $requestedEndpoint
     * @param DateTime          $referenceTodayDate
     * @param DateTime          $apiAccessLog
     *
     * @return string|null
     */
    private static function getApiEndpointUrl($requestedEndpoint, DateTime $referenceTodayDate, DateTime $apiAccessLog)
    {
        $path = self::getApiPathFromEndpoint($requestedEndpoint);

        if (is_null($path)) {
            return null;
        }

        if (strpos($path, "{date}") !== false || strpos($path, "{period}") !== false) {
            $daysSince = round(($referenceTodayDate->format("U") - $apiAccessLog->format("U")) / (60 * 60 * 24), 0,
                PHP_ROUND_HALF_UP);
            $syncDate = clone $apiAccessLog;
            if ($daysSince > 0) {
                $syncDate->modify("+ " . $daysSince . " days");
                if ($syncDate->format("Y-m-d") > $syncDate->format("Y-m-d")) {
                    $syncDate = $syncDate->setTimestamp(strtotime('now'));
                }
            }

            if (strpos($path, "{period}") !== false) {
                $syncPeriod = self::calculateLastSyncPeriod($apiAccessLog->format("Y-m-d"));
                $path = str_replace("{period}", $syncPeriod, $path);
            }

            if (strpos($path, "{date}") !== false) {
                $path = str_replace("{date}", $syncDate->format("Y-m-d"), $path);
            }
        }

        if (strpos($path, "{ext}") !== false) {
            $path = str_replace("{ext}", '.json', $path);
        }

        return $path;
    }

    /**
     * @param string $endpoint
     *
     * @return string|null
     */
    private static function getApiPathFromEndpoint(string $endpoint)
    {
        switch ($endpoint) {
            case 'BodyWeight':
                $path = '/body/log/weight/date/{date}/1m{ext}';
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

        return self::FITBIT_COM . "/1/user/-$path";
    }

    /**
     * @param $syncDate
     *
     * @return float|string
     */
    private static function calculateLastSyncPeriod($syncDate)
    {
        $daysSince = round((date("U") - strtotime($syncDate)) / (60 * 60 * 24), PHP_ROUND_HALF_UP);
        if ($daysSince < 8) {
            $daysSince = "7d";
        } else {
            if ($daysSince < 30) {
                $daysSince = "30d";
            } else {
                if ($daysSince < 90) {
                    $daysSince = "3m";
                } else {
                    if ($daysSince < 180) {
                        $daysSince = "6m";
                    } else {
                        if ($daysSince < 364) {
                            $daysSince = "1y";
                        } else {
                            $daysSince = "1y";
                        }
                    }
                }
            }
        }

        return $daysSince;
    }

    /**
     * @param $endpoint
     *
     * @return array|null
     */
    public static function convertSubscriptionToClass($endpoint)
    {
        switch ($endpoint) {
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
                    "BodyWeight",
                ];
                break;

            default:
                return null;
        }
    }
}
