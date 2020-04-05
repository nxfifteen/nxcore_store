<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RpgRewardsRepository")
 */
class RpgRewards
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
    private $text;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textLong;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RpgIndicator", inversedBy="rewards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $indicator;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $payload;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getTextLong(): ?string
    {
        return $this->textLong;
    }

    public function setTextLong(?string $textLong): self
    {
        $this->textLong = $textLong;

        return $this;
    }

    public function getIndicator(): ?RpgIndicator
    {
        return $this->indicator;
    }

    public function setIndicator(?RpgIndicator $indicator): self
    {
        $this->indicator = $indicator;

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

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): self
    {
        $this->payload = $payload;

        return $this;
    }
}
