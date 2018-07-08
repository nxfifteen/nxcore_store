<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LifeTrackedRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="Tracked", columns={"tracker","date_time","value"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "date_time": "exact", "value": "exact", "score": "exact", "lifeTracker": "exact", "lon": "exact", "lat": "exact"})
 */
class LifeTracked
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_time;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $lon;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $score;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LifeTracker")
     * @ORM\JoinColumn(name="tracker", referencedColumnName="id")
     */
    private $lifeTracker;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->date_time;
    }

    public function setDateTime(?\DateTimeInterface $date_time): self
    {
        $this->date_time = $date_time;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?string
    {
        return $this->lon;
    }

    public function setLon(?string $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

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