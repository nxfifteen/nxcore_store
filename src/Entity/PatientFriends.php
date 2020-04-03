<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientFriendsRepository")
 */
class PatientFriends
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="friendsOf")
     * @ORM\JoinColumn(nullable=false)
     */
    private $friendA;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="friendsToo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $friendB;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accepted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFriendA(): ?Patient
    {
        return $this->friendA;
    }

    public function setFriendA(?Patient $friendA): self
    {
        $this->friendA = $friendA;

        return $this;
    }

    public function getFriendB(): ?Patient
    {
        return $this->friendB;
    }

    public function setFriendB(?Patient $friendB): self
    {
        $this->friendB = $friendB;

        return $this;
    }

    public function getAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(?bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }
}
