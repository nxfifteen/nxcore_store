<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ThirdPartyRelationsRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="RemoteUserName", columns={"patient_id","username"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "username": "exact", "patient": "exact"})
 */
class ThirdPartyRelations
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $username;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="thirdPartyRelations")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     */
    private $patient;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

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
}