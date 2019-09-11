<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "uuid": "exact"})
 */
class Patient implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $apiToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FitStepsDailySummary", mappedBy="patient", orphanRemoval=true)
     */
    private $fitStepsDailySummaries;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrackingDevice", mappedBy="patient", orphanRemoval=true)
     */
    private $trackingDevices;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $surName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $uiSettings = [];

    public function __construct()
    {
        $this->fitStepsDailySummaries = new ArrayCollection();
        $this->trackingDevices = new ArrayCollection();
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->uuid;
    }

    /**
     * @return Collection|TrackingDevice[]
     */
    public function getTrackingDevices(): Collection
    {
        return $this->trackingDevices;
    }

    public function addTrackingDevice(TrackingDevice $trackingDevice): self
    {
        if (!$this->trackingDevices->contains($trackingDevice)) {
            $this->trackingDevices[] = $trackingDevice;
            $trackingDevice->setPatient($this);
        }

        return $this;
    }

    public function removeTrackingDevice(TrackingDevice $trackingDevice): self
    {
        if ($this->trackingDevices->contains($trackingDevice)) {
            $this->trackingDevices->removeElement($trackingDevice);
            // set the owning side to null (unless already changed)
            if ($trackingDevice->getPatient() === $this) {
                $trackingDevice->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FitStepsDailySummary[]
     */
    public function getFitStepsDailySummaries(): Collection
    {
        return $this->fitStepsDailySummaries;
    }

    public function addFitStepsDailySummary(FitStepsDailySummary $fitStepsDailySummary): self
    {
        if (!$this->fitStepsDailySummaries->contains($fitStepsDailySummary)) {
            $this->fitStepsDailySummaries[] = $fitStepsDailySummary;
            $fitStepsDailySummary->setPatient($this);
        }

        return $this;
    }

    public function removeFitStepsDailySummary(FitStepsDailySummary $fitStepsDailySummary): self
    {
        if ($this->fitStepsDailySummaries->contains($fitStepsDailySummary)) {
            $this->fitStepsDailySummaries->removeElement($fitStepsDailySummary);
            // set the owning side to null (unless already changed)
            if ($fitStepsDailySummary->getPatient() === $this) {
                $fitStepsDailySummary->setPatient(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSurName(): ?string
    {
        return $this->surName;
    }

    public function setSurName(?string $surName): self
    {
        $this->surName = $surName;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getUiSettings(): ?array
    {
        return $this->uiSettings;
    }

    public function setUiSettings(?array $uiSettings): self
    {
        $this->uiSettings = $uiSettings;

        return $this;
    }
}
