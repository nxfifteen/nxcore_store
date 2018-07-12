<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HeartRatePeakRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="UniqueReading", columns={"average","min","max"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "average": "exact", "min": "exact", "max": "exact", "time": "exact"})
 */
class HeartRatePeak
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
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $min;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $max;

    /**
     * 
     */
    private $time;

    /**
     * 
     */
    private $heartRateResting;

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

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(?int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): self
    {
        $this->time = $time;

        return $this;
    }
}