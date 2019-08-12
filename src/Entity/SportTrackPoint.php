<?php

/*
* This file is part of the Storage module in NxFIFTEEN Core.
*
* Copyright (c) 2019. Stuart McCulloch Anderson
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*
* @package     Store
* @version     0.0.0.x
* @since       0.0.0.1
* @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
* @link        https://nxfifteen.me.uk NxFIFTEEN
* @link        https://git.nxfifteen.rocks/nx-health NxFIFTEEN Core
* @link        https://git.nxfifteen.rocks/nx-health/store NxFIFTEEN Core Storage
* @copyright   2019 Stuart McCulloch Anderson
* @license     https://license.nxfifteen.rocks/mit/2015-2019/ MIT
*/

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SportTrackPointRepository")
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "time": "exact", "lat": "exact", "lon": "exact", "sportTrack": "exact"})
 */
class SportTrackPoint
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $lon;

    /**
     * @ORM\Column(type="string", length=18, nullable=true)
     */
    private $altitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $distrance;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $heart_rate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SportTrack")
     * @ORM\JoinColumn(name="sport_track_id", referencedColumnName="id")
     */
    private $sportTrack;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?string
    {
        return $this->lon;
    }

    public function setLon(?string $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getAltitude()
    {
        return $this->altitude;
    }

    public function setAltitude($altitude): self
    {
        $this->altitude = $altitude;

        return $this;
    }

    public function getHeartRate(): ?int
    {
        return $this->heart_rate;
    }

    public function setHeartRate(?int $heart_rate): self
    {
        $this->heart_rate = $heart_rate;

        return $this;
    }

    public function getSportTrack(): ?SportTrack
    {
        return $this->sportTrack;
    }

    public function setSportTrack(?SportTrack $sportTrack): self
    {
        $this->sportTrack = $sportTrack;

        return $this;
    }

    public function getDistrance(): ?float
    {
        return $this->distrance;
    }

    public function setDistrance(?float $distrance): self
    {
        $this->distrance = $distrance;

        return $this;
    }
}