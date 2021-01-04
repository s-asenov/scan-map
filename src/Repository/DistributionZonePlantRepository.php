<?php

namespace App\Repository;

use App\Entity\DistributionZonePlant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DistributionZonePlant|null find($id, $lockMode = null, $lockVersion = null)
 * @method DistributionZonePlant|null findOneBy(array $criteria, array $orderBy = null)
 * @method DistributionZonePlant[]    findAll()
 * @method DistributionZonePlant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DistributionZonePlantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DistributionZonePlant::class);
    }


    public function findByDistributionZone($value)
    {
         return $this->createQueryBuilder('p')
            ->andWhere('p.distributionZone = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?DistributionZonePlant
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
