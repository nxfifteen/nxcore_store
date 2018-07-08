<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LifeTrackerScoreRepository")
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "cond": "exact", "compare": "exact", "charge": "exact", "lifeTracker": "exact"})
 */
class LifeTrackerScore
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $cond;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $compare;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $charge;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LifeTracker")
     * @ORM\JoinColumn(name="life_tracker", referencedColumnName="id")
     */
    private $lifeTracker;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCond(): ?string
    {
        return $this->cond;
    }

    public function setCond(?string $cond): self
    {
        $this->cond = $cond;

        return $this;
    }

    public function getCompare(): ?int
    {
        return $this->compare;
    }

    public function setCompare(?int $compare): self
    {
        $this->compare = $compare;

        return $this;
    }

    public function getCharge(): ?int
    {
        return $this->charge;
    }

    public function setCharge(?int $charge): self
    {
        $this->charge = $charge;

        return $this;
    }

    public function getLifeTracker(): ?LifeTracker
    {
        return $this->lifeTracker;
    }

    public function setLifeTracker(?LifeTracker $lifeTracker): self
    {
        $this->lifeTracker = $lifeTracker;

        return $this;
    }
}