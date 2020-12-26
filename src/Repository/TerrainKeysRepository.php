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
class TerrainKeysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TerrainKey::class);
    }

    // /**
    //  * @return TerrainKey[] Returns an array of TerrainKey objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TerrainKey
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
