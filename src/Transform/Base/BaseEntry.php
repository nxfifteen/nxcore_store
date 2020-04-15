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

/** @noinspection DuplicatedCode */

namespace App\Transform\Base;

use App\AppConstants;

/**
 * Class Entry
 *
 * @package App\Transform\Base
 */
class BaseEntry
{

    /**
     * @param mixed $request
     */
    protected function postToMirror($request)
    {
        if (method_exists($request, "toJson") && array_key_exists("MESH_MIRROR",
                $_ENV) && !empty($_ENV['MESH_MIRROR'])) {
            $mirror_url = $_ENV['MESH_MIRROR'] . '/store/sync/upload/Core/' . str_ireplace("App\Entity\\", "",
                    get_class($request));
            $dataPacket = json_decode($request->toJson(), true);
            $dataPacket['AccessToken'] = 'blabla';
            $dataPacket['NodeToken'] = 'bleble';
            $dataPacket = json_encode($dataPacket);

            $ch = curl_init($mirror_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPacket);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            if (!empty($response)) {
                $response = json_decode($response);
                if ($response->success) {
                    AppConstants::writeToLog('mirror.txt',
                        '[' . $_ENV['MESH_MIRROR'] . '] Accepted the post to ' . $mirror_url);
                } else {
                    if ($response->status != 200) {
                        AppConstants::writeToLog('mirror.txt',
                            '[' . $_ENV['MESH_MIRROR'] . '] Failed to accept the post to ' . $mirror_url);
                        AppConstants::writeToLog('mirror.txt',
                            '[' . $_ENV['MESH_MIRROR'] . '] responded with ' . json_encode($response));
                    } else {
                        AppConstants::writeToLog('mirror.txt',
                            '[' . $_ENV['MESH_MIRROR'] . '] responded with ' . $response->status);
                    }
                }

            } else {
                AppConstants::writeToLog('mirror.txt',
                    '[' . $_ENV['MESH_MIRROR'] . '] sent an empty responce');
            }

            curl_close($ch);
        }
    }

}
