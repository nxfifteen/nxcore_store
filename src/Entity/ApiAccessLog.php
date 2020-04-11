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
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="EntityPulled", columns={"patient_id","third_party_service_id","entity"})})
 *
 * @ORM\Entity(repositoryClass="App\Repository\ApiAccessLogRepository")
 */
class ApiAccessLog
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ThirdPartyService")
     * @ORM\JoinColumn(nullable=false)
     */
    private $thirdPartyService;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $entity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastRetrieved;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastPulled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cooldown;

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
     * @return ThirdPartyService|null
     */
    public function getThirdPartyService(): ?ThirdPartyService
    {
        return $this->thirdPartyService;
    }

    /**
     * @param ThirdPartyService|null $thirdPartyService
     *
     * @return $this
     */
    public function setThirdPartyService(?ThirdPartyService $thirdPartyService): self
    {
        $this->thirdPartyService = $thirdPartyService;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEntity(): ?string
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     *
     * @return $this
     */
    public function setEntity(string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastRetrieved(): ?DateTimeInterface
    {
        return $this->lastRetrieved;
    }

    /**
     * @param DateTimeInterface $lastRetrieved
     *
     * @return $this
     */
    public function setLastRetrieved(DateTimeInterface $lastRetrieved): self
    {
        $this->lastRetrieved = $lastRetrieved;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastPulled(): ?DateTimeInterface
    {
        return $this->lastPulled;
    }

    /**
     * @param DateTimeInterface $lastPulled
     *
     * @return $this
     */
    public function setLastPulled(DateTimeInterface $lastPulled): self
    {
        $this->lastPulled = $lastPulled;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCooldown(): ?DateTimeInterface
    {
        return $this->cooldown;
    }

    /**
     * @param DateTimeInterface|null $cooldown
     *
     * @return $this
     */
    public function setCooldown(?DateTimeInterface $cooldown): self
    {
        $this->cooldown = $cooldown;

        return $this;
    }
}
