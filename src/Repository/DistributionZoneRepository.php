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
        return (int) $this->createQueryBuilder('d')
            ->select('count(d.name)')
            ->where('d.fetched > 0')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMostFetched()
    {
        return $this->createQueryBuilder('d')
            ->select('d.fetched as count, d.name')
            ->where('d.fetched > 0')
            // ->groupBy('d.fetched')
            ->orderBy('d.fetched', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
}
