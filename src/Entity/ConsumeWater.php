<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DeviceRemote", columns={"remote_id","tracking_device_id"})})
 *
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ConsumeWaterRepository")
 */
class ConsumeWater
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $RemoteId;

    /**
     * @ORM\Column(type="float")
     */
    private $measurement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UnitOfMeasurement")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unitOfMeasurement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\ThirdPartyService")
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PartOfDay")
     * @ORM\JoinColumn(nullable=false)
     */
    private $partOfDay;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PatientGoals")
     * @ORM\JoinColumn(nullable=true)
     */
    private $patientGoal;

    public function getId(): ?int
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

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->DateTime;
    }

    public function setDateTime(\DateTimeInterface $DateTime): self
    {
        $this->DateTime = $DateTime;

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

    public function getRemoteId(): ?string
    {
        return $this->RemoteId;
    }

    public function setRemoteId(string $RemoteId): self
    {
        $this->RemoteId = $RemoteId;

        return $this;
    }

    public function gettrackingDevice(): ?TrackingDevice
    {
        return $this->trackingDevice;
    }

    public function settrackingDevice(?TrackingDevice $trackingDevice): self
    {
        $this->trackingDevice = $trackingDevice;

        return $this;
    }

    public function getService(): ?ThirdPartyService
    {
        return $this->service;
    }

    public function setService(?ThirdPartyService $service): self
    {
        $this->service = $service;

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

    public function getPatientGoal(): ?PatientGoals
    {
        return $this->patientGoal;
    }

    public function setPatientGoal(?PatientGoals $patientGoal): self
    {
        $this->patientGoal = $patientGoal;

        return $this;
    }
}
