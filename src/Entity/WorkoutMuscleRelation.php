<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkoutMuscleRelationRepository")
 */
class WorkoutMuscleRelation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WorkoutMuscle")
     * @ORM\JoinColumn(nullable=false)
     */
    private $muscle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WorkoutExercise", inversedBy="muscles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $exercise;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPrimary;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMuscle(): ?WorkoutMuscle
    {
        return $this->muscle;
    }

    public function setMuscle(?WorkoutMuscle $muscle): self
    {
        $this->muscle = $muscle;

        return $this;
    }

    public function getExercise(): ?WorkoutExercise
    {
        return $this->exercise;
    }

    public function setExercise(?WorkoutExercise $exercise): self
    {
        $this->exercise = $exercise;

        return $this;
    }

    public function getIsPrimary(): ?bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }
}
