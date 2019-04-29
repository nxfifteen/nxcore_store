<?php

    namespace App\Entity;

    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use /** @noinspection PhpUnusedAliasInspection */ Doctrine\ORM\Mapping as ORM;
    use /** @noinspection PhpUnusedAliasInspection */ ApiPlatform\Core\Annotation\ApiResource;
    use /** @noinspection PhpUnusedAliasInspection */ ApiPlatform\Core\Annotation\ApiFilter;
    use /** @noinspection PhpUnusedAliasInspection */ ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
    use Symfony\Component\Security\Core\User\UserInterface;

    /**
     * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
     *
     * @ApiResource
     * @ApiFilter(SearchFilter::class, properties={"id": "exact", "uuid": "exact"})
     */
    class Patient implements UserInterface
    {
        /**
         * @ORM\Id
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
         * @ORM\Column(type="string", nullable=true)
         */
        private $password;

        /**
         * @ORM\Column(type="integer", length=6, nullable=true)
         */
        private $stepGoal;

        /**
         * @ORM\Column(type="integer", length=4, nullable=true)
         */
        private $floorGoal;

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

        /**
         * @ORM\OneToMany(targetEntity="App\Entity\WaterIntake", mappedBy="patient", orphanRemoval=true)
         */
        private $waterIntakes;

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
            $this->waterIntakes = new ArrayCollection();
        }

        public function getId()
        {
            return $this->id;
        }

        public function getFname(): ?string
        {
            return $this->fname;
        }

        public function setFname( ?string $fname ): self
        {
            $this->fname = $fname;

            return $this;
        }

        public function getLname(): ?string
        {
            return $this->lname;
        }

        public function setLname( ?string $lname ): self
        {
            $this->lname = $lname;

            return $this;
        }

        public function getBirthday(): ?\DateTimeInterface
        {
            return $this->birthday;
        }

        public function setBirthday( ?\DateTimeInterface $birthday ): self
        {
            $this->birthday = $birthday;

            return $this;
        }

        public function getHeight(): ?float
        {
            return $this->height;
        }

        public function setHeight( ?float $height ): self
        {
            $this->height = $height;

            return $this;
        }

        public function getEmail(): ?string
        {
            return $this->email;
        }

        public function setEmail( string $email ): self
        {
            $this->email = $email;

            return $this;
        }

        public function getGender(): ?string
        {
            return $this->gender;
        }

        public function setGender( ?string $gender ): self
        {
            $this->gender = $gender;

            return $this;
        }

        public function getStepGoal(): ?int
        {
            if ( is_null($this->stepGoal) ) {
                $this->stepGoal = 10000;
            }
            return $this->stepGoal;
        }

        public function setStepGoal( ?int $stepGoal ): self
        {
            $this->stepGoal = $stepGoal;

            return $this;
        }

        public function getFloorGoal(): ?int
        {
            if ( is_null($this->floorGoal) ) {
                $this->floorGoal = 16;
            }
            return $this->floorGoal;
        }

        public function setFloorGoal( ?int $floorGoal ): self
        {
            $this->floorGoal = $floorGoal;

            return $this;
        }

        public function getPassword(): ?string
        {
            return $this->password;
        }

        public function setPassword( string $password ): self
        {
            if ( array_key_exists("DATABASE_SALT", $_ENV) ) {
                $dbSalt = $_ENV[ 'DATABASE_SALT' ];
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

        public function addBodyWeight( BodyWeight $bodyWeight ): self
        {
            if ( !$this->bodyWeights->contains($bodyWeight) ) {
                $this->bodyWeights[] = $bodyWeight;
                $bodyWeight->setPatient($this);
            }

            return $this;
        }

        public function removeBodyWeight( BodyWeight $bodyWeight ): self
        {
            if ( $this->bodyWeights->contains($bodyWeight) ) {
                $this->bodyWeights->removeElement($bodyWeight);
                // set the owning side to null (unless already changed)
                if ( $bodyWeight->getPatient() === $this ) {
                    $bodyWeight->setPatient(NULL);
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

        public function addBodyFat( BodyFat $bodyFat ): self
        {
            if ( !$this->bodyFats->contains($bodyFat) ) {
                $this->bodyFats[] = $bodyFat;
                $bodyFat->setPatient($this);
            }

            return $this;
        }

        public function removeBodyFat( BodyFat $bodyFat ): self
        {
            if ( $this->bodyFats->contains($bodyFat) ) {
                $this->bodyFats->removeElement($bodyFat);
                // set the owning side to null (unless already changed)
                if ( $bodyFat->getPatient() === $this ) {
                    $bodyFat->setPatient(NULL);
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

        public function addStepCount( CountDailyStep $stepCount ): self
        {
            if ( !$this->stepCount->contains($stepCount) ) {
                $this->stepCount[] = $stepCount;
                $stepCount->setPatient($this);
            }

            return $this;
        }

        public function removeStepCount( CountDailyStep $stepCount ): self
        {
            if ( $this->stepCount->contains($stepCount) ) {
                $this->stepCount->removeElement($stepCount);
                // set the owning side to null (unless already changed)
                if ( $stepCount->getPatient() === $this ) {
                    $stepCount->setPatient(NULL);
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

        public function addFloorCount( CountDailyFloor $floorCount ): self
        {
            if ( !$this->floorCount->contains($floorCount) ) {
                $this->floorCount[] = $floorCount;
                $floorCount->setPatient($this);
            }

            return $this;
        }

        public function removeFloorCount( CountDailyFloor $floorCount ): self
        {
            if ( $this->floorCount->contains($floorCount) ) {
                $this->floorCount->removeElement($floorCount);
                // set the owning side to null (unless already changed)
                if ( $floorCount->getPatient() === $this ) {
                    $floorCount->setPatient(NULL);
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

        public function addThirdPartyRelation( ThirdPartyRelations $thirdPartyRelation ): self
        {
            if ( !$this->thirdPartyRelations->contains($thirdPartyRelation) ) {
                $this->thirdPartyRelations[] = $thirdPartyRelation;
                $thirdPartyRelation->setPatient($this);
            }

            return $this;
        }

        public function removeThirdPartyRelation( ThirdPartyRelations $thirdPartyRelation ): self
        {
            if ( $this->thirdPartyRelations->contains($thirdPartyRelation) ) {
                $this->thirdPartyRelations->removeElement($thirdPartyRelation);
                // set the owning side to null (unless already changed)
                if ( $thirdPartyRelation->getPatient() === $this ) {
                    $thirdPartyRelation->setPatient(NULL);
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

        public function addBodyBmi( BodyBmi $bodyBmi ): self
        {
            if ( !$this->bodyBmi->contains($bodyBmi) ) {
                $this->bodyBmi[] = $bodyBmi;
                $bodyBmi->setPatient($this);
            }

            return $this;
        }

        public function removeBodyBmi( BodyBmi $bodyBmi ): self
        {
            if ( $this->bodyBmi->contains($bodyBmi) ) {
                $this->bodyBmi->removeElement($bodyBmi);
                // set the owning side to null (unless already changed)
                if ( $bodyBmi->getPatient() === $this ) {
                    $bodyBmi->setPatient(NULL);
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

        public function addDistanceCount( CountDailyDistance $distanceCount ): self
        {
            if ( !$this->distanceCount->contains($distanceCount) ) {
                $this->distanceCount[] = $distanceCount;
                $distanceCount->setPatient($this);
            }

            return $this;
        }

        public function removeDistanceCount( CountDailyDistance $distanceCount ): self
        {
            if ( $this->distanceCount->contains($distanceCount) ) {
                $this->distanceCount->removeElement($distanceCount);
                // set the owning side to null (unless already changed)
                if ( $distanceCount->getPatient() === $this ) {
                    $distanceCount->setPatient(NULL);
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

        public function addCalorieCount( CountDailyCalories $calorieCount ): self
        {
            if ( !$this->calorieCount->contains($calorieCount) ) {
                $this->calorieCount[] = $calorieCount;
                $calorieCount->setPatient($this);
            }

            return $this;
        }

        public function removeCalorieCount( CountDailyCalories $calorieCount ): self
        {
            if ( $this->calorieCount->contains($calorieCount) ) {
                $this->calorieCount->removeElement($calorieCount);
                // set the owning side to null (unless already changed)
                if ( $calorieCount->getPatient() === $this ) {
                    $calorieCount->setPatient(NULL);
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

        public function addElevationCount( CountDailyElevation $elevationCount ): self
        {
            if ( !$this->elevationCount->contains($elevationCount) ) {
                $this->elevationCount[] = $elevationCount;
                $elevationCount->setPatient($this);
            }

            return $this;
        }

        public function removeElevationCount( CountDailyElevation $elevationCount ): self
        {
            if ( $this->elevationCount->contains($elevationCount) ) {
                $this->elevationCount->removeElement($elevationCount);
                // set the owning side to null (unless already changed)
                if ( $elevationCount->getPatient() === $this ) {
                    $elevationCount->setPatient(NULL);
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

        public function addNutritionInformation( NutritionInformation $nutritionInformation ): self
        {
            if ( !$this->nutritionInformation->contains($nutritionInformation) ) {
                $this->nutritionInformation[] = $nutritionInformation;
                $nutritionInformation->setPatient($this);
            }

            return $this;
        }

        public function removeNutritionInformation( NutritionInformation $nutritionInformation ): self
        {
            if ( $this->nutritionInformation->contains($nutritionInformation) ) {
                $this->nutritionInformation->removeElement($nutritionInformation);
                // set the owning side to null (unless already changed)
                if ( $nutritionInformation->getPatient() === $this ) {
                    $nutritionInformation->setPatient(NULL);
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

        public function addSportActivity( SportActivity $sportActivity ): self
        {
            if ( !$this->sportActivity->contains($sportActivity) ) {
                $this->sportActivity[] = $sportActivity;
                $sportActivity->setPatient($this);
            }

            return $this;
        }

        public function removeSportActivity( SportActivity $sportActivity ): self
        {
            if ( $this->sportActivity->contains($sportActivity) ) {
                $this->sportActivity->removeElement($sportActivity);
                // set the owning side to null (unless already changed)
                if ( $sportActivity->getPatient() === $this ) {
                    $sportActivity->setPatient(NULL);
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

        public function addTrackingDevice( TrackingDevice $trackingDevice ): self
        {
            if ( !$this->trackingDevice->contains($trackingDevice) ) {
                $this->trackingDevice[] = $trackingDevice;
                $trackingDevice->setPatient($this);
            }

            return $this;
        }

        public function removeTrackingDevice( TrackingDevice $trackingDevice ): self
        {
            if ( $this->trackingDevice->contains($trackingDevice) ) {
                $this->trackingDevice->removeElement($trackingDevice);
                // set the owning side to null (unless already changed)
                if ( $trackingDevice->getPatient() === $this ) {
                    $trackingDevice->setPatient(NULL);
                }
            }

            return $this;
        }

        public function getTimezone(): ?string
        {
            return $this->timezone;
        }

        public function setTimezone( ?string $timezone ): self
        {
            $this->timezone = $timezone;

            return $this;
        }

        public function getAvatar(): ?string
        {
            return $this->avatar;
        }

        public function setAvatar( ?string $avatar ): self
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

        public function addSleepEpisode( SleepEpisode $sleepEpisode ): self
        {
            if ( !$this->sleepEpisode->contains($sleepEpisode) ) {
                $this->sleepEpisode[] = $sleepEpisode;
                $sleepEpisode->setPatient($this);
            }

            return $this;
        }

        public function removeSleepEpisode( SleepEpisode $sleepEpisode ): self
        {
            if ( $this->sleepEpisode->contains($sleepEpisode) ) {
                $this->sleepEpisode->removeElement($sleepEpisode);
                // set the owning side to null (unless already changed)
                if ( $sleepEpisode->getPatient() === $this ) {
                    $sleepEpisode->setPatient(NULL);
                }
            }

            return $this;
        }

        /**
         * @return Collection|WaterIntake[]
         */
        public function getWaterIntakes(): Collection
        {
            return $this->waterIntakes;
        }

        public function addWaterIntake( WaterIntake $waterIntake ): self
        {
            if ( !$this->waterIntakes->contains($waterIntake) ) {
                $this->waterIntakes[] = $waterIntake;
                $waterIntake->setPatient($this);
            }

            return $this;
        }

        public function removeWaterIntake( WaterIntake $waterIntake ): self
        {
            if ( $this->waterIntakes->contains($waterIntake) ) {
                $this->waterIntakes->removeElement($waterIntake);
                // set the owning side to null (unless already changed)
                if ( $waterIntake->getPatient() === $this ) {
                    $waterIntake->setPatient(NULL);
                }
            }

            return $this;
        }

        /**
         * @ORM\Column(type="string", unique=true)
         */
        private $apiToken;

        public function getApiToken(): ?string
        {
            return $this->apiToken;
        }

        public function setApiToken( string $apiToken ): self
        {
            $this->apiToken = $apiToken;

            return $this;
        }

        public function getUuid(): ?string
        {
            return $this->uuid;
        }

        public function setUuid( string $uuid ): self
        {
            $this->uuid = $uuid;

            return $this;
        }

        /**
         * Returns the username used to authenticate the user.
         *
         * @return string The username
         */
        public function getUsername(): string
        {
            return (string)$this->uuid;
        }

        /**
         * Returns the roles granted to the user.
         *
         *     public function getRoles()
         *     {
         *         return ['ROLE_USER'];
         *     }
         *
         * Alternatively, the roles might be stored on a ``roles`` property,
         * and populated in any number of different ways when the user object
         * is created.
         *
         * @return (Role|string)[] The user roles
         */
        public function getRoles(): array
        {
            $roles = $this->roles;
            // guarantee every user at least has ROLE_USER
            $roles[] = 'ROLE_USER';

            return array_unique($roles);
        }

        public function setRoles( array $roles ): self
        {
            $this->roles = $roles;

            return $this;
        }

        /**
         * Returns the salt that was originally used to encode the password.
         *
         * This can return null if the password was not encoded using a salt.
         *
         * @return string|null The salt
         */
        public function getSalt()
        {
            // TODO: Implement getSalt() method.
        }

        /**
         * Removes sensitive data from the user.
         *
         * This is important if, at any given point, sensitive information like
         * the plain-text password is stored on this object.
         */
        public function eraseCredentials()
        {
            // TODO: Implement eraseCredentials() method.
        }
    }
