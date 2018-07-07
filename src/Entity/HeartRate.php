<?php
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
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "average": "exact"})
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
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $average;

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
}