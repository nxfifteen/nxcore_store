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

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BodyCompositionRepository")
 */
class BodyComposition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $skeletalMuscle;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $muscleMass;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $basalMetabolicRate;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $skeletalMuscleMass;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalBodyWater;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $RemoteId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PartOfDay")
     * @ORM\JoinColumn(nullable=false)
     */
    private $partOfDay;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrackingDevice")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trackingDevice;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSkeletalMuscle(): ?float
    {
        return $this->skeletalMuscle;
    }

    public function setSkeletalMuscle(?float $skeletalMuscle): self
    {
        $this->skeletalMuscle = $skeletalMuscle;

        return $this;
    }

    public function getMuscleMass(): ?float
    {
        return $this->muscleMass;
    }

    public function setMuscleMass(?float $muscleMass): self
    {
        $this->muscleMass = $muscleMass;

        return $this;
    }

    public function getBasalMetabolicRate(): ?float
    {
        return $this->basalMetabolicRate;
    }

    public function setBasalMetabolicRate(?float $basalMetabolicRate): self
    {
        $this->basalMetabolicRate = $basalMetabolicRate;

        return $this;
    }

    public function getSkeletalMuscleMass(): ?float
    {
        return $this->skeletalMuscleMass;
    }

    public function setSkeletalMuscleMass(?float $skeletalMuscleMass): self
    {
        $this->skeletalMuscleMass = $skeletalMuscleMass;

        return $this;
    }

    public function getTotalBodyWater(): ?float
    {
        return $this->totalBodyWater;
    }

    public function setTotalBodyWater(?float $totalBodyWater): self
    {
        $this->totalBodyWater = $totalBodyWater;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    public function getRemoteId(): ?string
    {
        return $this->RemoteId;
    }

    public function setRemoteId(string $RemoteId): self
    {
        $this->RemoteId = $RemoteId;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->DateTime;
    }

    public function setDateTime(\DateTimeInterface $DateTime): self
    {
        $this->DateTime = $DateTime;

        return $this;
    }

    public function getPartOfDay(): ?PartOfDay
    {
        return $this->partOfDay;
    }

    public function setPartOfDay(?PartOfDay $partOfDay): self
    {
        $this->partOfDay = $partOfDay;

        return $this;
    }

    public function getTrackingDevice(): ?TrackingDevice
    {
        return $this->trackingDevice;
    }

    public function setTrackingDevice(?TrackingDevice $trackingDevice): self
    {
        $this->trackingDevice = $trackingDevice;

        return $this;
    }
}
