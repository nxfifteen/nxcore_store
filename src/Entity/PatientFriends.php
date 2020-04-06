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
    public function getFriendA(): ?Patient
    {
        return $this->friendA;
    }

    /**
     * @param Patient|null $friendA
     *
     * @return $this
     */
    public function setFriendA(?Patient $friendA): self
    {
        $this->friendA = $friendA;

        return $this;
    }

    /**
     * @return Patient|null
     */
    public function getFriendB(): ?Patient
    {
        return $this->friendB;
    }

    /**
     * @param Patient|null $friendB
     *
     * @return $this
     */
    public function setFriendB(?Patient $friendB): self
    {
        $this->friendB = $friendB;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAccepted(): ?bool
    {
        return $this->accepted;
    }

    /**
     * @param bool|null $accepted
     *
     * @return $this
     */
    public function setAccepted(?bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }
}
