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

}
