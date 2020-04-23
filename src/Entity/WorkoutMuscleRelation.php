<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nxcore/
 * @link      https://gitlab.com/nx-core/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

/** @noinspection DuplicatedCode */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkoutMuscleRelationRepository")
 */
class WorkoutMuscleRelation
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
     * @return WorkoutExercise|null
     */
    public function getExercise(): ?WorkoutExercise
    {
        return $this->exercise;
    }

    public function setExercise(?WorkoutExercise $exercise): self
    {
        $this->exercise = $exercise;

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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMuscle(): ?WorkoutMuscle
    {
        return $this->muscle;
    }

    public function setMuscle(?WorkoutMuscle $muscle): self
    {
        $this->muscle = $muscle;

        return $this;
    }


}
