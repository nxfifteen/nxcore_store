<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrackingDeviceRepository")
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "name": "exact", "patient": "exact", "type": "exact", "remote_id": "exact"})
 */
class TrackingDevice
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $battery;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastSyncTime;

    /**
     * @ORM\Column(type="string", unique=true, length=20, nullable=true)
     */
    private $remote_id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="trackingDevice")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     */
    private $patient;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getBattery(): ?int
    {
        return $this->battery;
    }

    public function setBattery(?int $battery): self
    {
        $this->battery = $battery;

        return $this;
    }

    public function getLastSyncTime(): ?\DateTimeInterface
    {
        return $this->lastSyncTime;
    }

    public function setLastSyncTime(?\DateTimeInterface $lastSyncTime): self
    {
        $this->lastSyncTime = $lastSyncTime;

        return $this;
    }

    public function getRemoteId(): ?string
    {
        return $this->remote_id;
    }

    public function setRemoteId(?string $remote_id): self
    {
        $this->remote_id = $remote_id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}