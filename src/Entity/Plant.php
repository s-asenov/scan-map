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
 * @ORM\Entity(repositoryClass=PlantRepository::class)
 * @ORM\Table(name="`plants`")
 */
class Plant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private ?string $scientificName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $commonName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $imageUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $modelPath;

    /**
     * @ORM\OneToMany(targetEntity=DistributionZonePlant::class, mappedBy="plant", cascade={"persist", "remove"})
     */
    private PersistentCollection|ArrayCollection $distributionZonesPlants;

    public function __construct()
    {
        $this->distributionZonesPlants = new ArrayCollection();
    }


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

    /**
     * @return Collection|DistributionZonePlant[]
     */
    public function getDistributionZonesPlants(): Collection
    {
        return $this->distributionZonesPlants;
    }

    public function addDistributionZonesPlant(DistributionZonePlant $distributionZonesPlant): self
    {
        if (!$this->distributionZonesPlants->contains($distributionZonesPlant)) {
            $this->distributionZonesPlants[] = $distributionZonesPlant;
            $distributionZonesPlant->setPlant($this);
        }

        return $this;
    }

    public function removeDistributionZonesPlant(DistributionZonePlant $distributionZonesPlant): self
    {
        if ($this->distributionZonesPlants->removeElement($distributionZonesPlant)) {
            // set the owning side to null (unless already changed)
            if ($distributionZonesPlant->getPlant() === $this) {
                $distributionZonesPlant->setPlant(null);
            }
        }

        return $this;
    }

}
