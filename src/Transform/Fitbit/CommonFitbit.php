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


use App\Entity\PatientCredentials;
use djchen\OAuth2\Client\Provider\Fitbit;
use League\OAuth2\Client\Token\AccessToken;

class CommonFitbit
{


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
}
