<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientMembershipRepository")
 */
class PatientMembership
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tear;

    /**
     * @ORM\Column(type="datetime")
     */
    private $since;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="boolean")
     */
    private $lifetime;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Patient", inversedBy="membership", cascade={"persist", "remove"})
     */
    private $patient;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastPaid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTear(): ?string
    {
        return $this->tear;
    }

    public function setTear(string $tear): self
    {
        $this->tear = $tear;

        return $this;
    }

    public function getSince(): ?\DateTimeInterface
    {
        return $this->since;
    }

    public function setSince(\DateTimeInterface $since): self
    {
        $this->since = $since;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getLifetime(): ?bool
    {
        return $this->lifetime;
    }

    public function setLifetime(bool $lifetime): self
    {
        $this->lifetime = $lifetime;

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

    public function getLastPaid(): ?\DateTimeInterface
    {
        return $this->lastPaid;
    }

    public function setLastPaid(\DateTimeInterface $lastPaid): self
    {
        $this->lastPaid = $lastPaid;

        return $this;
    }
}
