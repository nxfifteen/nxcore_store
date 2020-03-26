<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UploadedFileRepository")
 */
class UploadedFile
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
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ContributionLicense")
     * @ORM\JoinColumn(nullable=false)
     */
    private $license;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WorkoutExercise", inversedBy="images")
     */
    private $workoutExercise;

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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getLicense(): ?ContributionLicense
    {
        return $this->license;
    }

    public function setLicense(?ContributionLicense $license): self
    {
        $this->license = $license;

        return $this;
    }

    public function getWorkoutExercise(): ?WorkoutExercise
    {
        return $this->workoutExercise;
    }

    public function setWorkoutExercise(?WorkoutExercise $workoutExercise): self
    {
        $this->workoutExercise = $workoutExercise;

        return $this;
    }
}
