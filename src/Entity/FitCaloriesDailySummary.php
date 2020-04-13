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
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DeviceRemote", columns={"remote_id","tracking_device_id"})})
 *
 * @ORM\Entity(repositoryClass="App\Repository\FitCaloriesDailySummaryRepository")
 */
class FitCaloriesDailySummary
{
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
     * The internal primary identity key.
     *
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $guid;

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
    private $value;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\PatientGoals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patientGoal;

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
     * Get the internal primary identity key.
     *
     * @return UuidInterface|null
     */
    public function getGuid(): ?UuidInterface
    {
        return $this->guid;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateTime(): ?DateTimeInterface
    {
        return $this->DateTime;
    }

    /**
     * @param DateTimeInterface $DateTime
     *
     * @return $this
     */
    public function setDateTime(DateTimeInterface $DateTime): self
    {
        $this->DateTime = $DateTime;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRemoteId(): ?string
    {
        return $this->RemoteId;
    }

    /**
     * @param string|null $RemoteId
     *
     * @return $this
     */
    public function setRemoteId(?string $RemoteId): self
    {
        $this->RemoteId = $RemoteId;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getValue(): ?float
    {
        return $this->value;
    }

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setValue(float $value): self
    {
        $this->value = $value;

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
     * @return TrackingDevice|null
     */
    public function getTrackingDevice(): ?TrackingDevice
    {
        return $this->trackingDevice;
    }

    /**
     * @param TrackingDevice|null $trackingDevice
     *
     * @return $this
     */
    public function setTrackingDevice(?TrackingDevice $trackingDevice): self
    {
        $this->trackingDevice = $trackingDevice;

        return $this;
    }

    /**
     * @return PatientGoals|null
     */
    public function getGoal(): ?PatientGoals
    {
        return $this->patientGoal;
    }

    /**
     * @param PatientGoals|null $patientGoal
     *
     * @return $this
     */
    public function setGoal(?PatientGoals $patientGoal): self
    {
        $this->patientGoal = $patientGoal;

        return $this;
    }

    /**
     * @return PatientGoals|null
     */
    public function getPatientGoal(): ?PatientGoals
    {
        return $this->patientGoal;
    }

    /**
     * @param PatientGoals|null $patientGoal
     *
     * @return $this
     */
    public function setPatientGoal(?PatientGoals $patientGoal): self
    {
        $this->patientGoal = $patientGoal;

        return $this;
    }
}
