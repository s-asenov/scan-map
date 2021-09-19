<?php

namespace App\Entity;

use App\Repository\PlantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * The entity responsible for holding the necessary information about the plant.
 *
 * @ORM\Entity(repositoryClass=PlantSyncRepository::class)
 * @ORM\Table(name="`plants_sync`")
 */
class PlantSync
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $scientificName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $commonName = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $imageUrl = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $modelPath = null;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private ?string $distributionZone = null;

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function __isset($prop) : bool
    {
        return isset($this->$prop);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommonName(): ?string
    {
        return $this->commonName;
    }

    public function setCommonName(?string $commonName): self
    {
        $this->commonName = $commonName;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    public function setScientificName(string $scientificName): self
    {
        $this->scientificName = $scientificName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getModelPath(): ?string
    {
        return $this->modelPath;
    }

    public function setModelPath(?string $modelPath): self
    {
        $this->modelPath = $modelPath;

        return $this;
    }

    public function getDistributionZone(): ?int
    {
        return $this->distributionZone;
    }

    public function setDistributionZone(int $zone): self
    {
        $this->distributionZone = $zone;

        return $this;
    }
}
