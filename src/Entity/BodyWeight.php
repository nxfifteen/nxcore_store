<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BodyWeightRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DateReading", columns={"date_time","patient_id"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "date_time": "exact", "patient": "exact"})
 */
class BodyWeight
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $measurement;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="bodyWeights")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UnitOfMeasurement")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id")
     */
    private $unitOfMeasurement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PartOfDay")
     * @ORM\JoinColumn(name="part_of_day", referencedColumnName="id")
     */
    private $partOfDay;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMeasurement(): ?float
    {
        return $this->measurement;
    }

    public function setMeasurement(?float $measurement): self
    {
        $this->measurement = $measurement;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->date_time;
    }

    public function setDateTime(?\DateTimeInterface $date_time): self
    {
        $this->date_time = $date_time;

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

    public function getPartOfDay(): ?PartOfDay
    {
        return $this->partOfDay;
    }

    public function setPartOfDay(?PartOfDay $partOfDay): self
    {
        $this->partOfDay = $partOfDay;

        return $this;
    }
}