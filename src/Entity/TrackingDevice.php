<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

/** @noinspection DuplicatedCode */

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DeviceService", columns={"remote_id","service_id","patient_id"})})
 *
 * @ORM\Entity(repositoryClass="App\Repository\TrackingDeviceRepository")
 */
class TrackingDevice
{
    /**
     * The internal primary identity key.
     *
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $guid;
    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $battery;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastSynced;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $remoteId;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $manufacturer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $model;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="trackingDevices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ThirdPartyService", inversedBy="trackingDevices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * Get the internal primary identity key.
     *
     * @return $this
     */
    public function createGuid()
    {
        if (is_null($this->guid)) {
            try {
                $this->guid = Uuid::uuid4();
            } catch (Exception $e) {
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBattery(): ?int
    {
        return $this->battery;
    }

    /**
     * @param int|null $battery
     *
     * @return $this
     */
    public function setBattery(?int $battery): self
    {
        $this->battery = $battery;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return $this
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the internal primary identity key.
     *
     * @return UuidInterface|null
     */
    public function getGuid(): ?UuidInterface
    {
        return $this->guid;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastSynced(): ?DateTimeInterface
    {
        return $this->lastSynced;
    }

    /**
     * @param DateTimeInterface|null $lastSynced
     *
     * @return $this
     */
    public function setLastSynced(?DateTimeInterface $lastSynced): self
    {
        $this->lastSynced = $lastSynced;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    /**
     * @param string $manufacturer
     *
     * @return $this
     */
    public function setManufacturer(string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * @param string|null $model
     *
     * @return $this
     */
    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Patient|null
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * @param Patient|null $patient
     *
     * @return $this
     */
    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRemoteId(): ?string
    {
        return $this->remoteId;
    }

    /**
     * @param string|null $remoteId
     *
     * @return $this
     */
    public function setRemoteId(?string $remoteId): self
    {
        $this->remoteId = $remoteId;

        return $this;
    }

    /**
     * @return ThirdPartyService|null
     */
    public function getService(): ?ThirdPartyService
    {
        return $this->service;
    }

    /**
     * @param ThirdPartyService|null $service
     *
     * @return $this
     */
    public function setService(?ThirdPartyService $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
