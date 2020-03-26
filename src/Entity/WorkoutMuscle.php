<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkoutMuscleRepository")
 */
class WorkoutMuscle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFront;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WorkoutExercise", mappedBy="muscle")
     */
    private $exercises;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WorkoutExercise", mappedBy="musclePrimary")
     */
    private $exercisesPrimary;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WorkoutExercise", mappedBy="muscleSecondary")
     */
    private $exercisesSecondary;

    public function __construct()
    {
        $this->exercises = new ArrayCollection();
        $this->exercisesPrimary = new ArrayCollection();
        $this->exercisesSecondary = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsFront(): ?bool
    {
        return $this->isFront;
    }

    public function setIsFront(bool $isFront): self
    {
        $this->isFront = $isFront;

        return $this;
    }

    /**
     * @return Collection|WorkoutExercise[]
     */
    public function getExercises(): Collection
    {
        return $this->exercises;
    }

    public function addExercise(WorkoutExercise $exercise): self
    {
        if (!$this->exercises->contains($exercise)) {
            $this->exercises[] = $exercise;
            $exercise->setMuscle($this);
        }

        return $this;
    }

    public function removeExercise(WorkoutExercise $exercise): self
    {
        if ($this->exercises->contains($exercise)) {
            $this->exercises->removeElement($exercise);
            // set the owning side to null (unless already changed)
            if ($exercise->getMuscle() === $this) {
                $exercise->setMuscle(NULL);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WorkoutExercise[]
     */
    public function getExercisesPrimary(): Collection
    {
        return $this->exercisesPrimary;
    }

    public function addExercisesPrimary(WorkoutExercise $exercisesPrimary): self
    {
        if (!$this->exercisesPrimary->contains($exercisesPrimary)) {
            $this->exercisesPrimary[] = $exercisesPrimary;
            $exercisesPrimary->setMusclePrimary($this);
        }

        return $this;
    }

    public function removeExercisesPrimary(WorkoutExercise $exercisesPrimary): self
    {
        if ($this->exercisesPrimary->contains($exercisesPrimary)) {
            $this->exercisesPrimary->removeElement($exercisesPrimary);
            // set the owning side to null (unless already changed)
            if ($exercisesPrimary->getMusclePrimary() === $this) {
                $exercisesPrimary->setMusclePrimary(NULL);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WorkoutExercise[]
     */
    public function getExercisesSecondary(): Collection
    {
        return $this->exercisesSecondary;
    }

    public function addExercisesSecondary(WorkoutExercise $exercisesSecondary): self
    {
        if (!$this->exercisesSecondary->contains($exercisesSecondary)) {
            $this->exercisesSecondary[] = $exercisesSecondary;
            $exercisesSecondary->setMuscleSecondary($this);
        }

        return $this;
    }

    public function removeExercisesSecondary(WorkoutExercise $exercisesSecondary): self
    {
        if ($this->exercisesSecondary->contains($exercisesSecondary)) {
            $this->exercisesSecondary->removeElement($exercisesSecondary);
            // set the owning side to null (unless already changed)
            if ($exercisesSecondary->getMuscleSecondary() === $this) {
                $exercisesSecondary->setMuscleSecondary(NULL);
            }
        }

        return $this;
    }
}
