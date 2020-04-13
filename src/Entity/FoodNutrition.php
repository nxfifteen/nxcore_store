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

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DeviceRemote", columns={"remote_id","meal_id","patient_id"})})
 *
 * @ORM\Entity(repositoryClass="App\Repository\FoodNutritionRepository")
 */
class FoodNutrition
{
    /**
     * The internal primary identity key.
     *
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $guid;
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
     * @return float|null
     */
    public function getCalcium(): ?float
    {
        return $this->calcium;
    }

    /**
     * @param float|null $calcium
     *
     * @return $this
     */
    public function setCalcium(?float $calcium): self
    {
        $this->calcium = $calcium;

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
    public function getCholesterol(): ?float
    {
        return $this->cholesterol;
    }

    /**
     * @param float|null $cholesterol
     *
     * @return $this
     */
    public function setCholesterol(?float $cholesterol): self
    {
        $this->cholesterol = $cholesterol;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateTime(): ?DateTimeInterface
    {
        return $this->DateTime;
    }

    /**
     * @param DateTimeInterface $DateTime
     *
     * @return $this
     */
    public function setDateTime(DateTimeInterface $DateTime): self
    {
        $this->DateTime = $DateTime;

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
     * Get the internal primary identity key.
     *
     * @return UuidInterface|null
     */
    public function getGuid(): ?UuidInterface
    {
        return $this->guid;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return float|null
     */
    public function getIron(): ?float
    {
        return $this->iron;
    }

    /**
     * @param float|null $iron
     *
     * @return $this
     */
    public function setIron(?float $iron): self
    {
        $this->iron = $iron;

        return $this;
    }

    /**
     * @return FoodMeals|null
     */
    public function getMeal(): ?FoodMeals
    {
        return $this->meal;
    }

    /**
     * @param FoodMeals|null $meal
     *
     * @return $this
     */
    public function setMeal(?FoodMeals $meal): self
    {
        $this->meal = $meal;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getMonosaturatedFat(): ?float
    {
        return $this->monosaturatedFat;
    }

    /**
     * @param float|null $monosaturatedFat
     *
     * @return $this
     */
    public function setMonosaturatedFat(?float $monosaturatedFat): self
    {
        $this->monosaturatedFat = $monosaturatedFat;

        return $this;
    }

    /**
     * @return Patient|null
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient|null $patient
     *
     * @return $this
     */
    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPolysaturatedFat(): ?float
    {
        return $this->polysaturatedFat;
    }

    /**
     * @param float|null $polysaturatedFat
     *
     * @return $this
     */
    public function setPolysaturatedFat(?float $polysaturatedFat): self
    {
        $this->polysaturatedFat = $polysaturatedFat;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPotassium(): ?float
    {
        return $this->potassium;
    }

    /**
     * @param float|null $potassium
     *
     * @return $this
     */
    public function setPotassium(?float $potassium): self
    {
        $this->potassium = $potassium;

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
    public function getRemoteId(): ?string
    {
        return $this->RemoteId;
    }

    /**
     * @param string|null $RemoteId
     *
     * @return $this
     */
    public function setRemoteId(?string $RemoteId): self
    {
        $this->RemoteId = $RemoteId;

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
    public function getSodium(): ?float
    {
        return $this->sodium;
    }

    /**
     * @param float|null $sodium
     *
     * @return $this
     */
    public function setSodium(?float $sodium): self
    {
        $this->sodium = $sodium;

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
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

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
     * @return TrackingDevice|null
     */
    public function getTrackingDevice(): ?TrackingDevice
    {
        return $this->trackingDevice;
    }

    /**
     * @param TrackingDevice|null $trackingDevice
     *
     * @return $this
     */
    public function setTrackingDevice(?TrackingDevice $trackingDevice): self
    {
        $this->trackingDevice = $trackingDevice;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTransFat(): ?float
    {
        return $this->transFat;
    }

    /**
     * @param float|null $transFat
     *
     * @return $this
     */
    public function setTransFat(?float $transFat): self
    {
        $this->transFat = $transFat;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getVitA(): ?float
    {
        return $this->vitA;
    }

    /**
     * @param float|null $vitA
     *
     * @return $this
     */
    public function setVitA(?float $vitA): self
    {
        $this->vitA = $vitA;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getVitC(): ?float
    {
        return $this->vitC;
    }

    /**
     * @param float|null $vitC
     *
     * @return $this
     */
    public function setVitC(?float $vitC): self
    {
        $this->vitC = $vitC;

        return $this;
    }
}
