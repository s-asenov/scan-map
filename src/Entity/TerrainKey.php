<?php

namespace App\Entity;

use App\Repository\TerrainKeysRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TerrainKeysRepository::class)
 * @ORM\Table(name="`terrain_keys`")
 */
class TerrainKey
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $zipName;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="keys")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    function random_str(
        int $length = 20,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public function __construct(User $user)
    {
        $now = new \DateTime();
        $this->id = $now->format('Y-m-d H:i:s') . "-" . $user->getId() . $this->random_str();
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
}
