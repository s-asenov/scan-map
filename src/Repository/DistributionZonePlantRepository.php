<?php

namespace App\Repository;

use App\Entity\DistributionZone;
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
            ->select('COUNT(dzp.id) as count, dz.name as zone')
            ->leftJoin(DistributionZone::class, "dz", "WITH", "dzp.distributionZone = dz.id")
            ->groupBy('dzp.distributionZone')
            ->orderBy('count', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function getMostSeenPlants()
    {
        return $this->createQueryBuilder('dzp')
            ->select('COUNT(dzp.id) as count, p.scientificName, p.commonName')
            ->leftJoin(Plant::class, "p", "WITH", "dzp.plant = p.id")
            ->groupBy('dzp.plant')
            ->orderBy('count', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getTopZonesByPlantsCount()
    {
        return $this->createQueryBuilder('dzp')
            ->select('COUNT(dzp.id) as count, dz.name as name')
            ->leftJoin(DistributionZone::class, "dz", "WITH", "dzp.distributionZone = dz.id")
            ->groupBy('dzp.distributionZone')
            ->orderBy('count', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * Using the group concat and getting the desired data from the server
     * avoids unnecessary loops and calls to the db.
     */
    public function getMostSeenPlantsWithZoneName()
    {
        return $this->createQueryBuilder('dzp')
            ->select('COUNT(dzp.id) as count, GROUP_CONCAT(dz.name) as zones, p.scientificName as name, p.commonName as commonName')
            ->leftJoin(DistributionZone::class, "dz", "WITH", "dzp.distributionZone = dz.id")
            ->leftJoin(Plant::class, "p", "WITH", "dzp.plant = p.id")
            ->groupBy('dzp.plant')
            ->orderBy('count', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

}
