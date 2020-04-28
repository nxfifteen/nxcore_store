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

namespace App\Controller;

use App\Entity\Patient;
use LogicException;
use Sentry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Common extends AbstractController
{
    /** @var Patient $patient */
    protected $patient;

    protected $feed_storage;

    /**
     * @param String $userRole
     *
     * @throws LogicException If the Security component is not available
     */
    protected function hasAccess(string $userRole = 'ROLE_USER')
    {
        $this->denyAccessUnlessGranted($userRole, null, 'User tried to access a page without having ' . $userRole);
    }

    protected function setupRoute(string $userRole = 'ROLE_USER')
    {
        if (is_null($this->patient)) {
            $this->patient = $this->getUser();
        }
        $this->feed_storage = null;

        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setUser([
                'id' => $this->patient->getId(),
                'username' => $this->patient->getUsername(),
                'email' => $this->patient->getEmail(),
            ]);
        });

        $this->hasAccess($userRole);
    }

}
