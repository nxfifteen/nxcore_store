<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LifeTrackerRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="LifeIndex", columns={"service","patient_id","remote_id"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "remote_id": "exact", "patient": "exact", "name": "exact", "thirdPartyService": "exact"})
 */
class LifeTracker
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $remote_id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $icon;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $charge;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $colour;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ThirdPartyService")
     * @ORM\JoinColumn(name="service", referencedColumnName="id")
     */
    private $thirdPartyService;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LifeTrackerConfig")
     * @ORM\JoinColumn(name="config", referencedColumnName="id")
     */
    private $lifeTrackerConfig;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemoteId(): ?string
    {
        return $this->remote_id;
    }

    public function setRemoteId(?string $remote_id): self
    {
        $this->remote_id = $remote_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function setColour(?string $colour): self
    {
        $this->colour = $colour;

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

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    public function getLifeTrackerConfig(): ?LifeTrackerConfig
    {
        return $this->lifeTrackerConfig;
    }

    public function setLifeTrackerConfig(?LifeTrackerConfig $lifeTrackerConfig): self
    {
        $this->lifeTrackerConfig = $lifeTrackerConfig;

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
}