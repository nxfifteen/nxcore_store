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
 * @ORM\Entity(repositoryClass="App\Repository\BodyFatRepository")
 */
class BodyFat
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
     * @ORM\Column(type="float")
     */
    private $measurement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UnitOfMeasurement")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unitOfMeasurement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PartOfDay")
     */
    private $partOfDay;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PatientGoals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patientGoal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $RemoteId;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $fatFreeMass;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $fatFree;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $bodyFatMass;

    /**
     * Get the internal primary identity key.
     *
     * @param bool $force
     *
     * @return $this
     */
    public function createGuid(bool $force = false)
    {
        if ($force || is_null($this->guid)) {
            try {
                $this->guid = Uuid::uuid1();
            } catch (Exception $e) {
            }
        }

        return $this;
    }

    /**
     * @return float|null
     */
    public function getBodyFatMass(): ?float
    {
        return $this->bodyFatMass;
    }

    /**
     * @param float|null $bodyFatMass
     *
     * @return $this
     */
    public function setBodyFatMass(?float $bodyFatMass): self
    {
        $this->bodyFatMass = $bodyFatMass;

        return $this;
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
     * @return float|null
     */
    public function getFatFree(): ?float
    {
        return $this->fatFree;
    }

    /**
     * @param float|null $fatFree
     *
     * @return $this
     */
    public function setFatFree(?float $fatFree): self
    {
        $this->fatFree = $fatFree;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getFatFreeMass(): ?float
    {
        return $this->fatFreeMass;
    }

    /**
     * @param float|null $fatFreeMass
     *
     * @return $this
     */
    public function setFatFreeMass(?float $fatFreeMass): self
    {
        $this->fatFreeMass = $fatFreeMass;

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
     * @return float|null
     */
    public function getMeasurement(): ?float
    {
        return $this->measurement;
    }

    /**
     * @param float $measurement
     *
     * @return $this
     */
    public function setMeasurement(float $measurement): self
    {
        $this->measurement = $measurement;

        return $this;
    }

    /**
     * @return PartOfDay|null
     */
    public function getPartOfDay(): ?PartOfDay
    {
        return $this->partOfDay;
    }

    /**
     * @param PartOfDay|null $partOfDay
     *
     * @return $this
     */
    public function setPartOfDay(?PartOfDay $partOfDay): self
    {
        $this->partOfDay = $partOfDay;

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

    /**
     * @return string|null
     */
    public function getRemoteId(): ?string
    {
        return $this->RemoteId;
    }

    /**
     * @param string $RemoteId
     *
     * @return $this
     */
    public function setRemoteId(string $RemoteId): self
    {
        $this->RemoteId = $RemoteId;

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
     * @return UnitOfMeasurement|null
     */
    public function getUnitOfMeasurement(): ?UnitOfMeasurement
    {
        return $this->unitOfMeasurement;
    }

    /**
     * @param UnitOfMeasurement|null $unitOfMeasurement
     *
     * @return $this
     */
    public function setUnitOfMeasurement(?UnitOfMeasurement $unitOfMeasurement): self
    {
        $this->unitOfMeasurement = $unitOfMeasurement;

        return $this;
    }

    /**
     * Helper method to create json string from entiry
     *
     * @return string|null
     */
    public function toJson(): ?string
    {
        $returnString = [];
        foreach (get_class_methods($this) as $classMethod) {
            unset($holdValue);
            if (substr($classMethod, 0, 3) === "get" && $classMethod != "getId" && $classMethod != "getRemoteId") {
                $methodValue = str_ireplace("get", "", $classMethod);
                $holdValue = $this->$classMethod();
                switch (gettype($holdValue)) {
                    case "string":
                    case "integer":
                        $returnString[$methodValue] = $holdValue;
                        break;
                    case "object":
                        switch (get_class($holdValue)) {
                            case "DateTime":
                                $returnString[$methodValue] = $holdValue->format("U");
                                break;
                            case "Ramsey\\Uuid\\Uuid":
                                /** @var $holdValue UuidInterface */
                                $returnString[$methodValue] = $holdValue->toString();
                                break;
                            default:
                                if (substr(get_class($holdValue), 0, strlen("App\Entity\\")) === "App\Entity\\") {
                                    $returnString[$methodValue] = $holdValue->getGuid();
                                } else {
                                    $returnString[$methodValue] = get_class($holdValue);
                                }
                                break;
                        }
                        break;
                    default:
                        $returnString[$methodValue] = gettype($holdValue);
                        break;
                }
            }
        }

        return json_encode($returnString);
    }
}
