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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 *
 * @ApiFilter(SearchFilter::class, properties={"name": "exact"})
 * @ORM\Entity(repositoryClass="App\Repository\ThirdPartyServiceRepository")
 */
class ThirdPartyService
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrackingDevice", mappedBy="service", orphanRemoval=true)
     */
    private $trackingDevices;

    /**
     * ThirdPartyService constructor.
     */
    public function __construct()
    {
        $this->trackingDevices = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

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
     * @param TrackingDevice $trackingDevice
     *
     * @return $this
     */
    public function addTrackingDevice(TrackingDevice $trackingDevice): self
    {
        if (!$this->trackingDevices->contains($trackingDevice)) {
            $this->trackingDevices[] = $trackingDevice;
            $trackingDevice->setService($this);
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
            if ($trackingDevice->getService() === $this) {
                $trackingDevice->setService(null);
            }
        }

        return $this;
    }
}
