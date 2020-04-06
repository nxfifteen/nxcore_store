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
 * @ORM\Entity(repositoryClass="App\Repository\RpgChallengeFriendsRepository")
 */
class RpgChallengeFriends
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarget(): ?int
    {
        return $this->target;
    }

    public function setTarget(int $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration + 1;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getChallenger(): ?Patient
    {
        return $this->challenger;
    }

    public function setChallenger(?Patient $challenger): self
    {
        $this->challenger = $challenger;

        return $this;
    }

    public function getChallenged(): ?Patient
    {
        return $this->challenged;
    }

    public function setChallenged(?Patient $challenged): self
    {
        $this->challenged = $challenged;

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

    public function getOutcome(): ?int
    {
        return $this->outcome;
    }

    public function setOutcome(?int $outcome): self
    {
        $this->outcome = $outcome;

        return $this;
    }

    public function getInviteDate(): ?\DateTimeInterface
    {
        return $this->inviteDate;
    }

    public function setInviteDate(\DateTimeInterface $inviteDate): self
    {
        $this->inviteDate = $inviteDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getChallengerSum(): ?int
    {
        return $this->challengerSum;
    }

    public function setChallengerSum(?int $challengerSum): self
    {
        $this->challengerSum = $challengerSum;

        return $this;
    }

    public function getChallengedSum(): ?int
    {
        return $this->challengedSum;
    }

    public function setChallengedSum(?int $challengedSum): self
    {
        $this->challengedSum = $challengedSum;

        return $this;
    }

    public function getChallengerDetails(): ?array
    {
        return $this->challengerDetails;
    }

    public function setChallengerDetails(?array $challengerDetails): self
    {
        $this->challengerDetails = $challengerDetails;

        return $this;
    }

    public function getChallengedDetails(): ?array
    {
        return $this->challengedDetails;
    }

    public function setChallengedDetails(?array $challengedDetails): self
    {
        $this->challengedDetails = $challengedDetails;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeInterface $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }
}
