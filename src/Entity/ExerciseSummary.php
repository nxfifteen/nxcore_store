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
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseSummaryRepository")
 */
class ExerciseSummary
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Exercise", inversedBy="exerciseSummary", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $exercise;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitudeGain;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitudeLoss;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitudeMax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitudeMin;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cadenceMax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cadenceMean;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cadenceMin;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $calorie;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $distanceIncline;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $distanceDecline;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $distance;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $speedMax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $speedMean;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $heartRateMax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $heartRateMean;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $heartRateMin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    public function setExercise(Exercise $exercise): self
    {
        $this->exercise = $exercise;

        return $this;
    }

    public function getAltitudeGain(): ?float
    {
        return $this->altitudeGain;
    }

    public function setAltitudeGain(?float $altitudeGain): self
    {
        $this->altitudeGain = $altitudeGain;

        return $this;
    }

    public function getAltitudeLoss(): ?float
    {
        return $this->altitudeLoss;
    }

    public function setAltitudeLoss(?float $altitudeLoss): self
    {
        $this->altitudeLoss = $altitudeLoss;

        return $this;
    }

    public function getAltitudeMax(): ?float
    {
        return $this->altitudeMax;
    }

    public function setAltitudeMax(?float $altitudeMax): self
    {
        $this->altitudeMax = $altitudeMax;

        return $this;
    }

    public function getAltitudeMin(): ?float
    {
        return $this->altitudeMin;
    }

    public function setAltitudeMin(?float $altitudeMin): self
    {
        $this->altitudeMin = $altitudeMin;

        return $this;
    }

    public function getCadenceMax(): ?float
    {
        return $this->cadenceMax;
    }

    public function setCadenceMax(?float $cadenceMax): self
    {
        $this->cadenceMax = $cadenceMax;

        return $this;
    }

    public function getCadenceMean(): ?float
    {
        return $this->cadenceMean;
    }

    public function setCadenceMean(?float $cadenceMean): self
    {
        $this->cadenceMean = $cadenceMean;

        return $this;
    }

    public function getCadenceMin(): ?float
    {
        return $this->cadenceMin;
    }

    public function setCadenceMin(?float $cadenceMin): self
    {
        $this->cadenceMin = $cadenceMin;

        return $this;
    }

    public function getCalorie(): ?float
    {
        return $this->calorie;
    }

    public function setCalorie(?float $calorie): self
    {
        $this->calorie = $calorie;

        return $this;
    }

    public function getDistanceIncline(): ?float
    {
        return $this->distanceIncline;
    }

    public function setDistanceIncline(?float $distanceIncline): self
    {
        $this->distanceIncline = $distanceIncline;

        return $this;
    }

    public function getDistanceDecline(): ?float
    {
        return $this->distanceDecline;
    }

    public function setDistanceDecline(?float $distanceDecline): self
    {
        $this->distanceDecline = $distanceDecline;

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(?float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getSpeedMax(): ?float
    {
        return $this->speedMax;
    }

    public function setSpeedMax(?float $speedMax): self
    {
        $this->speedMax = $speedMax;

        return $this;
    }

    public function getSpeedMean(): ?float
    {
        return $this->speedMean;
    }

    public function setSpeedMean(?float $speedMean): self
    {
        $this->speedMean = $speedMean;

        return $this;
    }

    public function getHeartRateMax(): ?float
    {
        return $this->heartRateMax;
    }

    public function setHeartRateMax(?float $heartRateMax): self
    {
        $this->heartRateMax = $heartRateMax;

        return $this;
    }

    public function getHeartRateMean(): ?float
    {
        return $this->heartRateMean;
    }

    public function setHeartRateMean(?float $heartRateMean): self
    {
        $this->heartRateMean = $heartRateMean;

        return $this;
    }

    public function getHeartRateMin(): ?float
    {
        return $this->heartRateMin;
    }

    public function setHeartRateMin(?float $heartRateMin): self
    {
        $this->heartRateMin = $heartRateMin;

        return $this;
    }
}
