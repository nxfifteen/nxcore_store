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
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="DateReading", columns={"date_time","patient_id","tracker_id"})})
 *
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "date_time": "exact", "date_time_end": "exact", "patient": "exact"})
 */
class Exercise
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_time;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_time_end;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     */
    private $patient;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="bigint")
     */
    private $duration;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $calorie;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $distance;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitude_gain;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitude_loss;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitude_max;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitude_min;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $speed_mean;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $speed_max;


    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cadence_max;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cadence_mean;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $caloricburn_rate_max;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $caloricburn_rate_mean;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $count_type;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $decline_distance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $exercise_custom_type;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $exercise_type;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $heart_rate_max;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $heart_rate_mean;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $heart_rate_min;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $incline_distance;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $power_max;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $power_mean;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rpm_max;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rpm_mean;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrackingDevice")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private $tracker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ThirdPartyService")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private $thirdPartyService;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->date_time;
    }

    public function setDateTime(\DateTimeInterface $date_time): self
    {
        $this->date_time = $date_time;

        return $this;
    }

    public function getDateTimeEnd(): ?\DateTimeInterface
    {
        return $this->date_time_end;
    }

    public function setDateTimeEnd(\DateTimeInterface $date_time_end): self
    {
        $this->date_time_end = $date_time_end;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

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

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(?float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getAltitudeGain(): ?float
    {
        return $this->altitude_gain;
    }

    public function setAltitudeGain(?float $altitude_gain): self
    {
        $this->altitude_gain = $altitude_gain;

        return $this;
    }

    public function getAltitudeLoss(): ?float
    {
        return $this->altitude_loss;
    }

    public function setAltitudeLoss(?float $altitude_loss): self
    {
        $this->altitude_loss = $altitude_loss;

        return $this;
    }

    public function getAltitudeMax(): ?float
    {
        return $this->altitude_max;
    }

    public function setAltitudeMax(?float $altitude_max): self
    {
        $this->altitude_max = $altitude_max;

        return $this;
    }

    public function getAltitudeMin(): ?float
    {
        return $this->altitude_min;
    }

    public function setAltitudeMin(?float $altitude_min): self
    {
        $this->altitude_min = $altitude_min;

        return $this;
    }

    public function getSpeedMean(): ?float
    {
        return $this->speed_mean;
    }

    public function setSpeedMean(?float $speed_mean): self
    {
        $this->speed_mean = $speed_mean;

        return $this;
    }

    public function getSpeedMax(): ?float
    {
        return $this->speed_max;
    }

    public function setSpeedMax(?float $speed_max): self
    {
        $this->speed_max = $speed_max;

        return $this;
    }

    public function getCadenceMax(): ?float
    {
        return $this->cadence_max;
    }

    public function setCadenceMax($cadence_max): self
    {
        $this->cadence_max = $cadence_max;

        return $this;
    }

    public function getCadenceMean(): ?float
    {
        return $this->cadence_mean;
    }

    public function setCadenceMean($cadence_mean): self
    {
        $this->cadence_mean = $cadence_mean;

        return $this;
    }

    public function getCaloricburnRateMax(): ?float
    {
        return $this->caloricburn_rate_max;
    }

    public function setCaloricburnRateMax(?float $caloricburn_rate_max): self
    {
        $this->caloricburn_rate_max = $caloricburn_rate_max;

        return $this;
    }

    public function getCaloricburnRateMean(): ?float
    {
        return $this->caloricburn_rate_mean;
    }

    public function setCaloricburnRateMean(?float $caloricburn_rate_mean): self
    {
        $this->caloricburn_rate_mean = $caloricburn_rate_mean;

        return $this;
    }

    public function getCountType(): ?float
    {
        return $this->count_type;
    }

    public function setCountType(?float $count_type): self
    {
        $this->count_type = $count_type;

        return $this;
    }

    public function getDeclineDistance(): ?float
    {
        return $this->decline_distance;
    }

    public function setDeclineDistance(?float $decline_distance): self
    {
        $this->decline_distance = $decline_distance;

        return $this;
    }

    public function getExerciseCustomType(): ?string
    {
        return $this->exercise_custom_type;
    }

    public function setExerciseCustomType(?string $exercise_custom_type): self
    {
        $this->exercise_custom_type = $exercise_custom_type;

        return $this;
    }

    public function getExerciseType(): ?float
    {
        return $this->exercise_type;
    }

    public function setExerciseType(?float $exercise_type): self
    {
        $this->exercise_type = $exercise_type;

        return $this;
    }

    public function getHeartRateMax(): ?float
    {
        return $this->heart_rate_max;
    }

    public function setHeartRateMax(?float $heart_rate_max): self
    {
        $this->heart_rate_max = $heart_rate_max;

        return $this;
    }

    public function getHeartRateMean(): ?float
    {
        return $this->heart_rate_mean;
    }

    public function setHeartRateMean(?float $heart_rate_mean): self
    {
        $this->heart_rate_mean = $heart_rate_mean;

        return $this;
    }

    public function getHeartRateMin(): ?float
    {
        return $this->heart_rate_min;
    }

    public function setHeartRateMin(?float $heart_rate_min): self
    {
        $this->heart_rate_min = $heart_rate_min;

        return $this;
    }

    public function getInclineDistance(): ?float
    {
        return $this->incline_distance;
    }

    public function setInclineDistance(?float $incline_distance): self
    {
        $this->incline_distance = $incline_distance;

        return $this;
    }

    public function getPowerMax(): ?float
    {
        return $this->power_max;
    }

    public function setPowerMax(?float $power_max): self
    {
        $this->power_max = $power_max;

        return $this;
    }

    public function getPowerMean(): ?float
    {
        return $this->power_mean;
    }

    public function setPowerMean(?float $power_mean): self
    {
        $this->power_mean = $power_mean;

        return $this;
    }

    public function getRpmMax(): ?float
    {
        return $this->rpm_max;
    }

    public function setRpmMax(?float $rpm_max): self
    {
        $this->rpm_max = $rpm_max;

        return $this;
    }

    public function getRpmMean(): ?float
    {
        return $this->rpm_mean;
    }

    public function setRpmMean(?float $rpm_mean): self
    {
        $this->rpm_mean = $rpm_mean;

        return $this;
    }

    public function getTracker(): ?TrackingDevice
    {
        return $this->tracker;
    }

    public function setTracker(?TrackingDevice $tracker): self
    {
        $this->tracker = $tracker;

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
}
