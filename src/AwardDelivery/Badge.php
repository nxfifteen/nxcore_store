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

namespace App\AwardDelivery;


use App\AppConstants;
use App\Entity\Patient;
use App\Entity\RpgRewards;
use App\Entity\RpgRewardsAwarded;
use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class Badge
 *
 * @package App\AwardDelivery
 */
class Badge extends AwardDelivery
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var Patient
     */
    private $patient;

    /**
     * @var RpgRewards
     */
    private $reward;

    /**
     * Badge constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param Patient         $patient
     * @param RpgRewards      $reward
     */
    public function __construct(
        ManagerRegistry $doctrine,
        Patient $patient,
        RpgRewards $reward
    ) {
        $this->doctrine = $doctrine;
        $this->patient = $patient;
        $this->reward = $reward;
    }

    /**
     * @param DateTimeInterface|NULL $dateTime
     *
     * @return bool
     */
    public function deliveryReward(DateTimeInterface $dateTime = null)
    {
        if (!is_string($this->reward->getPayload())) {
            return false;
        }

        /** @var RpgRewardsAwarded $xpAlreadyAwarded */
        $xpAlreadyAwarded = $this->doctrine->getRepository(RpgRewardsAwarded::class)->findOneBy([
            'patient' => $this->patient,
            'reward' => $this->reward,
            'datetime' => $dateTime,
        ]);
        if (!$xpAlreadyAwarded) {
            $rewarded = new RpgRewardsAwarded();
            $rewarded->setPatient($this->patient);
            $rewarded->setDatetime($dateTime);
            $rewarded->setReward($this->reward);

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($rewarded);
            $entityManager->flush();
        }

        return true;
    }
}
