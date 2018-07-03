<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BodyWeightRepository")
 */
class BodyWeight
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $measurement;

    /**
     * @ORM\Column(type="string", length=10, columnDefinition="enum('fg', 'pg', 'ng', 'ug', 'mg', 'g', 'kg', 'Metric Ton', 'gr', 'oz', 'lb', 'Ton')")
     */
    private $unit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_time;

    /**
     * @ORM\Column(type="string", length=9, columnDefinition="enum('morning', 'afternoon', 'evening', 'night')")
     */
    private $part_of_day;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="bodyWeights")
     */
    private $patient;

    public function __construct()
    {
        $this->patient = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMeasurement(): ?float
    {
        return $this->measurement;
    }

    public function setMeasurement(float $measurement): self
    {
        $this->measurement = $measurement;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
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

    public function getPartOfDay(): ?string
    {
        return $this->part_of_day;
    }

    public function setPartOfDay(string $part_of_day): self
    {
        $this->part_of_day = $part_of_day;

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
}
