<?php

namespace App\Entity;

use App\Repository\TerrainRepository;
use App\Util\MyHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * The terrain entity contains user`s generated terrain in the map section of the website.
 * @link https://flora.noit.eu/map
 *
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
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $zipName;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $name;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $user;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $imageDirectory;

    /**
     * @ORM\OneToMany(targetEntity=TerrainKey::class, mappedBy="terrain", orphanRemoval=true)
     */
    private PersistentCollection|ArrayCollection $terrainKeys;

    public function __construct(User $user)
    {
        $helper = new MyHelper();
        $now = new \DateTime();
        $formattedDate = $now->format('YmdHisv');

        do {
            $name = $user->getId() . "-" . uniqid($formattedDate) . "-" . $helper->randomStr();
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

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection
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

    public function getImageDirectory(): ?string
    {
        return $this->imageDirectory;
    }

    public function setImageDirectory(string $imageDirectory): self
    {
        $this->imageDirectory = $imageDirectory;

        return $this;
    }
}
