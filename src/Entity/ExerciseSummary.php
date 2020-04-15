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
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseSummaryRepository")
 */
class ExerciseSummary
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

    /**
     * Get the internal primary identity key.
     *
     * @param bool $force
     *
     * @return $this
     */
    public function createGuid(bool $force = false)
    {
        if ($force || is_null($this->guid)) {
            try {
                $this->guid = Uuid::uuid1();
            } catch (Exception $e) {
            }
        }

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAltitudeGain(): ?float
    {
        return $this->altitudeGain;
    }

    /**
     * @param float|null $altitudeGain
     *
     * @return $this
     */
    public function setAltitudeGain(?float $altitudeGain): self
    {
        $this->altitudeGain = $altitudeGain;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAltitudeLoss(): ?float
    {
        return $this->altitudeLoss;
    }

    /**
     * @param float|null $altitudeLoss
     *
     * @return $this
     */
    public function setAltitudeLoss(?float $altitudeLoss): self
    {
        $this->altitudeLoss = $altitudeLoss;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAltitudeMax(): ?float
    {
        return $this->altitudeMax;
    }

    /**
     * @param float|null $altitudeMax
     *
     * @return $this
     */
    public function setAltitudeMax(?float $altitudeMax): self
    {
        $this->altitudeMax = $altitudeMax;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAltitudeMin(): ?float
    {
        return $this->altitudeMin;
    }

    /**
     * @param float|null $altitudeMin
     *
     * @return $this
     */
    public function setAltitudeMin(?float $altitudeMin): self
    {
        $this->altitudeMin = $altitudeMin;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCadenceMax(): ?float
    {
        return $this->cadenceMax;
    }

    /**
     * @param float|null $cadenceMax
     *
     * @return $this
     */
    public function setCadenceMax(?float $cadenceMax): self
    {
        $this->cadenceMax = $cadenceMax;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCadenceMean(): ?float
    {
        return $this->cadenceMean;
    }

    /**
     * @param float|null $cadenceMean
     *
     * @return $this
     */
    public function setCadenceMean(?float $cadenceMean): self
    {
        $this->cadenceMean = $cadenceMean;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCadenceMin(): ?float
    {
        return $this->cadenceMin;
    }

    /**
     * @param float|null $cadenceMin
     *
     * @return $this
     */
    public function setCadenceMin(?float $cadenceMin): self
    {
        $this->cadenceMin = $cadenceMin;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCalorie(): ?float
    {
        return $this->calorie;
    }

    /**
     * @param float|null $calorie
     *
     * @return $this
     */
    public function setCalorie(?float $calorie): self
    {
        $this->calorie = $calorie;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getDistance(): ?float
    {
        return $this->distance;
    }

    /**
     * @param float|null $distance
     *
     * @return $this
     */
    public function setDistance(?float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getDistanceDecline(): ?float
    {
        return $this->distanceDecline;
    }

    /**
     * @param float|null $distanceDecline
     *
     * @return $this
     */
    public function setDistanceDecline(?float $distanceDecline): self
    {
        $this->distanceDecline = $distanceDecline;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getDistanceIncline(): ?float
    {
        return $this->distanceIncline;
    }

    /**
     * @param float|null $distanceIncline
     *
     * @return $this
     */
    public function setDistanceIncline(?float $distanceIncline): self
    {
        $this->distanceIncline = $distanceIncline;

        return $this;
    }

    /**
     * @return Exercise|null
     */
    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    /**
     * @param Exercise $exercise
     *
     * @return $this
     */
    public function setExercise(Exercise $exercise): self
    {
        $this->exercise = $exercise;

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
     * @return float|null
     */
    public function getHeartRateMax(): ?float
    {
        return $this->heartRateMax;
    }

    /**
     * @param float|null $heartRateMax
     *
     * @return $this
     */
    public function setHeartRateMax(?float $heartRateMax): self
    {
        $this->heartRateMax = $heartRateMax;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getHeartRateMean(): ?float
    {
        return $this->heartRateMean;
    }

    /**
     * @param float|null $heartRateMean
     *
     * @return $this
     */
    public function setHeartRateMean(?float $heartRateMean): self
    {
        $this->heartRateMean = $heartRateMean;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getHeartRateMin(): ?float
    {
        return $this->heartRateMin;
    }

    /**
     * @param float|null $heartRateMin
     *
     * @return $this
     */
    public function setHeartRateMin(?float $heartRateMin): self
    {
        $this->heartRateMin = $heartRateMin;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return float|null
     */
    public function getSpeedMax(): ?float
    {
        return $this->speedMax;
    }

    /**
     * @param float|null $speedMax
     *
     * @return $this
     */
    public function setSpeedMax(?float $speedMax): self
    {
        $this->speedMax = $speedMax;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getSpeedMean(): ?float
    {
        return $this->speedMean;
    }

    /**
     * @param float|null $speedMean
     *
     * @return $this
     */
    public function setSpeedMean(?float $speedMean): self
    {
        $this->speedMean = $speedMean;

        return $this;
    }

    /**
     * Helper method to create json string from entiry
     *
     * @return string|null
     */
    public function toJson(): ?string
    {
        $returnString = [];
        foreach (get_class_methods($this) as $classMethod) {
            unset($holdValue);
            if (substr($classMethod, 0, 3) === "get" && $classMethod != "getId" && $classMethod != "getRemoteId") {
                $methodValue = str_ireplace("get", "", $classMethod);
                $holdValue = $this->$classMethod();
                switch (gettype($holdValue)) {
                    case "string":
                    case "integer":
                        $returnString[$methodValue] = $holdValue;
                        break;
                    case "object":
                        switch (get_class($holdValue)) {
                            case "DateTime":
                                $returnString[$methodValue] = $holdValue->format("U");
                                break;
                            case "Ramsey\\Uuid\\Uuid":
                                /** @var $holdValue UuidInterface */
                                $returnString[$methodValue] = $holdValue->toString();
                                break;
                            default:
                                if (substr(get_class($holdValue), 0, strlen("App\Entity\\")) === "App\Entity\\") {
                                    $returnString[$methodValue] = $holdValue->getGuid();
                                } else {
                                    $returnString[$methodValue] = get_class($holdValue);
                                }
                                break;
                        }
                        break;
                    default:
                        $returnString[$methodValue] = gettype($holdValue);
                        break;
                }
            }
        }

        return json_encode($returnString);
    }
}
