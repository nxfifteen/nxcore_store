<?php

/*
* This file is part of the Storage module in NxFIFTEEN Core.
*
* Copyright (c) 2019. Stuart McCulloch Anderson
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*
* @package     Store
* @version     0.0.0.x
* @since       0.0.0.1
* @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
* @link        https://nxfifteen.me.uk NxFIFTEEN
* @link        https://git.nxfifteen.rocks/nx-health NxFIFTEEN Core
* @link        https://git.nxfifteen.rocks/nx-health/store NxFIFTEEN Core Storage
* @copyright   2019 Stuart McCulloch Anderson
* @license     https://license.nxfifteen.rocks/mit/2015-2019/ MIT
*/

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SportActivityRepository")
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "activitySource": "exact", "patient": "exact", "duration": "exact", "startTime": "exact", "activityName": "exact", "remote_id": "exact"})
 */
class SportActivity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $remote_id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $activityName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", length=6, nullable=true)
     */
    private $steps;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $calories;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $elevationGain;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="sportActivity")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SportActivitySource")
     * @ORM\JoinColumn(name="source", referencedColumnName="id")
     */
    private $activitySource;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HeartRate")
     * @ORM\JoinColumn(name="heart_rate_id", referencedColumnName="id")
     */
    private $heartRate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ActivityLevel")
     * @ORM\JoinColumn(name="activity_level_id", referencedColumnName="id")
     */
    private $activityLevel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PartOfDay")
     * @ORM\JoinColumn(name="part_of_day", referencedColumnName="id")
     */
    private $partOfDay;

    /**
     * 
     * @ORM\JoinColumn(name="sport_track", referencedColumnName="id", unique=true)
     * @ORM\OneToOne(targetEntity="App\Entity\SportTrack", inversedBy="sportActivity")
     */
    private $sportTrack;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemoteId(): ?int
    {
        return $this->remote_id;
    }

    public function setRemoteId(?int $remote_id): self
    {
        $this->remote_id = $remote_id;

        return $this;
    }

    public function getActivityName(): ?string
    {
        return $this->activityName;
    }

    public function setActivityName(?string $activityName): self
    {
        $this->activityName = $activityName;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

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

    public function getActivitySource(): ?SportActivitySource
    {
        return $this->activitySource;
    }

    public function setActivitySource(?SportActivitySource $activitySource): self
    {
        $this->activitySource = $activitySource;

        return $this;
    }

    public function getHeartRate(): ?HeartRate
    {
        return $this->heartRate;
    }

    public function setHeartRate(?HeartRate $heartRate): self
    {
        $this->heartRate = $heartRate;

        return $this;
    }

    public function getActivityLevel(): ?ActivityLevel
    {
        return $this->activityLevel;
    }

    public function setActivityLevel(?ActivityLevel $activityLevel): self
    {
        $this->activityLevel = $activityLevel;

        return $this;
    }

    public function getPartOfDay(): ?PartOfDay
    {
        return $this->partOfDay;
    }

    public function setPartOfDay(?PartOfDay $partOfDay): self
    {
        $this->partOfDay = $partOfDay;

        return $this;
    }

    public function getSportTrack(): ?SportTrack
    {
        return $this->sportTrack;
    }

    public function setSportTrack(?SportTrack $sportTrack): self
    {
        $this->sportTrack = $sportTrack;

        return $this;
    }

    public function getSteps(): ?int
    {
        return $this->steps;
    }

    public function setSteps(?int $steps): self
    {
        $this->steps = $steps;

        return $this;
    }

    public function getCalories(): ?int
    {
        return $this->calories;
    }

    public function setCalories(?int $calories): self
    {
        $this->calories = $calories;

        return $this;
    }

    public function getElevationGain(): ?float
    {
        return $this->elevationGain;
    }

    public function setElevationGain(?float $elevationGain): self
    {
        $this->elevationGain = $elevationGain;

        return $this;
    }
}