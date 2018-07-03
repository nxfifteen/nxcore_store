<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 */
class Patient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BodyWeight", mappedBy="patient", orphanRemoval=true)
     */
    private $bodyWeights;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BodyFat", mappedBy="patient", orphanRemoval=true)
     */
    private $bodyFats;

    public function __construct()
    {
        $this->bodyWeights = new ArrayCollection();
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

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
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
}
