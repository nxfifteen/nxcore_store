<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FloorCountDailyRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DateReading", columns={"patient_id","date_time"})})
 */
class FloorCountDaily
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $floor_count;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="floorCount")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     */
    private $patient;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFloorCount(): ?int
    {
        return $this->floor_count;
    }

    public function setFloorCount(?int $floor_count): self
    {
        $this->floor_count = $floor_count;

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
}