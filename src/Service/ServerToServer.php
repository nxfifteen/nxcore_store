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

/** @noinspection PhpUnusedPrivateMethodInspection */

namespace App\Service;


use App\AppConstants;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

class ServerToServer
{
    /** @var KernelInterface $appKernel */
    private $appKernel;

    /** @var PkiManager $pkiManager */
    private $pkiManager;

    /** @var array $membershipServers */
    private $membershipServers;

    /** @var string $protical */
    private $protical = "0.0.1";

    /** @var array $membershipServer */
    private $membershipServer;

    /**
     * ServerToServer constructor.
     *
     * @param PkiManager      $pkiManager
     * @param KernelInterface $appKernel
     */
    public function __construct(PkiManager $pkiManager, KernelInterface $appKernel)
    {
        $this->pkiManager = $pkiManager;
        $this->appKernel = $appKernel;

        $membershipFile = $appKernel->getProjectDir() . '/var/private/membership.yml';
        $this->membershipServers = Yaml::parseFile($membershipFile);
    }

    /**
     * @param $origin
     *
     * @return bool|array
     */
    private function findMember($origin)
    {
        foreach ($this->membershipServers as $membershipServer) {
            if ($membershipServer['host'] == $origin) {
                $this->membershipServer = $membershipServer;
                return true;
            }
        }

        return false;
    }

    private function isProticalNew($protical)
    {
        $testLocalProtical = str_ireplace(".", "", $this->protical);
        $testRemoteProtical = str_ireplace(".", "", $protical);
        if ($testLocalProtical < $testRemoteProtical) {
            return false;
        }

        return true;
    }

    private function isProticalOld($protical)
    {
        $testLocalProtical = str_ireplace(".", "", $this->protical);
        $testRemoteProtical = str_ireplace(".", "", $protical);
        if ($testLocalProtical > $testRemoteProtical) {
            return false;
        }

        return true;
    }

    private function isSigValid($recivedData)
    {
        $dataThatWasHashed = hash("sha256",
            $recivedData['protical'] . $recivedData['origin'] . $recivedData['data'] . $recivedData['timestamp']);
        return $this->pkiManager->isSigVerifiable($dataThatWasHashed, $recivedData['signature'],
            $this->membershipServer['public_key']);
    }

    /**
     * @return int
     */
    private function returnForbidden()
    {
        return 403;
    }

    /**
     * @return int
     */
    private function returnNotAccepted()
    {
        return 406;
    }

    /**
     * @return int
     */
    private function returnNotImplimented()
    {
        return 501;
    }

    /**
     * @return int
     */
    private function returnOkay()
    {
        return 204;
    }

    /**
     * @return int
     */
    private function returnPreconditionFailed()
    {
        return 412;
    }

    public function memberCount()
    {
        return count($this->membershipServers);
    }

    /**
     * @param string $recivedData
     *
     * @return int|mixed
     */
    public function recievedFromMember(string $recivedData)
    {
        if ($this->memberCount() == 0) {
            return $this->returnForbidden();
        }

        $recivedData = json_decode($recivedData, true);

        if (!$this->findMember($recivedData['origin'])) {
            return $this->returnForbidden();
        }

        AppConstants::writeToLog('debug_transform.txt',
            '[DEST] Incomming from ' . $this->membershipServer['host'] . '(' . $this->membershipServer['contact'] . ')');

        if (!$this->isProticalOld($recivedData['protical'])) {
            return $this->returnPreconditionFailed();
        }

        if (!$this->isProticalNew($recivedData['protical'])) {
            return $this->returnNotImplimented();
        }

        if (!$this->isSigValid($recivedData)) {
            return $this->returnNotAccepted();
        }

        //AppConstants::writeToLog('debug_transform.txt', '[DEST] ' . print_r($recivedData, true));
        AppConstants::writeToLog('debug_transform.txt', '[DEST] Data Sign = Ok');

        $decryptedData = $this->pkiManager->decryptData($recivedData['data'], $recivedData['keys']);
        $decryptedData = json_decode($decryptedData, true);
        $decryptedData['data'] = json_decode($decryptedData['data'], true);
        AppConstants::writeToLog('debug_transform.txt', '[DEST] Decypted Data = ' . print_r($decryptedData, true));

        return $decryptedData;
    }

    public function sentToMembers(array $dataObject)
    {
        if (is_object($dataObject['data']) && method_exists($dataObject['data'], "toJson")) {
            $dataObject['data'] = $dataObject['data']->toJson();
        }

        $dataObject = json_encode($dataObject);

        foreach ($this->membershipServers as $membershipServer) {
            AppConstants::writeToLog('debug_transform.txt',
                '[SOURCE] Sending message too ' . $membershipServer['host'] . 'test/server_comms');

            $messagePacket = [
                "protical" => $this->protical,
                "origin" => $_ENV['INSTALL_URL'],
                "timestamp" => date("U"),
            ];

            [$messagePacket['data'], $messagePacket['keys']] = $this->pkiManager->encryptData($dataObject,
                $membershipServer['public_key']);

            $dataToHash = hash("sha256",
                $messagePacket['protical'] . $messagePacket['origin'] . $messagePacket['data'] . $messagePacket['timestamp']);
            $messagePacket['signature'] = $this->pkiManager->signData($dataToHash);

            $signed = $this->pkiManager->isSigVerifiable($dataToHash, $messagePacket['signature'],
                $this->pkiManager->getPublicKey());
            if ($signed) {
                $this->postToMirror(json_encode($messagePacket), $membershipServer['host'] . 'test/server_comms');
            } else {
                AppConstants::writeToLog('debug_transform.txt', '[SOURCE] Some when wrong checking the signature');
            }
        }
    }

    /**
     * @param string $body
     * @param string $dest
     */
    private function postToMirror(string $body, string $dest)
    {
        $ch = curl_init($dest);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        if (!empty($response)) {
            AppConstants::writeToLog('debug_transform.txt', '[SOURCE] Responce was ' . $response);
            AppConstants::writeToLog('error.html', $response);
        }

        curl_close($ch);
    }

}
