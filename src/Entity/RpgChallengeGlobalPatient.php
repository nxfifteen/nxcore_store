<?php
/**
 * DONE This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RpgChallengeGlobalPatientRepository")
 */
class RpgChallengeGlobalPatient
{
    /**
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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getChallenge(): ?RpgChallengeGlobal
    {
        return $this->challenge;
    }

    public function setChallenge(?RpgChallengeGlobal $challenge): self
    {
        $this->challenge = $challenge;

        return $this;
    }

    public function getCriteria(): ?string
    {
        return $this->criteria;
    }

    public function setCriteria(string $criteria): self
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function getStartDateTime(): ?\DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTimeInterface $startDateTime): self
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getFinishDateTime(): ?\DateTimeInterface
    {
        return $this->finishDateTime;
    }

    public function setFinishDateTime(?\DateTimeInterface $finishDateTime): self
    {
        $this->finishDateTime = $finishDateTime;

        return $this;
    }

    public function getProgress(): ?float
    {
        return $this->progress;
    }

    public function setProgress(?float $progress): self
    {
        $this->progress = $progress;

        return $this;
    }
}
