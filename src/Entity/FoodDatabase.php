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
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DeviceRemote", columns={"provider_id","service_id"})})
 *
 * @ORM\Entity(repositoryClass="App\Repository\FoodDatabaseRepository")
 */
class FoodDatabase
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
    private $providerId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $calorie;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $servingAmount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UnitOfMeasurement")
     * @ORM\JoinColumn(nullable=true)
     */
    private $servingUnit;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalFat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $saturatedFat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $carbohydrate;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $dietaryFiber;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sugar;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $protein;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $servingDescription;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $servingNumberDefault;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ThirdPartyService")
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $remoteIds = [];

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
    public function getProviderId(): ?string
    {
        return $this->providerId;
    }

    /**
     * @param string $providerId
     *
     * @return $this
     */
    public function setProviderId(string $providerId): self
    {
        $this->providerId = $providerId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCalorie(): ?float
    {
        return $this->calorie;
    }

    /**
     * @param float|null $calorie
     *
     * @return $this
     */
    public function setCalorie(?float $calorie): self
    {
        $this->calorie = $calorie;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getServingAmount(): ?float
    {
        return $this->servingAmount;
    }

    /**
     * @param float|null $servingAmount
     *
     * @return $this
     */
    public function setServingAmount(?float $servingAmount): self
    {
        $this->servingAmount = $servingAmount;

        return $this;
    }

    /**
     * @return UnitOfMeasurement|null
     */
    public function getServingUnit(): ?UnitOfMeasurement
    {
        return $this->servingUnit;
    }

    /**
     * @param UnitOfMeasurement|null $servingUnit
     *
     * @return $this
     */
    public function setServingUnit(?UnitOfMeasurement $servingUnit): self
    {
        $this->servingUnit = $servingUnit;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalFat(): ?float
    {
        return $this->totalFat;
    }

    /**
     * @param float|null $totalFat
     *
     * @return $this
     */
    public function setTotalFat(?float $totalFat): self
    {
        $this->totalFat = $totalFat;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getSaturatedFat(): ?float
    {
        return $this->saturatedFat;
    }

    /**
     * @param float|null $saturatedFat
     *
     * @return $this
     */
    public function setSaturatedFat(?float $saturatedFat): self
    {
        $this->saturatedFat = $saturatedFat;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCarbohydrate(): ?float
    {
        return $this->carbohydrate;
    }

    /**
     * @param float|null $carbohydrate
     *
     * @return $this
     */
    public function setCarbohydrate(?float $carbohydrate): self
    {
        $this->carbohydrate = $carbohydrate;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getDietaryFiber(): ?float
    {
        return $this->dietaryFiber;
    }

    /**
     * @param float|null $dietaryFiber
     *
     * @return $this
     */
    public function setDietaryFiber(?float $dietaryFiber): self
    {
        $this->dietaryFiber = $dietaryFiber;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getSugar(): ?float
    {
        return $this->sugar;
    }

    /**
     * @param float|null $sugar
     *
     * @return $this
     */
    public function setSugar(?float $sugar): self
    {
        $this->sugar = $sugar;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getProtein(): ?float
    {
        return $this->protein;
    }

    /**
     * @param float|null $protein
     *
     * @return $this
     */
    public function setProtein(?float $protein): self
    {
        $this->protein = $protein;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getServingDescription(): ?string
    {
        return $this->servingDescription;
    }

    /**
     * @param string $servingDescription
     *
     * @return $this
     */
    public function setServingDescription(string $servingDescription): self
    {
        $this->servingDescription = $servingDescription;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getServingNumberDefault(): ?float
    {
        return $this->servingNumberDefault;
    }

    /**
     * @param float $servingNumberDefault
     *
     * @return $this
     */
    public function setServingNumberDefault(float $servingNumberDefault): self
    {
        $this->servingNumberDefault = $servingNumberDefault;

        return $this;
    }

    /**
     * @return ThirdPartyService|null
     */
    public function getService(): ?ThirdPartyService
    {
        return $this->service;
    }

    /**
     * @param ThirdPartyService|null $service
     *
     * @return $this
     */
    public function setService(?ThirdPartyService $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getRemoteIds(): ?array
    {
        return $this->remoteIds;
    }

    /**
     * @param array|null $remoteIds
     *
     * @return $this
     */
    public function setRemoteIds(?array $remoteIds): self
    {
        $this->remoteIds = $remoteIds;

        return $this;
    }
}
