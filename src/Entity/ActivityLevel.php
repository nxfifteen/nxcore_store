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
 * @ORM\Entity(repositoryClass="App\Repository\ActivityLevelRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="UniqueReading", columns={"sedentary","lightly","fairly","very"})})
 *
 * @ApiResource
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "sedentary": "exact", "lightly": "exact", "fairly": "exact", "very": "exact"})
 */
class ActivityLevel
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $sedentary;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $lightly;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $fairly;

    /**
     * @ORM\Column(type="integer", length=3, nullable=true)
     */
    private $very;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSedentary(): ?int
    {
        return $this->sedentary;
    }

    public function setSedentary(?int $sedentary): self
    {
        $this->sedentary = $sedentary;

        return $this;
    }

    public function getLightly(): ?int
    {
        return $this->lightly;
    }

    public function setLightly(?int $lightly): self
    {
        $this->lightly = $lightly;

        return $this;
    }

    public function getFairly(): ?int
    {
        return $this->fairly;
    }

    public function setFairly(?int $fairly): self
    {
        $this->fairly = $fairly;

        return $this;
    }

    public function getVery(): ?int
    {
        return $this->very;
    }

    public function setVery(?int $very): self
    {
        $this->very = $very;

        return $this;
    }
}