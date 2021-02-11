<?php

namespace App\Repository;

use App\Entity\DistributionZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DistributionZone|null find($id, $lockMode = null, $lockVersion = null)
 * @method DistributionZone|null findOneBy(array $criteria, array $orderBy = null)
 * @method DistributionZone[]    findAll()
 * @method DistributionZone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DistributionZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DistributionZone::class);
    }

    // /**
    //  * @return DistributionZone[] Returns an array of DistributionZone objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DistributionZone
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getFetchedCount()
    {
        return $this->createQueryBuilder('d')
            ->select('count(d.name)')
            ->where('d.fetched = :fetched')
            ->setParameter('fetched', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMostFetched()
    {
        return $this->createQueryBuilder('dz')
            ->select('count(dz.id) as count, dz.name')
            ->orderBy('count', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
