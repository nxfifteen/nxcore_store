<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\FoodDiaryRepository")
 */
class FoodDiary
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrackingDevice")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trackingDevice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $remoteId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FoodDatabase")
     * @ORM\JoinColumn(nullable=false)
     */
    private $foodItem;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UnitOfMeasurement")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FoodMeals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTrackingDevice(): ?TrackingDevice
    {
        return $this->trackingDevice;
    }

    public function setTrackingDevice(?TrackingDevice $trackingDevice): self
    {
        $this->trackingDevice = $trackingDevice;

        return $this;
    }

    public function getRemoteId(): ?string
    {
        return $this->remoteId;
    }

    public function setRemoteId(string $remoteId): self
    {
        $this->remoteId = $remoteId;

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

    public function getFoodItem(): ?FoodDatabase
    {
        return $this->foodItem;
    }

    public function setFoodItem(?FoodDatabase $foodItem): self
    {
        $this->foodItem = $foodItem;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUnit(): ?UnitOfMeasurement
    {
        return $this->unit;
    }

    public function setUnit(?UnitOfMeasurement $unit): self
    {
        $this->unit = $unit;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
