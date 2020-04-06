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

namespace App\Transform\SkiTracks;

use App\AppConstants;
use App\Entity\Patient;
use App\Service\AwardManager;
use App\Service\ChallengePve;
use App\Service\TweetManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Object_;
use Psr\Log\LoggerInterface;
use Sentry;

/**
 * Class Entry
 *
 * @package App\Transform\SkiTracks
 */
class Entry
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
     * @param Patient|null         $patient
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
     * @param TweetManager    $tweetManager
     *
     * @return array|int|null
     */
    public function transform(String $data_set, $getContent, ManagerRegistry $doctrine, AwardManager $awardManager, ChallengePve $challengePve, TweetManager $tweetManager)
    {
        $translateEntity = NULL;

        if (!is_null($this->patient)) {
            Sentry\configureScope(function (Sentry\State\Scope $scope) use ($data_set): void {
                $scope->setUser([
                    'id' => $this->patient->getId(),
                    'username' => $this->patient->getUsername(),
                    'email' => $this->patient->getEmail(),
                    'service' => 'SkiTracks',
                    'data_set' => $data_set,
                ]);
            });
        } else {
            Sentry\configureScope(function (Sentry\State\Scope $scope) use ($data_set): void {
                $scope->setUser([
                    'service' => 'SkiTracks',
                    'data_set' => $data_set,
                ]);
            });
        }

        switch ($data_set) {
            case Constants::SKITRACKSEXERCISE:
                try {
                    $translateEntity = SkiTracksExercise::translate($doctrine, $getContent, $awardManager, $tweetManager, $this->patient);
                } catch (\Exception $e) {
                }
                break;
            default:
                return -3;
                break;
        }

        if (!is_null($translateEntity)) {
            $entityManager = $doctrine->getManager();
            if (!is_array($translateEntity)) {
                $entityManager->persist($translateEntity);
                $returnId = $translateEntity->getId();
            } else {
                $returnId = [];
                foreach ($translateEntity as $item) {
                    if (!is_null($item)) {
                        $entityManager->persist($item);
                        array_push($returnId, $item->getId());
                    }
                }
            }
            $entityManager->flush();

            return $returnId;
        } else {
            return -1;
        }
    }
}
