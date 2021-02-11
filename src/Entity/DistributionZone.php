<?php

namespace App\Entity;

use App\Repository\DistributionZoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Distribution Zones are part of the WGSRPD convention.
 * Every zone has its unique id used in the trefle API.
 *
 * @link https://github.com/tdwg/wgsrpd
 * @link https://trefle.io/
 *
 * @ORM\Entity(repositoryClass=DistributionZoneRepository::class)
 * @ORM\Table(name="`distribution_zones`")
 */
class DistributionZone
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=DistributionZone::class, inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=DistributionZone::class, mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity=DistributionZonePlant::class, mappedBy="distributionZone", orphanRemoval=true)
     */
    private $distributionZonePlants;

    /**
     * @ORM\Column(type="integer")
     */
    private $fetched;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->distributionZonePlants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DistributionZonePlant[]
     */
    public function getDistributionZonePlants(): Collection
    {
        return $this->distributionZonePlants;
    }

    public function addDistributionZonePlant(DistributionZonePlant $distributionZonePlant): self
    {
        if (!$this->distributionZonePlants->contains($distributionZonePlant)) {
            $this->distributionZonePlants[] = $distributionZonePlant;
            $distributionZonePlant->setDistributionZone($this);
        }

        return $this;
    }

    public function removeDistributionZonePlant(DistributionZonePlant $distributionZonePlant): self
    {
        if ($this->distributionZonePlants->removeElement($distributionZonePlant)) {
            // set the owning side to null (unless already changed)
            if ($distributionZonePlant->getDistributionZone() === $this) {
                $distributionZonePlant->setDistributionZone(null);
            }
        }

        return $this;
    }

    public function getFetched(): ?int
    {
        return $this->fetched;
    }

    public function setFetched(int $fetched): self
    {
        $this->fetched = $fetched;

        return $this;
    }

    public function incrementFetched(): self
    {
        $this->fetched++;

        return $this;
    }
}
