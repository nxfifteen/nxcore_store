<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NutritionInformationRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DateReading", columns={"patient_id","date_time","period","meal"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "date_time": "exact", "patient": "exact", "meal": "exact"})
 */
class NutritionInformation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private $date_time;

    /**
     * @ORM\Column(type="string", length=4, nullable=false)
     */
    private $period;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $meal;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    private $calories;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    private $goal;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $carbs;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $fat;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $fiber;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $protein;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $sodium;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    private $water;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="nutritionInformation")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id", nullable=false)
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UnitOfMeasurement")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id")
     */
    private $unitOfMeasurement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->date_time;
    }

    public function setDateTime(\DateTimeInterface $date_time): self
    {
        $this->date_time = $date_time;

        return $this;
    }

    public function getPeriod(): ?string
    {
        return $this->period;
    }

    public function setPeriod(string $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getMeal(): ?string
    {
        return $this->meal;
    }

    public function setMeal(?string $meal): self
    {
        $this->meal = $meal;

        return $this;
    }

    public function getCalories(): ?int
    {
        return $this->calories;
    }

    public function setCalories(?int $calories): self
    {
        $this->calories = $calories;

        return $this;
    }

    public function getCarbs(): ?int
    {
        return $this->carbs;
    }

    public function setCarbs(?int $carbs): self
    {
        $this->carbs = $carbs;

        return $this;
    }

    public function getFat(): ?int
    {
        return $this->fat;
    }

    public function setFat(?int $fat): self
    {
        $this->fat = $fat;

        return $this;
    }

    public function getFiber(): ?int
    {
        return $this->fiber;
    }

    public function setFiber(?int $fiber): self
    {
        $this->fiber = $fiber;

        return $this;
    }

    public function getProtein(): ?int
    {
        return $this->protein;
    }

    public function setProtein(?int $protein): self
    {
        $this->protein = $protein;

        return $this;
    }

    public function getSodium(): ?int
    {
        return $this->sodium;
    }

    public function setSodium(?int $sodium): self
    {
        $this->sodium = $sodium;

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

    public function getUnitOfMeasurement(): ?UnitOfMeasurement
    {
        return $this->unitOfMeasurement;
    }

    public function setUnitOfMeasurement(?UnitOfMeasurement $unitOfMeasurement): self
    {
        $this->unitOfMeasurement = $unitOfMeasurement;

        return $this;
    }

    public function getGoal(): ?int
    {
        return $this->goal;
    }

    public function setGoal(?int $goal): self
    {
        $this->goal = $goal;

        return $this;
    }

    public function getWater(): ?int
    {
        return $this->water;
    }

    public function setWater(?int $water): self
    {
        $this->water = $water;

        return $this;
    }
}