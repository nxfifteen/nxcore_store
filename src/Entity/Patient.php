<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ornicar\GravatarBundle\GravatarApi;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 *
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RpgXP", mappedBy="patient", orphanRemoval=true)
     */
    private $xp;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RpgRewardsAwarded", mappedBy="patient", orphanRemoval=true, cascade={"persist"})
     */
    private $rewards;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rpgFactor;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $firstRun;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PatientCredentials", mappedBy="patient", orphanRemoval=true)
     */
    private $patientCredentials;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RpgChallengeFriends", mappedBy="challenger", orphanRemoval=true)
     */
    private $rpgChallenges;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RpgChallengeFriends", mappedBy="challenged", orphanRemoval=true)
     */
    private $rpgChallenger;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PatientFriends", mappedBy="friendA", orphanRemoval=true)
     */
    private $friendsOf;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PatientFriends", mappedBy="friendB", orphanRemoval=true)
     */
    private $friendsToo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PatientSettings", mappedBy="patient", orphanRemoval=true)
     */
    private $settings;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoggedIn;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $loginStreak;

    public function __construct()
    {
        $this->fitStepsDailySummaries = new ArrayCollection();
        $this->trackingDevices = new ArrayCollection();
        $this->xp = new ArrayCollection();
        $this->rewards = new ArrayCollection();
        $this->patientCredentials = new ArrayCollection();
        $this->rpgChallenges = new ArrayCollection();
        $this->rpgChallenger = new ArrayCollection();
        $this->friendsOf = new ArrayCollection();
        $this->friendsToo = new ArrayCollection();
        $this->settings = new ArrayCollection();
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
        if (is_null($this->avatar)) {
            $gravatarApi = new GravatarApi();
            return $return['avatar'] = $gravatarApi->getUrl($this->email, 128, 'g', 'identicon');
        } else {
            return $this->avatar;
        }
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

    /**
     * @return Collection|RpgXP[]
     */
    public function getXp(): Collection
    {
        return $this->xp;
    }

    public function addXp(RpgXP $xp): self
    {
        if (!$this->xp->contains($xp)) {
            $this->xp[] = $xp;
            $xp->setPatient($this);
        }

        return $this;
    }

    public function removeXp(RpgXP $xp): self
    {
        if ($this->xp->contains($xp)) {
            $this->xp->removeElement($xp);
            // set the owning side to null (unless already changed)
            if ($xp->getPatient() === $this) {
                $xp->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RpgRewardsAwarded[]
     */
    public function getRewards(): Collection
    {
        return $this->rewards;
    }

    public function addReward(RpgRewardsAwarded $reward): self
    {
        if (!$this->rewards->contains($reward)) {
            $this->rewards[] = $reward;
            $reward->setPatient($this);
        }

        return $this;
    }

    public function removeReward(RpgRewardsAwarded $reward): self
    {
        if ($this->rewards->contains($reward)) {
            $this->rewards->removeElement($reward);
            // set the owning side to null (unless already changed)
            if ($reward->getPatient() === $this) {
                $reward->setPatient(null);
            }
        }

        return $this;
    }

    public function getRpgFactor(): ?float
    {
        if (is_null($this->rpgFactor)) {
            return 1;
        } else {
            return $this->rpgFactor;
        }
    }

    public function setRpgFactor(?float $rpgFactor): self
    {
        $this->rpgFactor = $rpgFactor;

        return $this;
    }

    public function getXpTotal()
    {
        $totalXp = 0;
        /** @var RpgXP $value */
        foreach ($this->xp as $value) {
            $totalXp = $totalXp + $value->getValue();
        }
        return $totalXp;
    }

    public function getRpgLevel()
    {
        $totalXp = $this->getXpTotal();
        return intval(explode(".", ( $totalXp / 100 ))[0]);
    }

    public function getFirstRun(): ?bool
    {
        if (is_null($this->firstRun) || $this->firstRun) {
            return true;
        } else {
            return false;
        }
    }

    public function setFirstRun(?bool $firstRun): self
    {
        $this->firstRun = $firstRun;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|PatientCredentials[]
     */
    public function getPatientCredentials(): Collection
    {
        return $this->patientCredentials;
    }

    public function addPatientCredential(PatientCredentials $patientCredential): self
    {
        if (!$this->patientCredentials->contains($patientCredential)) {
            $this->patientCredentials[] = $patientCredential;
            $patientCredential->setPatient($this);
        }

        return $this;
    }

    public function removePatientCredential(PatientCredentials $patientCredential): self
    {
        if ($this->patientCredentials->contains($patientCredential)) {
            $this->patientCredentials->removeElement($patientCredential);
            // set the owning side to null (unless already changed)
            if ($patientCredential->getPatient() === $this) {
                $patientCredential->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RpgChallengeFriends[]
     */
    public function getRpgChallenges(): Collection
    {
        return $this->rpgChallenges;
    }

    public function addRpgChallenge(RpgChallengeFriends $rpgChallenge): self
    {
        if (!$this->rpgChallenges->contains($rpgChallenge)) {
            $this->rpgChallenges[] = $rpgChallenge;
            $rpgChallenge->setChallenger($this);
        }

        return $this;
    }

    public function removeRpgChallenge(RpgChallengeFriends $rpgChallenge): self
    {
        if ($this->rpgChallenges->contains($rpgChallenge)) {
            $this->rpgChallenges->removeElement($rpgChallenge);
            // set the owning side to null (unless already changed)
            if ($rpgChallenge->getChallenger() === $this) {
                $rpgChallenge->setChallenger(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RpgChallengeFriends[]
     */
    public function getRpgChallenger(): Collection
    {
        return $this->rpgChallenger;
    }

    public function addRpgChallenger(RpgChallengeFriends $rpgChallenger): self
    {
        if (!$this->rpgChallenger->contains($rpgChallenger)) {
            $this->rpgChallenger[] = $rpgChallenger;
            $rpgChallenger->setChallenged($this);
        }

        return $this;
    }

    public function removeRpgChallenger(RpgChallengeFriends $rpgChallenger): self
    {
        if ($this->rpgChallenger->contains($rpgChallenger)) {
            $this->rpgChallenger->removeElement($rpgChallenger);
            // set the owning side to null (unless already changed)
            if ($rpgChallenger->getChallenged() === $this) {
                $rpgChallenger->setChallenged(null);
            }
        }

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * @return Collection|PatientFriends[]
     */
    public function getFriendsOf(): Collection
    {
        return $this->friendsOf;
    }

    public function addFriendsOf(PatientFriends $friendsOf): self
    {
        if (!$this->friendsOf->contains($friendsOf)) {
            $this->friendsOf[] = $friendsOf;
            $friendsOf->setFriendA($this);
        }

        return $this;
    }

    public function removeFriendsOf(PatientFriends $friendsOf): self
    {
        if ($this->friendsOf->contains($friendsOf)) {
            $this->friendsOf->removeElement($friendsOf);
            // set the owning side to null (unless already changed)
            if ($friendsOf->getFriendA() === $this) {
                $friendsOf->setFriendA(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PatientFriends[]
     */
    public function getFriendsToo(): Collection
    {
        return $this->friendsToo;
    }

    public function addFriendsToo(PatientFriends $friendsToo): self
    {
        if (!$this->friendsToo->contains($friendsToo)) {
            $this->friendsToo[] = $friendsToo;
            $friendsToo->setFriendB($this);
        }

        return $this;
    }

    public function removeFriendsToo(PatientFriends $friendsToo): self
    {
        if ($this->friendsToo->contains($friendsToo)) {
            $this->friendsToo->removeElement($friendsToo);
            // set the owning side to null (unless already changed)
            if ($friendsToo->getFriendB() === $this) {
                $friendsToo->setFriendB(null);
            }
        }

        return $this;
    }

    /**
     * @param Patient $friend
     *
     * @return bool
     */
    public function isFriendOf($friend): bool {

        return true;
    }

    /**
     * @return array
     */
    public function getFriendsWith(): array
    {
        $allFriends = [];
        /**
         * @var PatientFriends $friendToo
         */
        foreach ($this->friendsToo as $friendToo) {
            if (!is_null($friendToo->getAccepted()) && $friendToo->getAccepted()) {
                $allFriends[] = [
                    "id" => $friendToo->getFriendA()->getId(),
                    "name" => $friendToo->getFriendA()->getFirstName() . " " . $friendToo->getFriendA()->getSurName(),
                    "uuid" => $friendToo->getFriendA()->getUuid(),
                    "avatar" => $friendToo->getFriendA()->getAvatar(),
                ];
            }
        }

        /**
         * @var PatientFriends $friendToo
         */
        foreach ($this->friendsOf as $friendOf) {
            if (!is_null($friendOf->getAccepted()) && $friendOf->getAccepted()) {
                $allFriends[] = [
                    "id" => $friendOf->getFriendB()->getId(),
                    "name" => $friendOf->getFriendB()->getFirstName() . " " . $friendOf->getFriendB()->getSurName(),
                    "uuid" => $friendOf->getFriendB()->getUuid(),
                    "avatar" => $friendOf->getFriendB()->getAvatar(),
                ];
            }
        }

        return $allFriends;
    }

    /**
     * @return Collection|PatientSettings[]
     */
    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function addSetting(PatientSettings $setting): self
    {
        if (!$this->settings->contains($setting)) {
            $this->settings[] = $setting;
            $setting->setPatient($this);
        }

        return $this;
    }

    public function removeSetting(PatientSettings $setting): self
    {
        if ($this->settings->contains($setting)) {
            $this->settings->removeElement($setting);
            // set the owning side to null (unless already changed)
            if ($setting->getPatient() === $this) {
                $setting->setPatient(null);
            }
        }

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getPronoun()
    {
        switch ($this->gender) {
            case "male":
                return "he";
            case "female":
                return "she";
            default:
                return "they";
        }
    }

    public function getPronounAlt()
    {
        switch ($this->gender) {
            case "male":
                return "him";
            case "female":
                return "her";
            default:
                return "them";
        }
    }

    public function getLastLoggedIn(): ?\DateTimeInterface
    {
        return $this->lastLoggedIn;
    }

    public function setLastLoggedIn(?\DateTimeInterface $lastLoggedIn): self
    {
        $this->lastLoggedIn = $lastLoggedIn;

        return $this;
    }

    public function getLoginStreak(): ?int
    {
        return $this->loginStreak;
    }

    public function setLoginStreak(?int $loginStreak): self
    {
        $this->loginStreak = $loginStreak;

        return $this;
    }
}
