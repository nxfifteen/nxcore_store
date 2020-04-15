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

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientFriendsRepository")
 */
class PatientFriends
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
     * Get the internal primary identity key.
     *
     * @return $this
     */
    public function createGuid()
    {
        if (is_null($this->guid)) {
            try {
                $this->guid = Uuid::uuid1();
            } catch (Exception $e) {
            }
        }

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
}
