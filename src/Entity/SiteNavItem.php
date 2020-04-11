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
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteNavItemRepository")
 */
class SiteNavItem
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
        if(is_null($this->guid)) {
            try {
                $this->guid = Uuid::uuid4();
            } catch (\Exception $e) {
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
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getDivider(): ?bool
    {
        return $this->divider;
    }

    /**
     * @param bool|null $divider
     *
     * @return $this
     */
    public function setDivider(?bool $divider): self
    {
        $this->divider = $divider;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getTitle(): ?bool
    {
        return $this->title;
    }

    /**
     * @param bool|null $title
     *
     * @return $this
     */
    public function setTitle(?bool $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     *
     * @return $this
     */
    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    /**
     * @param int $displayOrder
     *
     * @return $this
     */
    public function setDisplayOrder(int $displayOrder): self
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBadgeVariant(): ?string
    {
        return $this->badgeVariant;
    }

    /**
     * @param string|null $badgeVariant
     *
     * @return $this
     */
    public function setBadgeVariant(?string $badgeVariant): self
    {
        $this->badgeVariant = $badgeVariant;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBadgeText(): ?string
    {
        return $this->badgeText;
    }

    /**
     * @param string|null $badgeText
     *
     * @return $this
     */
    public function setBadgeText(?string $badgeText): self
    {
        $this->badgeText = $badgeText;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getChildOf(): ?int
    {
        return $this->childOf;
    }

    /**
     * @param int $childOf
     *
     * @return $this
     */
    public function setChildOf(int $childOf): self
    {
        $this->childOf = $childOf;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAccessLevel(): ?string
    {
        return $this->accessLevel;
    }

    /**
     * @param string|null $accessLevel
     *
     * @return $this
     */
    public function setAccessLevel(?string $accessLevel): self
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getInDevelopment(): ?bool
    {
        return $this->inDevelopment;
    }

    /**
     * @param bool $inDevelopment
     *
     * @return $this
     */
    public function setInDevelopment(bool $inDevelopment): self
    {
        $this->inDevelopment = $inDevelopment;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getRequireService(): ?array
    {
        return $this->requireService;
    }

    /**
     * @param array|null $requireService
     *
     * @return $this
     */
    public function setRequireService(?array $requireService): self
    {
        $this->requireService = $requireService;

        return $this;
    }
}
