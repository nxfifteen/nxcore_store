<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SportTrackRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="ActivityTrack", columns={"total_time","start_time","total_distance","patient_id"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "start_time": "exact", "total_time": "exact", "total_distance": "exact", "method": "exact", "patient": "exact", "trackingDevice": "exact"})
 */
class SportTrack
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $start_time;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $total_time;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $total_distance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $calories;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $intensity;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $method;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SportActivity", mappedBy="sportTrack")
     */
    private $sportActivity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrackingDevice")
     * @ORM\JoinColumn(name="tracking_device", referencedColumnName="id")
     */
    private $trackingDevice;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(?\DateTimeInterface $start_time): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getTotalTime(): ?float
    {
        return $this->total_time;
    }

    public function setTotalTime(?float $total_time): self
    {
        $this->total_time = $total_time;

        return $this;
    }

    public function getTotalDistance(): ?float
    {
        return $this->total_distance;
    }

    public function setTotalDistance(?float $total_distance): self
    {
        $this->total_distance = $total_distance;

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

    public function getIntensity(): ?string
    {
        return $this->intensity;
    }

    public function setIntensity(?string $intensity): self
    {
        $this->intensity = $intensity;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): self
    {
        $this->method = $method;

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

    public function getTrackingDevice(): ?TrackingDevice
    {
        return $this->trackingDevice;
    }

    public function setTrackingDevice(?TrackingDevice $trackingDevice): self
    {
        $this->trackingDevice = $trackingDevice;

        return $this;
    }

    public function getSportActivity(): ?SportActivity
    {
        return $this->sportActivity;
    }

    public function setSportActivity(?SportActivity $sportActivity): self
    {
        $this->sportActivity = $sportActivity;

        // set (or unset) the owning side of the relation if necessary
        $newSportTrack = $sportActivity === null ? null : $this;
        if ($newSportTrack !== $sportActivity->getSportTrack()) {
            $sportActivity->setSportTrack($newSportTrack);
        }

        return $this;
    }
}