<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\RpgMilestonesRepository")
 */
class RpgMilestones
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
    private $category;

    /**
     * @ORM\Column(type="float")
     */
    private $value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $msgLess;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $msgMore;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getMsgLess(): ?string
    {
        return $this->msgLess;
    }

    public function setMsgLess(?string $msgLess): self
    {
        $this->msgLess = $msgLess;

        return $this;
    }

    public function getMsgMore(): ?string
    {
        return $this->msgMore;
    }

    public function setMsgMore(?string $msgMore): self
    {
        $this->msgMore = $msgMore;

        return $this;
    }
}
