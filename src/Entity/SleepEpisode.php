<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SleepEpisodeRepository")
 * @ORM\Table(uniqueConstraints={
 *         @ORM\UniqueConstraint(name="DateRecord", columns={"patient_id","service","start_time"}),
 *         @ORM\UniqueConstraint(name="RemoteId", columns={"patient_id","remote_id","service"})
 *     })
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "remoteId": "exact", "start_time": "exact", "end_time": "exact", "is_main_sleep": "exact", "patient": "exact"})
 */
class SleepEpisode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $remoteId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $start_time;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end_time;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    private $latency_to_sleep_onset;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    private $latency_to_arising;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    private $total_sleep_time;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    private $number_of_awakenings;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_main_sleep;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $efficiency_percentage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SleepLevels", mappedBy="sleepEpisode")
     */
    private $sleepLevels;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="sleepEpisode")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UnitOfMeasurement")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id")
     */
    private $unitOfMeasurement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ThirdPartyService")
     * @ORM\JoinColumn(name="service", referencedColumnName="id")
     */
    private $thirdPartyService;

    public function __construct()
    {
        $this->sleepLevels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemoteId(): ?int
    {
        return $this->remoteId;
    }

    public function setRemoteId(?int $remoteId): self
    {
        $this->remoteId = $remoteId;

        return $this;
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

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(?\DateTimeInterface $end_time): self
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getLatencyToSleepOnset(): ?int
    {
        return $this->latency_to_sleep_onset;
    }

    public function setLatencyToSleepOnset(?int $latency_to_sleep_onset): self
    {
        $this->latency_to_sleep_onset = $latency_to_sleep_onset;

        return $this;
    }

    public function getLatencyToArising(): ?int
    {
        return $this->latency_to_arising;
    }

    public function setLatencyToArising(?int $latency_to_arising): self
    {
        $this->latency_to_arising = $latency_to_arising;

        return $this;
    }

    public function getTotalSleepTime(): ?int
    {
        return $this->total_sleep_time;
    }

    public function setTotalSleepTime(?int $total_sleep_time): self
    {
        $this->total_sleep_time = $total_sleep_time;

        return $this;
    }

    public function getNumberOfAwakenings(): ?int
    {
        return $this->number_of_awakenings;
    }

    public function setNumberOfAwakenings(?int $number_of_awakenings): self
    {
        $this->number_of_awakenings = $number_of_awakenings;

        return $this;
    }

    public function getIsMainSleep(): ?bool
    {
        return $this->is_main_sleep;
    }

    public function setIsMainSleep(?bool $is_main_sleep): self
    {
        $this->is_main_sleep = $is_main_sleep;

        return $this;
    }

    public function getEfficiencyPercentage(): ?int
    {
        return $this->efficiency_percentage;
    }

    public function setEfficiencyPercentage(?int $efficiency_percentage): self
    {
        $this->efficiency_percentage = $efficiency_percentage;

        return $this;
    }

    /**
     * @return Collection|SleepLevels[]
     */
    public function getSleepLevels(): Collection
    {
        return $this->sleepLevels;
    }

    public function addSleepLevel(SleepLevels $sleepLevel): self
    {
        if (!$this->sleepLevels->contains($sleepLevel)) {
            $this->sleepLevels[] = $sleepLevel;
            $sleepLevel->setSleepEpisode($this);
        }

        return $this;
    }

    public function removeSleepLevel(SleepLevels $sleepLevel): self
    {
        if ($this->sleepLevels->contains($sleepLevel)) {
            $this->sleepLevels->removeElement($sleepLevel);
            // set the owning side to null (unless already changed)
            if ($sleepLevel->getSleepEpisode() === $this) {
                $sleepLevel->setSleepEpisode(null);
            }
        }

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

    public function getThirdPartyService(): ?ThirdPartyService
    {
        return $this->thirdPartyService;
    }

    public function setThirdPartyService(?ThirdPartyService $thirdPartyService): self
    {
        $this->thirdPartyService = $thirdPartyService;

        return $this;
    }
}