<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 *
 * @ApiResource
 */
class Patient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lname;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $timezone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BodyWeight", mappedBy="patient", orphanRemoval=true)
     */
    private $bodyWeights;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CountDailyStep", mappedBy="patient")
     */
    private $stepCount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CountDailyFloor", mappedBy="patient")
     */
    private $floorCount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ThirdPartyRelations", mappedBy="patient")
     */
    private $thirdPartyRelations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BodyBmi", mappedBy="patient")
     */
    private $bodyBmi;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CountDailyDistance", mappedBy="patient")
     */
    private $distanceCount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CountDailyCalories", mappedBy="patient")
     * 
     * 
     * 
     * 
     */
    private $calorieCount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CountDailyElevation", mappedBy="patient")
     */
    private $elevationCount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NutritionInformation", mappedBy="patient")
     */
    private $nutritionInformation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrackingDevice", mappedBy="patient")
     */
    private $trackingDevice;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SleepEpisode", mappedBy="patient")
     */
    private $sleepEpisode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SportActivity", mappedBy="patient")
     */
    private $sportActivity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BodyFat", mappedBy="patient", orphanRemoval=true)
     */
    private $bodyFats;

    public function __construct()
    {
        $this->bodyWeights = new ArrayCollection();
        $this->stepCount = new ArrayCollection();
        $this->floorCount = new ArrayCollection();
        $this->bodyFats = new ArrayCollection();
        $this->thirdPartyRelations = new ArrayCollection();
        $this->bodyBmi = new ArrayCollection();
        $this->distanceCount = new ArrayCollection();
        $this->calorieCount = new ArrayCollection();
        $this->elevationCount = new ArrayCollection();
        $this->nutritionInformation = new ArrayCollection();
        $this->sportActivity = new ArrayCollection();
        $this->trackingDevice = new ArrayCollection();
        $this->sleepEpisode = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFname(): ?string
    {
        return $this->fname;
    }

    public function setFname(?string $fname): self
    {
        $this->fname = $fname;

        return $this;
    }

    public function getLname(): ?string
    {
        return $this->lname;
    }

    public function setLname(?string $lname): self
    {
        $this->lname = $lname;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

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

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        if (array_key_exists("DATABASE_SALT", $_ENV)) {
            $dbSalt = $_ENV['DATABASE_SALT'];
        } else {
            $dbSalt = '$0m3 $4lt 1$ b3tt3r th4n n0n3, but y0u y0u r34lly $h0uld h4v3 4 DATABASE_SALT 3nv v4r14bl3';
        }

        $this->password = hash("sha256", $dbSalt . $password);

        return $this;
    }

    /**
     * @return Collection|BodyWeight[]
     */
    public function getBodyWeights(): Collection
    {
        return $this->bodyWeights;
    }

    public function addBodyWeight(BodyWeight $bodyWeight): self
    {
        if (!$this->bodyWeights->contains($bodyWeight)) {
            $this->bodyWeights[] = $bodyWeight;
            $bodyWeight->setPatient($this);
        }

        return $this;
    }

    public function removeBodyWeight(BodyWeight $bodyWeight): self
    {
        if ($this->bodyWeights->contains($bodyWeight)) {
            $this->bodyWeights->removeElement($bodyWeight);
            // set the owning side to null (unless already changed)
            if ($bodyWeight->getPatient() === $this) {
                $bodyWeight->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BodyFat[]
     */
    public function getBodyFats(): Collection
    {
        return $this->bodyFats;
    }

    public function addBodyFat(BodyFat $bodyFat): self
    {
        if (!$this->bodyFats->contains($bodyFat)) {
            $this->bodyFats[] = $bodyFat;
            $bodyFat->setPatient($this);
        }

        return $this;
    }

    public function removeBodyFat(BodyFat $bodyFat): self
    {
        if ($this->bodyFats->contains($bodyFat)) {
            $this->bodyFats->removeElement($bodyFat);
            // set the owning side to null (unless already changed)
            if ($bodyFat->getPatient() === $this) {
                $bodyFat->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CountDailyStep[]
     */
    public function getStepCount(): Collection
    {
        return $this->stepCount;
    }

    public function addStepCount(CountDailyStep $stepCount): self
    {
        if (!$this->stepCount->contains($stepCount)) {
            $this->stepCount[] = $stepCount;
            $stepCount->setPatient($this);
        }

        return $this;
    }

    public function removeStepCount(CountDailyStep $stepCount): self
    {
        if ($this->stepCount->contains($stepCount)) {
            $this->stepCount->removeElement($stepCount);
            // set the owning side to null (unless already changed)
            if ($stepCount->getPatient() === $this) {
                $stepCount->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CountDailyFloor[]
     */
    public function getFloorCount(): Collection
    {
        return $this->floorCount;
    }

    public function addFloorCount(CountDailyFloor $floorCount): self
    {
        if (!$this->floorCount->contains($floorCount)) {
            $this->floorCount[] = $floorCount;
            $floorCount->setPatient($this);
        }

        return $this;
    }

    public function removeFloorCount(CountDailyFloor $floorCount): self
    {
        if ($this->floorCount->contains($floorCount)) {
            $this->floorCount->removeElement($floorCount);
            // set the owning side to null (unless already changed)
            if ($floorCount->getPatient() === $this) {
                $floorCount->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ThirdPartyRelations[]
     */
    public function getThirdPartyRelations(): Collection
    {
        return $this->thirdPartyRelations;
    }

    public function addThirdPartyRelation(ThirdPartyRelations $thirdPartyRelation): self
    {
        if (!$this->thirdPartyRelations->contains($thirdPartyRelation)) {
            $this->thirdPartyRelations[] = $thirdPartyRelation;
            $thirdPartyRelation->setPatient($this);
        }

        return $this;
    }

    public function removeThirdPartyRelation(ThirdPartyRelations $thirdPartyRelation): self
    {
        if ($this->thirdPartyRelations->contains($thirdPartyRelation)) {
            $this->thirdPartyRelations->removeElement($thirdPartyRelation);
            // set the owning side to null (unless already changed)
            if ($thirdPartyRelation->getPatient() === $this) {
                $thirdPartyRelation->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BodyBmi[]
     */
    public function getBodyBmi(): Collection
    {
        return $this->bodyBmi;
    }

    public function addBodyBmi(BodyBmi $bodyBmi): self
    {
        if (!$this->bodyBmi->contains($bodyBmi)) {
            $this->bodyBmi[] = $bodyBmi;
            $bodyBmi->setPatient($this);
        }

        return $this;
    }

    public function removeBodyBmi(BodyBmi $bodyBmi): self
    {
        if ($this->bodyBmi->contains($bodyBmi)) {
            $this->bodyBmi->removeElement($bodyBmi);
            // set the owning side to null (unless already changed)
            if ($bodyBmi->getPatient() === $this) {
                $bodyBmi->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CountDailyDistance[]
     */
    public function getDistanceCount(): Collection
    {
        return $this->distanceCount;
    }

    public function addDistanceCount(CountDailyDistance $distanceCount): self
    {
        if (!$this->distanceCount->contains($distanceCount)) {
            $this->distanceCount[] = $distanceCount;
            $distanceCount->setPatient($this);
        }

        return $this;
    }

    public function removeDistanceCount(CountDailyDistance $distanceCount): self
    {
        if ($this->distanceCount->contains($distanceCount)) {
            $this->distanceCount->removeElement($distanceCount);
            // set the owning side to null (unless already changed)
            if ($distanceCount->getPatient() === $this) {
                $distanceCount->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CountDailyCalories[]
     */
    public function getCalorieCount(): Collection
    {
        return $this->calorieCount;
    }

    public function addCalorieCount(CountDailyCalories $calorieCount): self
    {
        if (!$this->calorieCount->contains($calorieCount)) {
            $this->calorieCount[] = $calorieCount;
            $calorieCount->setPatient($this);
        }

        return $this;
    }

    public function removeCalorieCount(CountDailyCalories $calorieCount): self
    {
        if ($this->calorieCount->contains($calorieCount)) {
            $this->calorieCount->removeElement($calorieCount);
            // set the owning side to null (unless already changed)
            if ($calorieCount->getPatient() === $this) {
                $calorieCount->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CountDailyElevation[]
     */
    public function getElevationCount(): Collection
    {
        return $this->elevationCount;
    }

    public function addElevationCount(CountDailyElevation $elevationCount): self
    {
        if (!$this->elevationCount->contains($elevationCount)) {
            $this->elevationCount[] = $elevationCount;
            $elevationCount->setPatient($this);
        }

        return $this;
    }

    public function removeElevationCount(CountDailyElevation $elevationCount): self
    {
        if ($this->elevationCount->contains($elevationCount)) {
            $this->elevationCount->removeElement($elevationCount);
            // set the owning side to null (unless already changed)
            if ($elevationCount->getPatient() === $this) {
                $elevationCount->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|NutritionInformation[]
     */
    public function getNutritionInformation(): Collection
    {
        return $this->nutritionInformation;
    }

    public function addNutritionInformation(NutritionInformation $nutritionInformation): self
    {
        if (!$this->nutritionInformation->contains($nutritionInformation)) {
            $this->nutritionInformation[] = $nutritionInformation;
            $nutritionInformation->setPatient($this);
        }

        return $this;
    }

    public function removeNutritionInformation(NutritionInformation $nutritionInformation): self
    {
        if ($this->nutritionInformation->contains($nutritionInformation)) {
            $this->nutritionInformation->removeElement($nutritionInformation);
            // set the owning side to null (unless already changed)
            if ($nutritionInformation->getPatient() === $this) {
                $nutritionInformation->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SportActivity[]
     */
    public function getSportActivity(): Collection
    {
        return $this->sportActivity;
    }

    public function addSportActivity(SportActivity $sportActivity): self
    {
        if (!$this->sportActivity->contains($sportActivity)) {
            $this->sportActivity[] = $sportActivity;
            $sportActivity->setPatient($this);
        }

        return $this;
    }

    public function removeSportActivity(SportActivity $sportActivity): self
    {
        if ($this->sportActivity->contains($sportActivity)) {
            $this->sportActivity->removeElement($sportActivity);
            // set the owning side to null (unless already changed)
            if ($sportActivity->getPatient() === $this) {
                $sportActivity->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrackingDevice[]
     */
    public function getTrackingDevice(): Collection
    {
        return $this->trackingDevice;
    }

    public function addTrackingDevice(TrackingDevice $trackingDevice): self
    {
        if (!$this->trackingDevice->contains($trackingDevice)) {
            $this->trackingDevice[] = $trackingDevice;
            $trackingDevice->setPatient($this);
        }

        return $this;
    }

    public function removeTrackingDevice(TrackingDevice $trackingDevice): self
    {
        if ($this->trackingDevice->contains($trackingDevice)) {
            $this->trackingDevice->removeElement($trackingDevice);
            // set the owning side to null (unless already changed)
            if ($trackingDevice->getPatient() === $this) {
                $trackingDevice->setPatient(null);
            }
        }

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

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

    /**
     * @return Collection|SleepEpisode[]
     */
    public function getSleepEpisode(): Collection
    {
        return $this->sleepEpisode;
    }

    public function addSleepEpisode(SleepEpisode $sleepEpisode): self
    {
        if (!$this->sleepEpisode->contains($sleepEpisode)) {
            $this->sleepEpisode[] = $sleepEpisode;
            $sleepEpisode->setPatient($this);
        }

        return $this;
    }

    public function removeSleepEpisode(SleepEpisode $sleepEpisode): self
    {
        if ($this->sleepEpisode->contains($sleepEpisode)) {
            $this->sleepEpisode->removeElement($sleepEpisode);
            // set the owning side to null (unless already changed)
            if ($sleepEpisode->getPatient() === $this) {
                $sleepEpisode->setPatient(null);
            }
        }

        return $this;
    }
}
