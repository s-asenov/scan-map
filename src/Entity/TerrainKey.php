<?php

namespace App\Entity;

use App\Repository\TerrainKeyRepository;
use App\Util\MyHelper;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * The terrain key with which a user can parse in the Unity project.
 *
 * @ORM\Entity(repositoryClass=TerrainKeyRepository::class)
 * @ORM\Table(name="`terrain_keys`")
 */
class TerrainKey
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private string $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdOn;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $expiringOn;

    /**
     * @ORM\ManyToOne(targetEntity=Terrain::class, inversedBy="terrainKeys")
     * @ORM\JoinColumn(nullable=false)
     */
    private Terrain $terrain;

    public function __construct(int $id)
    {
        $now = new DateTime();
        $helper = new MyHelper();
        $formattedDate = $now->format('YmdHisv');

        $this->id = uniqid($formattedDate . $id) . "-" . $helper->randomStr();
        $this->createdOn = $now;
        $this->expiringOn = new DateTime("+1 hour");
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreatedOn(): ?DateTime
    {
        return $this->createdOn;
    }

    public function setCreatedOn(DateTime $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getExpiringOn(): ?DateTime
    {
        return $this->expiringOn;
    }

    public function setExpiringOn(DateTime $expiringOn): self
    {
        $this->expiringOn = $expiringOn;

        return $this;
    }


    public function getTerrain(): Terrain
    {
        return $this->terrain;
    }

    public function setTerrain(Terrain $terrain): self
    {
        $this->terrain = $terrain;

        return $this;
    }
    
}
