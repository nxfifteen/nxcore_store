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
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="EntityPulled", columns={"patient_id","third_party_service_id","entity"})})
 *
 * @ORM\Entity(repositoryClass="App\Repository\ApiAccessLogRepository")
 */
class ApiAccessLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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

    public function getThirdPartyService(): ?ThirdPartyService
    {
        return $this->thirdPartyService;
    }

    public function setThirdPartyService(?ThirdPartyService $thirdPartyService): self
    {
        $this->thirdPartyService = $thirdPartyService;

        return $this;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getLastRetrieved(): ?\DateTimeInterface
    {
        return $this->lastRetrieved;
    }

    public function setLastRetrieved(\DateTimeInterface $lastRetrieved): self
    {
        $this->lastRetrieved = $lastRetrieved;

        return $this;
    }

    public function getLastPulled(): ?\DateTimeInterface
    {
        return $this->lastPulled;
    }

    public function setLastPulled(\DateTimeInterface $lastPulled): self
    {
        $this->lastPulled = $lastPulled;

        return $this;
    }

    public function getCooldown(): ?\DateTimeInterface
    {
        return $this->cooldown;
    }

    public function setCooldown(?\DateTimeInterface $cooldown): self
    {
        $this->cooldown = $cooldown;

        return $this;
    }
}
