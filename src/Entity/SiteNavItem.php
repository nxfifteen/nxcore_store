<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteNavItemRepository")
 */
class SiteNavItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $divider;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $displayOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $badgeVariant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $badgeText;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $childOf;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accessLevel;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $inDevelopment;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $requireService = [];

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getDivider(): ?bool
    {
        return $this->divider;
    }

    public function setDivider(?bool $divider): self
    {
        $this->divider = $divider;

        return $this;
    }

    public function getTitle(): ?bool
    {
        return $this->title;
    }

    public function setTitle(?bool $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): self
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    public function getBadgeVariant(): ?string
    {
        return $this->badgeVariant;
    }

    public function setBadgeVariant(?string $badgeVariant): self
    {
        $this->badgeVariant = $badgeVariant;

        return $this;
    }

    public function getBadgeText(): ?string
    {
        return $this->badgeText;
    }

    public function setBadgeText(?string $badgeText): self
    {
        $this->badgeText = $badgeText;

        return $this;
    }

    public function getChildOf(): ?int
    {
        return $this->childOf;
    }

    public function setChildOf(int $childOf): self
    {
        $this->childOf = $childOf;

        return $this;
    }

    public function getAccessLevel(): ?string
    {
        return $this->accessLevel;
    }

    public function setAccessLevel(?string $accessLevel): self
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }

    public function getInDevelopment(): ?bool
    {
        return $this->inDevelopment;
    }

    public function setInDevelopment(bool $inDevelopment): self
    {
        $this->inDevelopment = $inDevelopment;

        return $this;
    }

    public function getRequireService(): ?array
    {
        return $this->requireService;
    }

    public function setRequireService(?array $requireService): self
    {
        $this->requireService = $requireService;

        return $this;
    }
}
