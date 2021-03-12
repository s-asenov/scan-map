<?php

namespace App\Repository;

use App\Entity\TerrainKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TerrainKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method TerrainKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method TerrainKey[]    findAll()
 * @method TerrainKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TerrainKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TerrainKey::class);
    }
}
