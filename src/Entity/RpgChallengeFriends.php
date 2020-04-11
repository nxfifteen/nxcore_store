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
 * @ORM\Entity(repositoryClass="App\Repository\RpgChallengeFriendsRepository")
 */
class RpgChallengeFriends
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
     * @ORM\Column(type="integer")
     */
    private $target;

    /**
     * @ORM\Column(type="datetime")
     */
    private $inviteDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="rpgChallenges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $challenger;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="rpgChallenger")
     * @ORM\JoinColumn(nullable=false)
     */
    private $challenged;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $criteria;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $outcome;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $challengerSum;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $challengedSum;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $challengerDetails = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $challengedDetails = [];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completedAt;

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
     * @return int|null
     */
    public function getTarget(): ?int
    {
        return $this->target;
    }

    /**
     * @param int $target
     *
     * @return $this
     */
    public function setTarget(int $target): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * @param DateTimeInterface $startDate
     *
     * @return $this
     */
    public function setStartDate(DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration + 1;
    }

    /**
     * @param int $duration
     *
     * @return $this
     */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Patient|null
     */
    public function getChallenger(): ?Patient
    {
        return $this->challenger;
    }

    /**
     * @param Patient|null $challenger
     *
     * @return $this
     */
    public function setChallenger(?Patient $challenger): self
    {
        $this->challenger = $challenger;

        return $this;
    }

    /**
     * @return Patient|null
     */
    public function getChallenged(): ?Patient
    {
        return $this->challenged;
    }

    /**
     * @param Patient|null $challenged
     *
     * @return $this
     */
    public function setChallenged(?Patient $challenged): self
    {
        $this->challenged = $challenged;

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
     * @return int|null
     */
    public function getOutcome(): ?int
    {
        return $this->outcome;
    }

    /**
     * @param int|null $outcome
     *
     * @return $this
     */
    public function setOutcome(?int $outcome): self
    {
        $this->outcome = $outcome;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getInviteDate(): ?DateTimeInterface
    {
        return $this->inviteDate;
    }

    /**
     * @param DateTimeInterface $inviteDate
     *
     * @return $this
     */
    public function setInviteDate(DateTimeInterface $inviteDate): self
    {
        $this->inviteDate = $inviteDate;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * @param DateTimeInterface|null $endDate
     *
     * @return $this
     */
    public function setEndDate(?DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getChallengerSum(): ?int
    {
        return $this->challengerSum;
    }

    /**
     * @param int|null $challengerSum
     *
     * @return $this
     */
    public function setChallengerSum(?int $challengerSum): self
    {
        $this->challengerSum = $challengerSum;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getChallengedSum(): ?int
    {
        return $this->challengedSum;
    }

    /**
     * @param int|null $challengedSum
     *
     * @return $this
     */
    public function setChallengedSum(?int $challengedSum): self
    {
        $this->challengedSum = $challengedSum;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getChallengerDetails(): ?array
    {
        return $this->challengerDetails;
    }

    /**
     * @param array|null $challengerDetails
     *
     * @return $this
     */
    public function setChallengerDetails(?array $challengerDetails): self
    {
        $this->challengerDetails = $challengerDetails;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getChallengedDetails(): ?array
    {
        return $this->challengedDetails;
    }

    /**
     * @param array|null $challengedDetails
     *
     * @return $this
     */
    public function setChallengedDetails(?array $challengedDetails): self
    {
        $this->challengedDetails = $challengedDetails;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCompletedAt(): ?DateTimeInterface
    {
        return $this->completedAt;
    }

    /**
     * @param DateTimeInterface|null $completedAt
     *
     * @return $this
     */
    public function setCompletedAt(?DateTimeInterface $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }
}
