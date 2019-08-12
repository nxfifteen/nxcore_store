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
 * @ORM\Entity(repositoryClass="App\Repository\HeartRateRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(
 *             name="UniqueReading",
 *             columns={"average","out_of_range_id","fat_burn_id","cardio_id","peak_id"}
 *         )})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "average": "exact", "service": "exact"})
 */
class HeartRate
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ThirdPartyService")
     * @ORM\JoinColumn(name="service", referencedColumnName="id")
     */
    private $thirdPartyService;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $average;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $out_of_range_time;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $fat_burn_time;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $cardio_time;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $peak_time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HeartRateOutOfRange")
     * @ORM\JoinColumn(name="out_of_range_id", referencedColumnName="id")
     */
    private $heartRateOutOfRange;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HeartRateFatBurn")
     * @ORM\JoinColumn(name="fat_burn_id", referencedColumnName="id")
     */
    private $heartRateFatBurn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HeartRateCardio")
     * @ORM\JoinColumn(name="cardio_id", referencedColumnName="id")
     */
    private $heartRateCardio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HeartRatePeak")
     * @ORM\JoinColumn(name="peak_id", referencedColumnName="id")
     */
    private $heartRatePeak;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UnitOfMeasurement")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id")
     */
    private $unitOfMeasurement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAverage(): ?int
    {
        return $this->average;
    }

    public function setAverage(?int $average): self
    {
        $this->average = $average;

        return $this;
    }

    public function getHeartRateOutOfRange(): ?HeartRateOutOfRange
    {
        return $this->heartRateOutOfRange;
    }

    public function setHeartRateOutOfRange(?HeartRateOutOfRange $heartRateOutOfRange): self
    {
        $this->heartRateOutOfRange = $heartRateOutOfRange;

        return $this;
    }

    public function getHeartRateFatBurn(): ?HeartRateFatBurn
    {
        return $this->heartRateFatBurn;
    }

    public function setHeartRateFatBurn(?HeartRateFatBurn $heartRateFatBurn): self
    {
        $this->heartRateFatBurn = $heartRateFatBurn;

        return $this;
    }

    public function getHeartRateCardio(): ?HeartRateCardio
    {
        return $this->heartRateCardio;
    }

    public function setHeartRateCardio(?HeartRateCardio $heartRateCardio): self
    {
        $this->heartRateCardio = $heartRateCardio;

        return $this;
    }

    public function getHeartRatePeak(): ?HeartRatePeak
    {
        return $this->heartRatePeak;
    }

    public function setHeartRatePeak(?HeartRatePeak $heartRatePeak): self
    {
        $this->heartRatePeak = $heartRatePeak;

        return $this;
    }

    public function getOutOfRangeTime(): ?int
    {
        return $this->out_of_range_time;
    }

    public function setOutOfRangeTime(?int $out_of_range_time): self
    {
        $this->out_of_range_time = $out_of_range_time;

        return $this;
    }

    public function getFatBurnTime(): ?int
    {
        return $this->fat_burn_time;
    }

    public function setFatBurnTime(?int $fat_burn_time): self
    {
        $this->fat_burn_time = $fat_burn_time;

        return $this;
    }

    public function getCardioTime(): ?int
    {
        return $this->cardio_time;
    }

    public function setCardioTime(?int $cardio_time): self
    {
        $this->cardio_time = $cardio_time;

        return $this;
    }

    public function getPeakTime(): ?int
    {
        return $this->peak_time;
    }

    public function setPeakTime(?int $peak_time): self
    {
        $this->peak_time = $peak_time;

        return $this;
    }

    public function getUnitOfMeasurement(): ?UnitOfMeasurement
    {
        return $this->unitOfMeasurement;
    }

    public function setUnitOfMeasurement(?UnitOfMeasurement $unitOfMeasurement): self
    {
        $this->unitOfMeasurement = $unitOfMeasurement;

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