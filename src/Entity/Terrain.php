<?php

namespace App\Entity;

use App\Repository\TerrainRepository;
use App\Util\MyHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TerrainRepository::class)
 * @ORM\Table(name="`terrains`")
 */
class Terrain
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $zipName;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=TerrainKey::class, mappedBy="terrain", orphanRemoval=true)
     */
    private $terrainKeys;

    public function __construct(User $user)
    {
        $helper = new MyHelper();
        $now = new \DateTime();
        $formattedDate = $now->format('YmdHisv');

        do {
            $name = $user->getId() . "-" . uniqid($formattedDate) . "-" . $helper->random_str();
            $this->zipName = $name;
        } while (file_exists('zip/' . $name));

        $this->user = $user;
        $this->terrainKeys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZipName(): ?string
    {
        return $this->zipName;
    }

    public function setZipName(string $zipName): self
    {
        $this->zipName = $zipName;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|TerrainKey[]
     */
    public function getTerrainKeys(): Collection
    {
        return $this->terrainKeys;
    }

    public function addTerrainKey(TerrainKey $terrainKey): self
    {
        if (!$this->terrainKeys->contains($terrainKey)) {
            $this->terrainKeys[] = $terrainKey;
            $terrainKey->setTerrain($this);
        }

        return $this;
    }

    public function removeTerrainKey(TerrainKey $terrainKey): self
    {
        if ($this->terrainKeys->removeElement($terrainKey)) {
            // set the owning side to null (unless already changed)
            if ($terrainKey->getTerrain() === $this) {
                $terrainKey->setTerrain(null);
            }
        }

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
}
