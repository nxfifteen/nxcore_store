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
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseRepository")
 */
class Exercise
{
    /**
     * The internal primary identity key.
     *
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $guid;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTimeStart;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTimeEnd;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrackingDevice")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trackingDevice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $RemoteId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PartOfDay")
     * @ORM\JoinColumn(nullable=false)
     */
    private $partOfDay;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ExerciseSummary", mappedBy="exercise", cascade={"persist", "remove"})
     */
    private $exerciseSummary;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ExerciseType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $exerciseType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $steps;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $liveDataBlob;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $locationDataBlob;

    /**
     * Exercise constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get the internal primary identity key.
     *
     * @param bool $force
     *
     * @return $this
     */
    public function createGuid(bool $force = false)
    {
        if ($force || is_null($this->guid)) {
            try {
                $this->guid = Uuid::uuid1();
            } catch (Exception $e) {
            }
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateTimeEnd(): ?DateTimeInterface
    {
        return $this->dateTimeEnd;
    }

    /**
     * @param DateTimeInterface $dateTimeEnd
     *
     * @return $this
     */
    public function setDateTimeEnd(DateTimeInterface $dateTimeEnd): self
    {
        $this->dateTimeEnd = $dateTimeEnd;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateTimeStart(): ?DateTimeInterface
    {
        return $this->dateTimeStart;
    }

    /**
     * @param DateTimeInterface $dateTimeStart
     *
     * @return $this
     */
    public function setDateTimeStart(DateTimeInterface $dateTimeStart): self
    {
        $this->dateTimeStart = $dateTimeStart;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @return $this
     */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return ExerciseSummary|null
     */
    public function getExerciseSummary(): ?ExerciseSummary
    {
        return $this->exerciseSummary;
    }

    /**
     * @param ExerciseSummary $exerciseSummary
     *
     * @return $this
     */
    public function setExerciseSummary(ExerciseSummary $exerciseSummary): self
    {
        $this->exerciseSummary = $exerciseSummary;

        // set the owning side of the relation if necessary
        if ($this !== $exerciseSummary->getExercise()) {
            $exerciseSummary->setExercise($this);
        }

        return $this;
    }

    /**
     * @return ExerciseType|null
     */
    public function getExerciseType(): ?ExerciseType
    {
        return $this->exerciseType;
    }

    /**
     * @param ExerciseType|null $exerciseType
     *
     * @return $this
     */
    public function setExerciseType(?ExerciseType $exerciseType): self
    {
        $this->exerciseType = $exerciseType;

        return $this;
    }

    /**
     * Get the internal primary identity key.
     *
     * @return UuidInterface|null
     */
    public function getGuid(): ?UuidInterface
    {
        return $this->guid;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return false|string|null
     */
    public function getLiveDataBlob()
    {
        if (!is_null($this->liveDataBlob)) {
            return stream_get_contents($this->liveDataBlob);
        } else {
            return null;
        }
    }

    /**
     * @param $liveDataBlob
     *
     * @return $this
     */
    public function setLiveDataBlob($liveDataBlob): self
    {
        $this->liveDataBlob = $liveDataBlob;

        return $this;
    }

    /**
     * @return false|string|null
     */
    public function getLocationDataBlob()
    {
        if (!is_null($this->locationDataBlob)) {
            return stream_get_contents($this->locationDataBlob);
        } else {
            return null;
        }
    }

    /**
     * @param $locationDataBlob
     *
     * @return $this
     */
    public function setLocationDataBlob($locationDataBlob): self
    {
        $this->locationDataBlob = $locationDataBlob;

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
     * @param string|null $RemoteId
     *
     * @return $this
     */
    public function setRemoteId(?string $RemoteId): self
    {
        $this->RemoteId = $RemoteId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSteps(): ?int
    {
        return $this->steps;
    }

    /**
     * @param int|null $steps
     *
     * @return $this
     */
    public function setSteps(?int $steps): self
    {
        $this->steps = $steps;

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
