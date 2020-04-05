<?php


namespace App\AwardDelivery;


use App\AppConstants;
use App\Entity\Patient;
use App\Entity\RpgRewards;
use App\Entity\RpgRewardsAwarded;
use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    public function deliveryReward(DateTimeInterface $dateTime = null) {
        if (!is_string($this->reward->getPayload())) {
            return false;
        }

        /** @var RpgRewardsAwarded $xpAlreadyAwarded */
        $xpAlreadyAwarded = $this->doctrine->getRepository(RpgRewardsAwarded::class)->findOneBy(['patient' => $this->patient, 'reward' => $this->reward, 'datetime' => $dateTime]);
        if (!$xpAlreadyAwarded) {
            $rewarded = new RpgRewardsAwarded();
            $rewarded->setPatient($this->patient);
            $rewarded->setDatetime($dateTime);
            $rewarded->setReward($this->reward);

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($rewarded);
            $entityManager->flush();
        }
    }
}
