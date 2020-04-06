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
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DeviceRemote", columns={"provider_id","service_id"})})
 *
 * @ORM\Entity(repositoryClass="App\Repository\FoodDatabaseRepository")
 */
class FoodDatabase
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProviderId(): ?string
    {
        return $this->providerId;
    }

    public function setProviderId(string $providerId): self
    {
        $this->providerId = $providerId;

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

    public function getCalorie(): ?float
    {
        return $this->calorie;
    }

    public function setCalorie(?float $calorie): self
    {
        $this->calorie = $calorie;

        return $this;
    }

    public function getServingAmount(): ?float
    {
        return $this->servingAmount;
    }

    public function setServingAmount(?float $servingAmount): self
    {
        $this->servingAmount = $servingAmount;

        return $this;
    }

    public function getServingUnit(): ?UnitOfMeasurement
    {
        return $this->servingUnit;
    }

    public function setServingUnit(?UnitOfMeasurement $servingUnit): self
    {
        $this->servingUnit = $servingUnit;

        return $this;
    }

    public function getTotalFat(): ?float
    {
        return $this->totalFat;
    }

    public function setTotalFat(?float $totalFat): self
    {
        $this->totalFat = $totalFat;

        return $this;
    }

    public function getSaturatedFat(): ?float
    {
        return $this->saturatedFat;
    }

    public function setSaturatedFat(?float $saturatedFat): self
    {
        $this->saturatedFat = $saturatedFat;

        return $this;
    }

    public function getCarbohydrate(): ?float
    {
        return $this->carbohydrate;
    }

    public function setCarbohydrate(?float $carbohydrate): self
    {
        $this->carbohydrate = $carbohydrate;

        return $this;
    }

    public function getDietaryFiber(): ?float
    {
        return $this->dietaryFiber;
    }

    public function setDietaryFiber(?float $dietaryFiber): self
    {
        $this->dietaryFiber = $dietaryFiber;

        return $this;
    }

    public function getSugar(): ?float
    {
        return $this->sugar;
    }

    public function setSugar(?float $sugar): self
    {
        $this->sugar = $sugar;

        return $this;
    }

    public function getProtein(): ?float
    {
        return $this->protein;
    }

    public function setProtein(?float $protein): self
    {
        $this->protein = $protein;

        return $this;
    }

    public function getServingDescription(): ?string
    {
        return $this->servingDescription;
    }

    public function setServingDescription(string $servingDescription): self
    {
        $this->servingDescription = $servingDescription;

        return $this;
    }

    public function getServingNumberDefault(): ?float
    {
        return $this->servingNumberDefault;
    }

    public function setServingNumberDefault(float $servingNumberDefault): self
    {
        $this->servingNumberDefault = $servingNumberDefault;

        return $this;
    }

    public function getService(): ?ThirdPartyService
    {
        return $this->service;
    }

    public function setService(?ThirdPartyService $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getRemoteIds(): ?array
    {
        return $this->remoteIds;
    }

    public function setRemoteIds(?array $remoteIds): self
    {
        $this->remoteIds = $remoteIds;

        return $this;
    }
}
