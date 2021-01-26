<?php

namespace App\Entity;

use App\Repository\PlantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * The entity responsible for holding the necessary information about the plant.
 *
 * @ORM\Entity(repositoryClass=PlantRepository::class)
 * @ORM\Table(name="`plants`")
 * @UniqueEntity(fields={"scientificName"})
 */
class Plant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $scientificName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commonName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $information;

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

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(string $information): self
    {
//        $this->information = empty($information) ?  '' : $information;
        $this->information = $information;

        return $this;
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

    public function setScientificName(?string $scientificName): self
    {
        $this->scientificName = $scientificName;

        return $this;
    }
}
