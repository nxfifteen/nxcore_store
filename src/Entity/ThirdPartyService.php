<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ThirdPartyServiceRepository")
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "name": "exact"})
 */
class ThirdPartyService
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $member_since;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMemberSince(): ?\DateTimeInterface
    {
        return $this->member_since;
    }

    public function setMemberSince(?\DateTimeInterface $member_since): self
    {
        $this->member_since = $member_since;

        return $this;
    }
}