<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

/** @noinspection DuplicatedCode */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ornicar\GravatarBundle\GravatarApi;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 *
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "uuid": "exact"})
 */
class Patient implements UserInterface
{
    /**
     * The internal primary identity key.
     *
     * @var UuidInterface|null
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $guid;
    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     *
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
     * @ORM\OneToMany(targetEntity="App\Entity\RpgRewardsAwarded", mappedBy="patient", orphanRemoval=true,
     *                                                             cascade={"persist"})
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

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PatientMembership", mappedBy="patient", cascade={"persist", "remove"})
     */
    private $membership;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RpgChallengeGlobalPatient", mappedBy="patient", orphanRemoval=true)
     */
    private $rpgChallengeGlobals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SiteNews", mappedBy="patient")
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PatientDevice", mappedBy="patient", orphanRemoval=true)
     */
    private $devices;

    /**
     * Patient constructor.
     */
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
        $this->rpgChallengeGlobals = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->devices = new ArrayCollection();
    }

    /**
     * @param PatientDevice $device
     *
     * @return $this
     */
    public function addDevice(PatientDevice $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices[] = $device;
            $device->setPatient($this);
        }

        return $this;
    }

    /**
     * @param FitStepsDailySummary $fitStepsDailySummary
     *
     * @return $this
     */
    public function addFitStepsDailySummary(FitStepsDailySummary $fitStepsDailySummary): self
    {
        if (!$this->fitStepsDailySummaries->contains($fitStepsDailySummary)) {
            $this->fitStepsDailySummaries[] = $fitStepsDailySummary;
            $fitStepsDailySummary->setPatient($this);
        }

        return $this;
    }

    /**
     * @param PatientFriends $friendsOf
     *
     * @return $this
     */
    public function addFriendsOf(PatientFriends $friendsOf): self
    {
        if (!$this->friendsOf->contains($friendsOf)) {
            $this->friendsOf[] = $friendsOf;
            $friendsOf->setFriendA($this);
        }

        return $this;
    }

    /**
     * @param PatientFriends $friendsToo
     *
     * @return $this
     */
    public function addFriendsToo(PatientFriends $friendsToo): self
    {
        if (!$this->friendsToo->contains($friendsToo)) {
            $this->friendsToo[] = $friendsToo;
            $friendsToo->setFriendB($this);
        }

        return $this;
    }

    /**
     * @param SiteNews $notification
     *
     * @return $this
     */
    public function addNotification(SiteNews $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setPatient($this);
        }

        return $this;
    }

    /**
     * @param PatientCredentials $patientCredential
     *
     * @return $this
     */
    public function addPatientCredential(PatientCredentials $patientCredential): self
    {
        if (!$this->patientCredentials->contains($patientCredential)) {
            $this->patientCredentials[] = $patientCredential;
            $patientCredential->setPatient($this);
        }

        return $this;
    }

    /**
     * @param RpgRewardsAwarded $reward
     *
     * @return $this
     */
    public function addReward(RpgRewardsAwarded $reward): self
    {
        if (!$this->rewards->contains($reward)) {
            $this->rewards[] = $reward;
            $reward->setPatient($this);
        }

        return $this;
    }

    /**
     * @param RpgChallengeFriends $rpgChallenge
     *
     * @return $this
     */
    public function addRpgChallenge(RpgChallengeFriends $rpgChallenge): self
    {
        if (!$this->rpgChallenges->contains($rpgChallenge)) {
            $this->rpgChallenges[] = $rpgChallenge;
            $rpgChallenge->setChallenger($this);
        }

        return $this;
    }

    /**
     * @param RpgChallengeGlobalPatient $rpgChallengeGlobal
     *
     * @return $this
     */
    public function addRpgChallengeGlobal(RpgChallengeGlobalPatient $rpgChallengeGlobal): self
    {
        if (!$this->rpgChallengeGlobals->contains($rpgChallengeGlobal)) {
            $this->rpgChallengeGlobals[] = $rpgChallengeGlobal;
            $rpgChallengeGlobal->setPatient($this);
        }

        return $this;
    }

    /**
     * @param RpgChallengeFriends $rpgChallenger
     *
     * @return $this
     */
    public function addRpgChallenger(RpgChallengeFriends $rpgChallenger): self
    {
        if (!$this->rpgChallenger->contains($rpgChallenger)) {
            $this->rpgChallenger[] = $rpgChallenger;
            $rpgChallenger->setChallenged($this);
        }

        return $this;
    }

    /**
     * @param PatientSettings $setting
     *
     * @return $this
     */
    public function addSetting(PatientSettings $setting): self
    {
        if (!$this->settings->contains($setting)) {
            $this->settings[] = $setting;
            $setting->setPatient($this);
        }

        return $this;
    }

    /**
     * @param TrackingDevice $trackingDevice
     *
     * @return $this
     */
    public function addTrackingDevice(TrackingDevice $trackingDevice): self
    {
        if (!$this->trackingDevices->contains($trackingDevice)) {
            $this->trackingDevices[] = $trackingDevice;
            $trackingDevice->setPatient($this);
        }

        return $this;
    }

    /**
     * @param RpgXP $xp
     *
     * @return $this
     */
    public function addXp(RpgXP $xp): self
    {
        if (!$this->xp->contains($xp)) {
            $this->xp[] = $xp;
            $xp->setPatient($this);
        }

        return $this;
    }

    /**
     * Get the internal primary identity key.
     *
     * @param bool $force
     *
     * @return $this
     */
    public function createGuid(bool $force = false)
    {
        if ($force || is_null($this->guid)) {
            try {
                $this->guid = Uuid::uuid1();
            } catch (Exception $e) {
            }
        }

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

    /**
     * @param string $password
     *
     * @return $this
     */
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

    /**
     * @param array $roles
     *
     * @return $this
     */
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
     * @return string|null
     */
    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    /**
     * @param string $apiToken
     *
     * @return $this
     */
    public function setApiToken(string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        if (is_null($this->avatar)) {
            $gravatarApi = new GravatarApi();
            return $return['avatar'] = $gravatarApi->getUrl($this->email, 128, 'g', 'identicon');
        } else {
            return $this->avatar;
        }
    }

    /**
     * @param string|null $avatar
     *
     * @return $this
     */
    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateOfBirth(): ?DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    /**
     * @param DateTimeInterface|null $dateOfBirth
     *
     * @return $this
     */
    public function setDateOfBirth(?DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * @return Collection|PatientDevice[]
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     *
     * @return $this
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFirstRun(): ?bool
    {
        if (is_null($this->firstRun) || $this->firstRun) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param bool|null $firstRun
     *
     * @return $this
     */
    public function setFirstRun(?bool $firstRun): self
    {
        $this->firstRun = $firstRun;

        return $this;
    }

    /**
     * @return Collection|FitStepsDailySummary[]
     */
    public function getFitStepsDailySummaries(): Collection
    {
        return $this->fitStepsDailySummaries;
    }

    /**
     * @return Collection|PatientFriends[]
     */
    public function getFriendsOf(): Collection
    {
        return $this->friendsOf;
    }

    /**
     * @return Collection|PatientFriends[]
     */
    public function getFriendsToo(): Collection
    {
        return $this->friendsToo;
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
                    "name" => $friendToo->getFriendA()->getFirstName(),
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
                    "name" => $friendOf->getFriendB()->getFirstName(),
                    "uuid" => $friendOf->getFriendB()->getUuid(),
                    "avatar" => $friendOf->getFriendB()->getAvatar(),
                ];
            }
        }

        return $allFriends;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     *
     * @return $this
     */
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get the internal primary identity key.
     *
     * @return UuidInterface|null
     */
    public function getGuid(): ?UuidInterface
    {
        return $this->guid;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastLoggedIn(): ?DateTimeInterface
    {
        return $this->lastLoggedIn;
    }

    /**
     * @param DateTimeInterface|null $lastLoggedIn
     *
     * @return $this
     */
    public function setLastLoggedIn(?DateTimeInterface $lastLoggedIn): self
    {
        $this->lastLoggedIn = $lastLoggedIn;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLoginStreak(): ?int
    {
        return $this->loginStreak;
    }

    /**
     * @param int|null $loginStreak
     *
     * @return $this
     */
    public function setLoginStreak(?int $loginStreak): self
    {
        $this->loginStreak = $loginStreak;

        return $this;
    }

    /**
     * @return PatientMembership|null
     */
    public function getMembership(): ?PatientMembership
    {
        return $this->membership;
    }

    /**
     * @param PatientMembership|null $membership
     *
     * @return $this
     */
    public function setMembership(?PatientMembership $membership): self
    {
        $this->membership = $membership;

        // set (or unset) the owning side of the relation if necessary
        $newPatient = $membership === null ? null : $this;
        if ($newPatient !== $membership->getPatient()) {
            $membership->setPatient($newPatient);
        }

        return $this;
    }

    /**
     * @return Collection|SiteNews[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    /**
     * @return Collection|PatientCredentials[]
     */
    public function getPatientCredentials(): Collection
    {
        return $this->patientCredentials;
    }

    /**
     * @return string
     */
    public function getPronounTheir()
    {
        switch ($this->gender) {
            case "male":
                return "his";
            case "female":
                return "her";
            default:
                return "their";
        }
    }

    /**
     * @return string
     */
    public function getPronounThem()
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

    /**
     * @return string
     */
    public function getPronounThey()
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

    /**
     * @return Collection|RpgRewardsAwarded[]
     */
    public function getRewards(): Collection
    {
        return $this->rewards;
    }

    /**
     * @return Collection|RpgChallengeGlobalPatient[]
     */
    public function getRpgChallengeGlobals(): Collection
    {
        return $this->rpgChallengeGlobals;
    }

    /**
     * @return Collection|RpgChallengeFriends[]
     */
    public function getRpgChallenger(): Collection
    {
        return $this->rpgChallenger;
    }

    /**
     * @return Collection|RpgChallengeFriends[]
     */
    public function getRpgChallenges(): Collection
    {
        return $this->rpgChallenges;
    }

    /**
     * @return float|null
     */
    public function getRpgFactor(): ?float
    {
        if (is_null($this->rpgFactor)) {
            return 1;
        } else {
            return $this->rpgFactor;
        }
    }

    /**
     * @param float|null $rpgFactor
     *
     * @return $this
     */
    public function setRpgFactor(?float $rpgFactor): self
    {
        $this->rpgFactor = $rpgFactor;

        return $this;
    }

    /**
     * @return int
     */
    public function getRpgLevel()
    {
        $totalXp = $this->getXpTotal();
        return intval(explode(".", ($totalXp / 100))[0]);
    }

    /**
     * @return Collection|PatientSettings[]
     */
    public function getSettings(): Collection
    {
        return $this->settings;
    }

    /**
     * @return string|null
     */
    public function getSurName(): ?string
    {
        return $this->surName;
    }

    /**
     * @param string|null $surName
     *
     * @return $this
     */
    public function setSurName(?string $surName): self
    {
        $this->surName = $surName;

        return $this;
    }

    /**
     * @return Collection|TrackingDevice[]
     */
    public function getTrackingDevices(): Collection
    {
        return $this->trackingDevices;
    }

    /**
     * @return array|null
     */
    public function getUiSettings(): ?array
    {
        return $this->uiSettings;
    }

    /**
     * @param array|null $uiSettings
     *
     * @return $this
     */
    public function setUiSettings(?array $uiSettings): self
    {
        $this->uiSettings = $uiSettings;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return $this
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return Collection|RpgXP[]
     */
    public function getXp(): Collection
    {
        return $this->xp;
    }

    /**
     * @return float|int|null
     */
    public function getXpTotal()
    {
        $totalXp = 0;
        /** @var RpgXP $value */
        foreach ($this->xp as $value) {
            $totalXp = $totalXp + $value->getValue();
        }
        return $totalXp;
    }

    /**
     * @param Patient $friend
     *
     * @return bool
     */
    public function isFriendOf($friend): bool
    {

        return true;
    }

    /**
     * @param PatientDevice $device
     *
     * @return $this
     */
    public function removeDevice(PatientDevice $device): self
    {
        if ($this->devices->contains($device)) {
            $this->devices->removeElement($device);
            // set the owning side to null (unless already changed)
            if ($device->getPatient() === $this) {
                $device->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @param FitStepsDailySummary $fitStepsDailySummary
     *
     * @return $this
     */
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

    /**
     * @param PatientFriends $friendsOf
     *
     * @return $this
     */
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
     * @param PatientFriends $friendsToo
     *
     * @return $this
     */
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
     * @param SiteNews $notification
     *
     * @return $this
     */
    public function removeNotification(SiteNews $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getPatient() === $this) {
                $notification->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @param PatientCredentials $patientCredential
     *
     * @return $this
     */
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
     * @param RpgRewardsAwarded $reward
     *
     * @return $this
     */
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

    /**
     * @param RpgChallengeFriends $rpgChallenge
     *
     * @return $this
     */
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
     * @param RpgChallengeGlobalPatient $rpgChallengeGlobal
     *
     * @return $this
     */
    public function removeRpgChallengeGlobal(RpgChallengeGlobalPatient $rpgChallengeGlobal): self
    {
        if ($this->rpgChallengeGlobals->contains($rpgChallengeGlobal)) {
            $this->rpgChallengeGlobals->removeElement($rpgChallengeGlobal);
            // set the owning side to null (unless already changed)
            if ($rpgChallengeGlobal->getPatient() === $this) {
                $rpgChallengeGlobal->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @param RpgChallengeFriends $rpgChallenger
     *
     * @return $this
     */
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

    /**
     * @param PatientSettings $setting
     *
     * @return $this
     */
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

    /**
     * @param TrackingDevice $trackingDevice
     *
     * @return $this
     */
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
     * @param RpgXP $xp
     *
     * @return $this
     */
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
     * Helper method to create json string from entiry
     *
     * @return string|null
     */
    public function toJson(): ?string
    {
        $returnString = [];
        foreach (get_class_methods($this) as $classMethod) {
            unset($holdValue);
            if (substr($classMethod, 0, 3) === "get" && $classMethod != "getId" && $classMethod != "getRemoteId") {
                $methodValue = str_ireplace("get", "", $classMethod);
                $holdValue = $this->$classMethod();
                switch (gettype($holdValue)) {
                    case "string":
                    case "integer":
                        $returnString[$methodValue] = $holdValue;
                        break;
                    case "object":
                        switch (get_class($holdValue)) {
                            case "DateTime":
                                $returnString[$methodValue] = $holdValue->format("U");
                                break;
                            case "Ramsey\\Uuid\\Uuid":
                                /** @var $holdValue UuidInterface */
                                $returnString[$methodValue] = $holdValue->toString();
                                break;
                            default:
                                if (substr(get_class($holdValue), 0, strlen("App\Entity\\")) === "App\Entity\\") {
                                    $returnString[$methodValue] = $holdValue->getGuid();
                                } else {
                                    $returnString[$methodValue] = get_class($holdValue);
                                }
                                break;
                        }
                        break;
                    default:
                        $returnString[$methodValue] = gettype($holdValue);
                        break;
                }
            }
        }

        return json_encode($returnString);
    }
}
