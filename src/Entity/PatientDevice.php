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
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientDeviceRepository")
 */
class PatientDevice
{
    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * The internal primary identity key.
     *
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $guid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="devices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $userAgent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $os;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $browser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $device;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $os_version = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $browser_version;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sms;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastSeen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $app;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $version;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $production;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the internal primary identity key.
     *
     * @return UuidInterface|null
     */
    public function getGuid(): ?UuidInterface
    {
        if(is_null($this->guid)) {
            try {
                $this->guid = Uuid::uuid4();
            } catch (\Exception $e) {
            }
        }
        return $this->guid;
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
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * @param string|null $userAgent
     *
     * @return $this
     */
    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOs(): ?string
    {
        return $this->os;
    }

    /**
     * @param string|null $os
     *
     * @return $this
     */
    public function setOs(?string $os): self
    {
        $this->os = $os;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    /**
     * @param string|null $browser
     *
     * @return $this
     */
    public function setBrowser(?string $browser): self
    {
        $this->browser = $browser;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDevice(): ?string
    {
        return $this->device;
    }

    /**
     * @param string|null $device
     *
     * @return $this
     */
    public function setDevice(?string $device): self
    {
        $this->device = $device;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getOsVersion(): ?array
    {
        return $this->os_version;
    }

    /**
     * @param string|null $os_version
     *
     * @return $this
     */
    public function setOsVersion(?string $os_version): self
    {
        $this->os_version = $os_version;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBrowserVersion(): ?string
    {
        return $this->browser_version;
    }

    /**
     * @param string|null $browser_version
     *
     * @return $this
     */
    public function setBrowserVersion(?string $browser_version): self
    {
        $this->browser_version = $browser_version;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSms(): ?string
    {
        return $this->sms;
    }

    /**
     * @param string|null $sms
     *
     * @return $this
     */
    public function setSms(?string $sms): self
    {
        $this->sms = $sms;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastSeen(): ?DateTimeInterface
    {
        return $this->lastSeen;
    }

    /**
     * @param DateTimeInterface|null $lastSeen
     *
     * @return $this
     */
    public function setLastSeen(?DateTimeInterface $lastSeen): self
    {
        $this->lastSeen = $lastSeen;

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
     * @return string|null
     */
    public function getApp(): ?string
    {
        return $this->app;
    }

    /**
     * @param string|null $app
     *
     * @return $this
     */
    public function setApp(?string $app): self
    {
        $this->app = $app;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string|null $version
     *
     * @return $this
     */
    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getProduction(): ?bool
    {
        return $this->production;
    }

    /**
     * @param bool|null $production
     *
     * @return $this
     */
    public function setProduction(?bool $production): self
    {
        $this->production = $production;

        return $this;
    }
}
