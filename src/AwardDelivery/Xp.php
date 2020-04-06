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
use App\Entity\RpgXP;
use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class Xp
 *
 * @package App\AwardDelivery
 */
class Xp extends AwardDelivery
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
     * Xp constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param Patient         $patient
     * @param RpgRewards      $reward
     */
    public function __construct(
        ManagerRegistry $doctrine,
        Patient $patient,
        RpgRewards $reward
    )
    {
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
        if (!is_numeric($this->reward->getPayload())) {
            return false;
        }

        if ($this->reward->getPayload() > 0) {
            if (is_null($this->reward->getTextLong())) {
                $reasoning = $this->reward->getText();
            } else {
                $reasoning = $this->reward->getTextLong();
            }

            /** @var RpgXP $xpAlreadyAwarded */
            $xpAlreadyAwarded = $this->doctrine->getRepository(RpgXP::class)->findOneBy(['patient' => $this->patient, 'reason' => $reasoning, 'datetime' => $dateTime]);
            if (!$xpAlreadyAwarded) {
                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Awarding ' . $this->patient->getFirstName() . ' ' . $this->reward->getPayload() . 'XP for ' . $reasoning);

                $xpToAward = round(($this->reward->getPayload() * $this->patient->getRpgFactor()), 0, PHP_ROUND_HALF_DOWN);

                $xpAward = new RpgXP();
                $xpAward->setDatetime($dateTime);
                $xpAward->setReason($reasoning);
                $xpAward->setValue($xpToAward);
                $xpAward->setPatient($this->patient);

                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($xpAward);
                $entityManager->flush();

                AppConstants::writeToLog('debug_transform.txt', __LINE__ . ' Final award will be ' . $xpToAward . 'XP');
            }
        }

        return true;
    }
}
