<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountDailyCaloriesRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DateReading", columns={"patient_id","date_time"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "date_time": "exact", "patient": "exact"})
 */
class CountDailyCalories
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $goal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="calorieCount")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     */
    private $patient;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
    {
        $this->value = $value;

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