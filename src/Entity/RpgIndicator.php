<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RpgIndicatorRepository")
 */
class RpgIndicator
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
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dataSet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $comparator;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RpgRewards", mappedBy="indicator", orphanRemoval=true)
     */
    private $rewards;

    public function __construct()
    {
        $this->rewards = new ArrayCollection();
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDataSet(): ?string
    {
        return $this->dataSet;
    }

    public function setDataSet(string $dataSet): self
    {
        $this->dataSet = $dataSet;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getComparator(): ?string
    {
        return $this->comparator;
    }

    public function setComparator(string $comparator): self
    {
        $this->comparator = $comparator;

        return $this;
    }

    /**
     * @return Collection|RpgRewards[]
     */
    public function getRewards(): Collection
    {
        return $this->rewards;
    }

    public function addReward(RpgRewards $reward): self
    {
        if (!$this->rewards->contains($reward)) {
            $this->rewards[] = $reward;
            $reward->setIndicator($this);
        }

        return $this;
    }

    public function removeReward(RpgRewards $reward): self
    {
        if ($this->rewards->contains($reward)) {
            $this->rewards->removeElement($reward);
            // set the owning side to null (unless already changed)
            if ($reward->getIndicator() === $this) {
                $reward->setIndicator(null);
            }
        }

        return $this;
    }
}
