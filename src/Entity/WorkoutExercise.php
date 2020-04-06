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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkoutExerciseRepository")
 */
class WorkoutExercise
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WorkoutEquipment", inversedBy="exercises")
     */
    private $equipment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WorkoutMuscleRelation", mappedBy="exercise", orphanRemoval=true)
     */
    private $muscles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UploadedFile", mappedBy="exercise")
     */
    private $uploads;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\WorkoutCategories", inversedBy="exercises")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ContributionLicense")
     */
    private $license;

    public function __construct()
    {
        $this->muscles = new ArrayCollection();
        $this->uploads = new ArrayCollection();
        $this->category = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEquipment(): ?WorkoutEquipment
    {
        return $this->equipment;
    }

    public function setEquipment(?WorkoutEquipment $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }

    /**
     * @return Collection|WorkoutMuscleRelation[]
     */
    public function getMuscles(): Collection
    {
        return $this->muscles;
    }

    public function addMuscle(WorkoutMuscleRelation $muscle): self
    {
        if (!$this->muscles->contains($muscle)) {
            $this->muscles[] = $muscle;
            $muscle->setExercise($this);
        }

        return $this;
    }

    public function removeMuscle(WorkoutMuscleRelation $muscle): self
    {
        if ($this->muscles->contains($muscle)) {
            $this->muscles->removeElement($muscle);
            // set the owning side to null (unless already changed)
            if ($muscle->getExercise() === $this) {
                $muscle->setExercise(NULL);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UploadedFile[]
     */
    public function getUploads(): Collection
    {
        return $this->uploads;
    }

    public function addUpload(UploadedFile $upload): self
    {
        if (!$this->uploads->contains($upload)) {
            $this->uploads[] = $upload;
            $upload->setExercise($this);
        }

        return $this;
    }

    public function removeUpload(UploadedFile $upload): self
    {
        if ($this->uploads->contains($upload)) {
            $this->uploads->removeElement($upload);
            // set the owning side to null (unless already changed)
            if ($upload->getExercise() === $this) {
                $upload->setExercise(NULL);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WorkoutCategories[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(WorkoutCategories $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(WorkoutCategories $category): self
    {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
        }

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

}
