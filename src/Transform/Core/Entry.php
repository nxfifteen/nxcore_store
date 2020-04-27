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

/** @noinspection DuplicatedCode */

namespace App\Transform\Core;

use App\AppConstants;
use App\Entity\Patient;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\CommsManager;
use App\Transform\Base\BaseEntry;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
use Sentry;

/**
 * Class Entry
 *
 * @package App\Transform\SkiTracks
 */
class Entry extends BaseEntry
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var Patient $patient */
    private $patient;

    /**
     * Entry constructor.
     *
     * @param LoggerInterface $logger
     * @param Patient|null    $patient
     */
    public function __construct(LoggerInterface $logger, Patient $patient = null)
    {
        $this->logger = $logger;
        $this->patient = $patient;
    }

    /**
     * @param String          $data_set
     * @param                 $getContent
     * @param ManagerRegistry $doctrine
     * @param AwardManager    $awardManager
     * @param ChallengePve    $challengePve
     * @param CommsManager    $commsManager
     *
     * @return array|int|null
     */
    public function transform(
        string $data_set,
        $getContent,
        ManagerRegistry $doctrine,
        AwardManager $awardManager,
        ChallengePve $challengePve,
        CommsManager $commsManager
    ) {
        $translateEntity = null;

        if (!is_null($this->patient)) {
            Sentry\configureScope(function (Sentry\State\Scope $scope) use ($data_set): void {
                $scope->setUser([
                    'id' => $this->patient->getId(),
                    'username' => $this->patient->getUsername(),
                    'email' => $this->patient->getEmail(),
                    'service' => Constants::CORESERVICE,
                    'data_set' => $data_set,
                ]);
            });
        } else {
            Sentry\configureScope(function (Sentry\State\Scope $scope) use ($data_set): void {
                $scope->setUser([
                    'service' => Constants::CORESERVICE,
                    'data_set' => $data_set,
                ]);
            });
        }

        switch ($data_set) {
            case Constants::COREINTRADAYSTEPS:
                try {
                    $translateEntity = CoreSyncIntraDaySteps::translate($doctrine, $getContent, $awardManager);
                } catch (Exception $e) {
                }
                break;
            default:
                AppConstants::writeToLog('mirror.txt',
                    __CLASS__ . '::' . __FUNCTION__ . '|' . __LINE__ . ' Unknown data_set ' . $data_set);
                return -3;
                break;
        }

        if (!is_null($translateEntity)) {
            $entityManager = $doctrine->getManager();
            if (!is_array($translateEntity)) {
                $entityManager->persist($translateEntity);
                $entityManager->flush();
                $returnId = $translateEntity->getId();
            } else {
                $returnId = [];
                foreach ($translateEntity as $item) {
                    if (!is_null($item)) {
                        $entityManager->persist($item);
                        $entityManager->flush();
                        array_push($returnId, $item->getId());
                    }
                }
            }

            return $returnId;
        } else {
            return -1;
        }
    }
}
