<?php

namespace App\Repository;

use App\Entity\DistributionZonePlant;
use App\Entity\Plant;
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

    public function getMostPlantsInDistributionZone()
    {
        return $this->createQueryBuilder('dzp')
            ->select('count(dzp.id) as count')
            ->groupBy('dzp.distributionZone')
            ->orderBy('count', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMostSeenPlants()
    {
        return $this->createQueryBuilder('dzp')
            ->select('count(dzp.id) as count, p.scientificName, p.commonName')
            ->leftJoin(Plant::class, "p", "WITH", "dzp.id = p.id")
            ->groupBy('dzp.plant')
            ->orderBy('count', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
