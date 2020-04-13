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

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RpgChallengeGlobalRepository")
 */
class RpgChallengeGlobal
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descripton;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RpgChallengeGlobal")
     */
    private $childOf;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\RpgChallengeGlobal", mappedBy="childOf", cascade={"persist", "remove"})
     */
    private $children;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $criteria;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $target;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $progression;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\RpgRewards", cascade={"persist", "remove"})
     */
    private $reward;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $xp;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UnitOfMeasurement")
     */
    private $unitOfMeasurement;

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
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool|null $active
     *
     * @return $this
     */
    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return $this|null
     */
    public function getChildOf(): ?self
    {
        return $this->childOf;
    }

    /**
     * @param RpgChallengeGlobal|null $childOf
     *
     * @return $this
     */
    public function setChildOf(?self $childOf): self
    {
        $this->childOf = $childOf;

        return $this;
    }

    /**
     * @return $this|null
     */
    public function getChildren(): ?self
    {
        return $this->children;
    }

    /**
     * @param RpgChallengeGlobal|null $children
     *
     * @return $this
     */
    public function setChildren(?self $children): self
    {
        $this->children = $children;

        // set (or unset) the owning side of the relation if necessary
        $newChildOf = $children === null ? null : $this;
        if ($newChildOf !== $children->getChildOf()) {
            $children->setChildOf($newChildOf);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteria(): ?string
    {
        return $this->criteria;
    }

    /**
     * @param string|null $criteria
     *
     * @return $this
     */
    public function setCriteria(?string $criteria): self
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescripton(): ?string
    {
        return $this->descripton;
    }

    /**
     * @param string|null $descripton
     *
     * @return $this
     */
    public function setDescripton(?string $descripton): self
    {
        $this->descripton = $descripton;

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
    public function getProgression(): ?string
    {
        return $this->progression;
    }

    /**
     * @param string|null $progression
     *
     * @return $this
     */
    public function setProgression(?string $progression): self
    {
        $this->progression = $progression;

        return $this;
    }

    /**
     * @return RpgRewards|null
     */
    public function getReward(): ?RpgRewards
    {
        return $this->reward;
    }

    /**
     * @param RpgRewards|null $reward
     *
     * @return $this
     */
    public function setReward(?RpgRewards $reward): self
    {
        $this->reward = $reward;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTarget(): ?float
    {
        return $this->target;
    }

    /**
     * @param float|null $target
     *
     * @return $this
     */
    public function setTarget(?float $target): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return UnitOfMeasurement|null
     */
    public function getUnitOfMeasurement(): ?UnitOfMeasurement
    {
        return $this->unitOfMeasurement;
    }

    /**
     * @param UnitOfMeasurement|null $unitOfMeasurement
     *
     * @return $this
     */
    public function setUnitOfMeasurement(?UnitOfMeasurement $unitOfMeasurement): self
    {
        $this->unitOfMeasurement = $unitOfMeasurement;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getXp(): ?float
    {
        return $this->xp;
    }

    /**
     * @param float|null $xp
     *
     * @return $this
     */
    public function setXp(?float $xp): self
    {
        $this->xp = $xp;

        return $this;
    }
}
