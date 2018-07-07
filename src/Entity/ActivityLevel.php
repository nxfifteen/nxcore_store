<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityLevelRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="UniqueReading", columns={"sedentary","lightly","fairly","very"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "sedentary": "exact", "lightly": "exact", "fairly": "exact", "very": "exact"})
 */
class ActivityLevel
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
    private $sedentary;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $lightly;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $fairly;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $very;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSedentary(): ?int
    {
        return $this->sedentary;
    }

    public function setSedentary(?int $sedentary): self
    {
        $this->sedentary = $sedentary;

        return $this;
    }

    public function getLightly(): ?int
    {
        return $this->lightly;
    }

    public function setLightly(?int $lightly): self
    {
        $this->lightly = $lightly;

        return $this;
    }

    public function getFairly(): ?int
    {
        return $this->fairly;
    }

    public function setFairly(?int $fairly): self
    {
        $this->fairly = $fairly;

        return $this;
    }

    public function getVery(): ?int
    {
        return $this->very;
    }

    public function setVery(?int $very): self
    {
        $this->very = $very;

        return $this;
    }
}