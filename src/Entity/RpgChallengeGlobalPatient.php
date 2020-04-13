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
 * @ORM\Entity(repositoryClass="App\Repository\RpgChallengeGlobalPatientRepository")
 */
class RpgChallengeGlobalPatient
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
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="rpgChallengeGlobals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RpgChallengeGlobal")
     * @ORM\JoinColumn(nullable=false)
     */
    private $challenge;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $criteria;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finishDateTime;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $progress;

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
     * @return RpgChallengeGlobal|null
     */
    public function getChallenge(): ?RpgChallengeGlobal
    {
        return $this->challenge;
    }

    /**
     * @param RpgChallengeGlobal|null $challenge
     *
     * @return $this
     */
    public function setChallenge(?RpgChallengeGlobal $challenge): self
    {
        $this->challenge = $challenge;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteria(): ?string
    {
        return $this->criteria;
    }

    /**
     * @param string $criteria
     *
     * @return $this
     */
    public function setCriteria(string $criteria): self
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFinishDateTime(): ?DateTimeInterface
    {
        return $this->finishDateTime;
    }

    /**
     * @param DateTimeInterface|null $finishDateTime
     *
     * @return $this
     */
    public function setFinishDateTime(?DateTimeInterface $finishDateTime): self
    {
        $this->finishDateTime = $finishDateTime;

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
     * @return float|null
     */
    public function getProgress(): ?float
    {
        return $this->progress;
    }

    /**
     * @param float|null $progress
     *
     * @return $this
     */
    public function setProgress(?float $progress): self
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getStartDateTime(): ?DateTimeInterface
    {
        return $this->startDateTime;
    }

    /**
     * @param DateTimeInterface $startDateTime
     *
     * @return $this
     */
    public function setStartDateTime(DateTimeInterface $startDateTime): self
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }
}
