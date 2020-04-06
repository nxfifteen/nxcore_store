<?php
/**
 * DONE This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RpgChallengeGlobalRepository")
 */
class RpgChallengeGlobal
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

    public function getDescripton(): ?string
    {
        return $this->descripton;
    }

    public function setDescripton(?string $descripton): self
    {
        $this->descripton = $descripton;

        return $this;
    }

    public function getChildren(): ?self
    {
        return $this->children;
    }

    public function setChildren(?self $children): self
    {
        $this->children = $children;

        // set (or unset) the owning side of the relation if necessary
        $newChildOf = $children === NULL ? NULL : $this;
        if ($newChildOf !== $children->getChildOf()) {
            $children->setChildOf($newChildOf);
        }

        return $this;
    }

    public function getChildOf(): ?self
    {
        return $this->childOf;
    }

    public function setChildOf(?self $childOf): self
    {
        $this->childOf = $childOf;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCriteria(): ?string
    {
        return $this->criteria;
    }

    public function setCriteria(?string $criteria): self
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function getTarget(): ?float
    {
        return $this->target;
    }

    public function setTarget(?float $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getProgression(): ?string
    {
        return $this->progression;
    }

    public function setProgression(?string $progression): self
    {
        $this->progression = $progression;

        return $this;
    }

    public function getReward(): ?RpgRewards
    {
        return $this->reward;
    }

    public function setReward(?RpgRewards $reward): self
    {
        $this->reward = $reward;

        return $this;
    }

    public function getXp(): ?float
    {
        return $this->xp;
    }

    public function setXp(?float $xp): self
    {
        $this->xp = $xp;

        return $this;
    }

    public function getUnitOfMeasurement(): ?UnitOfMeasurement
    {
        return $this->unitOfMeasurement;
    }

    public function setUnitOfMeasurement(?UnitOfMeasurement $unitOfMeasurement): self
    {
        $this->unitOfMeasurement = $unitOfMeasurement;

        return $this;
    }
}
