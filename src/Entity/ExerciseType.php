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

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseTypeRepository")
 */
class ExerciseType
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
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tag;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $MET;

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
     * @return float|null
     */
    public function getMET(): ?float
    {
        return $this->MET;
    }

    /**
     * @param float|null $MET
     *
     * @return $this
     */
    public function setMET(?float $MET): self
    {
        $this->MET = $MET;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @param string|null $tag
     *
     * @return $this
     */
    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

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
