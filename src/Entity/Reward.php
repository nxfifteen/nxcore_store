<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RewardRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="Reward", columns={"service","remote_id"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "remote_id": "exact", "name": "exact", "value": "exact", "type": "exact", "category": "exact", "thirdPartyService": "exact"})
 */
class Reward
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $remote_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", length=6, nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $category;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionFull;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $gradientEndColor;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $gradientStartColor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ThirdPartyService")
     * @ORM\JoinColumn(name="service", referencedColumnName="id")
     */
    private $thirdPartyService;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemoteId(): ?string
    {
        return $this->remote_id;
    }

    public function setRemoteId(?string $remote_id): self
    {
        $this->remote_id = $remote_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

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

    public function getDescriptionFull(): ?string
    {
        return $this->descriptionFull;
    }

    public function setDescriptionFull(?string $descriptionFull): self
    {
        $this->descriptionFull = $descriptionFull;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getGradientEndColor(): ?string
    {
        return $this->gradientEndColor;
    }

    public function setGradientEndColor(?string $gradientEndColor): self
    {
        $this->gradientEndColor = $gradientEndColor;

        return $this;
    }

    public function getGradientStartColor(): ?string
    {
        return $this->gradientStartColor;
    }

    public function setGradientStartColor(?string $gradientStartColor): self
    {
        $this->gradientStartColor = $gradientStartColor;

        return $this;
    }

    public function getThirdPartyService(): ?ThirdPartyService
    {
        return $this->thirdPartyService;
    }

    public function setThirdPartyService(?ThirdPartyService $thirdPartyService): self
    {
        $this->thirdPartyService = $thirdPartyService;

        return $this;
    }
}