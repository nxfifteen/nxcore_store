<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DeviceRemote", columns={"remote_id","meal_id","patient_id"})})
 *
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\FoodNutritionRepository")
 */
class FoodNutrition
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
    private $RemoteId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FoodMeals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meal;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $calorie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

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
    private $polysaturatedFat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $monosaturatedFat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $transFat;

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
     * @ORM\Column(type="float", nullable=true)
     */
    private $cholesterol;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sodium;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $potassium;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $vitA;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $vitC;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $calcium;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $iron;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrackingDevice")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trackingDevice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $carbohydrate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemoteId(): ?string
    {
        return $this->RemoteId;
    }

    public function setRemoteId(?string $RemoteId): self
    {
        $this->RemoteId = $RemoteId;

        return $this;
    }

    public function getMeal(): ?FoodMeals
    {
        return $this->meal;
    }

    public function setMeal(?FoodMeals $meal): self
    {
        $this->meal = $meal;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    public function getPolysaturatedFat(): ?float
    {
        return $this->polysaturatedFat;
    }

    public function setPolysaturatedFat(?float $polysaturatedFat): self
    {
        $this->polysaturatedFat = $polysaturatedFat;

        return $this;
    }

    public function getMonosaturatedFat(): ?float
    {
        return $this->monosaturatedFat;
    }

    public function setMonosaturatedFat(?float $monosaturatedFat): self
    {
        $this->monosaturatedFat = $monosaturatedFat;

        return $this;
    }

    public function getTransFat(): ?float
    {
        return $this->transFat;
    }

    public function setTransFat(?float $transFat): self
    {
        $this->transFat = $transFat;

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

    public function getCholesterol(): ?float
    {
        return $this->cholesterol;
    }

    public function setCholesterol(?float $cholesterol): self
    {
        $this->cholesterol = $cholesterol;

        return $this;
    }

    public function getSodium(): ?float
    {
        return $this->sodium;
    }

    public function setSodium(?float $sodium): self
    {
        $this->sodium = $sodium;

        return $this;
    }

    public function getPotassium(): ?float
    {
        return $this->potassium;
    }

    public function setPotassium(?float $potassium): self
    {
        $this->potassium = $potassium;

        return $this;
    }

    public function getVitA(): ?float
    {
        return $this->vitA;
    }

    public function setVitA(?float $vitA): self
    {
        $this->vitA = $vitA;

        return $this;
    }

    public function getVitC(): ?float
    {
        return $this->vitC;
    }

    public function setVitC(?float $vitC): self
    {
        $this->vitC = $vitC;

        return $this;
    }

    public function getCalcium(): ?float
    {
        return $this->calcium;
    }

    public function setCalcium(?float $calcium): self
    {
        $this->calcium = $calcium;

        return $this;
    }

    public function getIron(): ?float
    {
        return $this->iron;
    }

    public function setIron(?float $iron): self
    {
        $this->iron = $iron;

        return $this;
    }

    public function getTrackingDevice(): ?TrackingDevice
    {
        return $this->trackingDevice;
    }

    public function setTrackingDevice(?TrackingDevice $trackingDevice): self
    {
        $this->trackingDevice = $trackingDevice;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

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

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->DateTime;
    }

    public function setDateTime(\DateTimeInterface $DateTime): self
    {
        $this->DateTime = $DateTime;

        return $this;
    }
}
