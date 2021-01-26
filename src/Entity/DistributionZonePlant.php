<?php

namespace App\Entity;

use App\Repository\DistributionZonePlantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * The entity adding the plant to the specific zone it is found.
 *
 * @ORM\Entity(repositoryClass=DistributionZonePlantRepository::class)
 * @UniqueEntity(fields={"distributionZone", "plant"})
 */
class DistributionZonePlant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=DistributionZone::class, inversedBy="distributionZonePlants", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $distributionZone;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class, fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDistributionZone(): ?DistributionZone
    {
        return $this->distributionZone;
    }

    public function setDistributionZone(?DistributionZone $distributionZone): self
    {
        $this->distributionZone = $distributionZone;

        return $this;
    }

    public function getPlant(): ?Plant
    {
        return $this->plant;
    }

    public function setPlant(?Plant $plant): self
    {
        $this->plant = $plant;

        return $this;
    }
}
