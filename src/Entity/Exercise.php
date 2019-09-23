<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseRepository")
 */
class Exercise
{
    /**
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
     * @ORM\OneToMany(targetEntity="App\Entity\ExerciseTrack", mappedBy="exercise", orphanRemoval=true,cascade={"persist"})
     */
    private $exerciseTrack;

    public function __construct()
    {
        $this->exerciseTrack = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateTimeStart(): ?\DateTimeInterface
    {
        return $this->dateTimeStart;
    }

    public function setDateTimeStart(\DateTimeInterface $dateTimeStart): self
    {
        $this->dateTimeStart = $dateTimeStart;

        return $this;
    }

    public function getDateTimeEnd(): ?\DateTimeInterface
    {
        return $this->dateTimeEnd;
    }

    public function setDateTimeEnd(\DateTimeInterface $dateTimeEnd): self
    {
        $this->dateTimeEnd = $dateTimeEnd;

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

    public function getRemoteId(): ?string
    {
        return $this->RemoteId;
    }

    public function setRemoteId(?string $RemoteId): self
    {
        $this->RemoteId = $RemoteId;

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

    public function getExerciseSummary(): ?ExerciseSummary
    {
        return $this->exerciseSummary;
    }

    public function setExerciseSummary(ExerciseSummary $exerciseSummary): self
    {
        $this->exerciseSummary = $exerciseSummary;

        // set the owning side of the relation if necessary
        if ($this !== $exerciseSummary->getExercise()) {
            $exerciseSummary->setExercise($this);
        }

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getExerciseType(): ?ExerciseType
    {
        return $this->exerciseType;
    }

    public function setExerciseType(?ExerciseType $exerciseType): self
    {
        $this->exerciseType = $exerciseType;

        return $this;
    }

    /**
     * @return Collection|ExerciseTrack[]
     */
    public function getExerciseTrack(): Collection
    {
        return $this->exerciseTrack;
    }

    public function addExerciseTrack(ExerciseTrack $exerciseTrack): self
    {
        if (!$this->exerciseTrack->contains($exerciseTrack)) {
            $this->exerciseTrack[] = $exerciseTrack;
            $exerciseTrack->setExercise($this);
        }

        return $this;
    }

    public function removeExerciseTrack(ExerciseTrack $exerciseTrack): self
    {
        if ($this->exerciseTrack->contains($exerciseTrack)) {
            $this->exerciseTrack->removeElement($exerciseTrack);
            // set the owning side to null (unless already changed)
            if ($exerciseTrack->getExercise() === $this) {
                $exerciseTrack->setExercise(null);
            }
        }

        return $this;
    }
}
