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

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BodyCompositionRepository")
 */
class BodyComposition
{
    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * The internal primary identity key.
     *
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $guid;

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

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the internal primary identity key.
     *
     * @return UuidInterface|null
     */
    public function getGuid(): ?UuidInterface
    {
        if(is_null($this->guid)) {
            try {
                $this->guid = Uuid::uuid4();
            } catch (\Exception $e) {
            }
        }
        return $this->guid;
    }

    /**
     * @return float|null
     */
    public function getSkeletalMuscle(): ?float
    {
        return $this->skeletalMuscle;
    }

    /**
     * @param float|null $skeletalMuscle
     *
     * @return $this
     */
    public function setSkeletalMuscle(?float $skeletalMuscle): self
    {
        $this->skeletalMuscle = $skeletalMuscle;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getMuscleMass(): ?float
    {
        return $this->muscleMass;
    }

    /**
     * @param float|null $muscleMass
     *
     * @return $this
     */
    public function setMuscleMass(?float $muscleMass): self
    {
        $this->muscleMass = $muscleMass;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getBasalMetabolicRate(): ?float
    {
        return $this->basalMetabolicRate;
    }

    /**
     * @param float|null $basalMetabolicRate
     *
     * @return $this
     */
    public function setBasalMetabolicRate(?float $basalMetabolicRate): self
    {
        $this->basalMetabolicRate = $basalMetabolicRate;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getSkeletalMuscleMass(): ?float
    {
        return $this->skeletalMuscleMass;
    }

    /**
     * @param float|null $skeletalMuscleMass
     *
     * @return $this
     */
    public function setSkeletalMuscleMass(?float $skeletalMuscleMass): self
    {
        $this->skeletalMuscleMass = $skeletalMuscleMass;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalBodyWater(): ?float
    {
        return $this->totalBodyWater;
    }

    /**
     * @param float|null $totalBodyWater
     *
     * @return $this
     */
    public function setTotalBodyWater(?float $totalBodyWater): self
    {
        $this->totalBodyWater = $totalBodyWater;

        return $this;
    }

    /**
     * @return Patient|null
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient|null $patient
     *
     * @return $this
     */
    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRemoteId(): ?string
    {
        return $this->RemoteId;
    }

    /**
     * @param string $RemoteId
     *
     * @return $this
     */
    public function setRemoteId(string $RemoteId): self
    {
        $this->RemoteId = $RemoteId;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateTime(): ?DateTimeInterface
    {
        return $this->DateTime;
    }

    /**
     * @param DateTimeInterface $DateTime
     *
     * @return $this
     */
    public function setDateTime(DateTimeInterface $DateTime): self
    {
        $this->DateTime = $DateTime;

        return $this;
    }

    /**
     * @return PartOfDay|null
     */
    public function getPartOfDay(): ?PartOfDay
    {
        return $this->partOfDay;
    }

    /**
     * @param PartOfDay|null $partOfDay
     *
     * @return $this
     */
    public function setPartOfDay(?PartOfDay $partOfDay): self
    {
        $this->partOfDay = $partOfDay;

        return $this;
    }

    /**
     * @return TrackingDevice|null
     */
    public function getTrackingDevice(): ?TrackingDevice
    {
        return $this->trackingDevice;
    }

    /**
     * @param TrackingDevice|null $trackingDevice
     *
     * @return $this
     */
    public function setTrackingDevice(?TrackingDevice $trackingDevice): self
    {
        $this->trackingDevice = $trackingDevice;

        return $this;
    }
}
