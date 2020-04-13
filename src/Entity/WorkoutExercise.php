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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkoutExerciseRepository")
 */
class WorkoutExercise
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

    /**
     * WorkoutExercise constructor.
     */
    public function __construct()
    {
        $this->muscles = new ArrayCollection();
        $this->uploads = new ArrayCollection();
        $this->category = new ArrayCollection();
    }

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
     * @return $this
     */
    public function createGuid()
    {
        if (is_null($this->guid)) {
            try {
                $this->guid = Uuid::uuid4();
            } catch (Exception $e) {
            }
        }

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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return WorkoutEquipment|null
     */
    public function getEquipment(): ?WorkoutEquipment
    {
        return $this->equipment;
    }

    /**
     * @param WorkoutEquipment|null $equipment
     *
     * @return $this
     */
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

    /**
     * @param WorkoutMuscleRelation $muscle
     *
     * @return $this
     */
    public function addMuscle(WorkoutMuscleRelation $muscle): self
    {
        if (!$this->muscles->contains($muscle)) {
            $this->muscles[] = $muscle;
            $muscle->setExercise($this);
        }

        return $this;
    }

    /**
     * @param WorkoutMuscleRelation $muscle
     *
     * @return $this
     */
    public function removeMuscle(WorkoutMuscleRelation $muscle): self
    {
        if ($this->muscles->contains($muscle)) {
            $this->muscles->removeElement($muscle);
            // set the owning side to null (unless already changed)
            if ($muscle->getExercise() === $this) {
                $muscle->setExercise(null);
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

    /**
     * @param UploadedFile $upload
     *
     * @return $this
     */
    public function addUpload(UploadedFile $upload): self
    {
        if (!$this->uploads->contains($upload)) {
            $this->uploads[] = $upload;
            $upload->setExercise($this);
        }

        return $this;
    }

    /**
     * @param UploadedFile $upload
     *
     * @return $this
     */
    public function removeUpload(UploadedFile $upload): self
    {
        if ($this->uploads->contains($upload)) {
            $this->uploads->removeElement($upload);
            // set the owning side to null (unless already changed)
            if ($upload->getExercise() === $this) {
                $upload->setExercise(null);
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

    /**
     * @param WorkoutCategories $category
     *
     * @return $this
     */
    public function addCategory(WorkoutCategories $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    /**
     * @param WorkoutCategories $category
     *
     * @return $this
     */
    public function removeCategory(WorkoutCategories $category): self
    {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
        }

        return $this;
    }

    /**
     * @return ContributionLicense|null
     */
    public function getLicense(): ?ContributionLicense
    {
        return $this->license;
    }

    /**
     * @param ContributionLicense|null $license
     *
     * @return $this
     */
    public function setLicense(?ContributionLicense $license): self
    {
        $this->license = $license;

        return $this;
    }

}
