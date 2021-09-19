<?php

namespace App\Repository;

use App\Entity\PlantSync;
use App\Util\MyHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlantSync|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlantSync|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlantSync[]    findAll()
 * @method PlantSync[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlantSyncRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private MyHelper $helper)
    {
        parent::__construct($registry, PlantSync::class);
    }

    public function findByScientificName($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.scientificName in (:val)')
            ->setParameter('val', $value)
            ->indexBy('p', 'p.scientificName')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByInput(string $input)
    {
        return $this->createQueryBuilder('p')
            ->orWhere('p.scientificName LIKE :input')
            ->setParameter('input', '%'.$input.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param PlantSync $plant
     * @param string $path
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addModelToPlantSync(PlantSync $plant, string $path)
    {
        $plant->setModelPath($path);

        $this->_em->persist($plant);
        $this->_em->flush();
    }

    public function deletePlantsSync($distributionZone, $names)
    {
        return $this->createQueryBuilder('p')
            ->delete()
            ->where('p.distributionZone = :zone')
            ->andWhere('p.scientificName IN (:names)')
            ->setParameter("zone", $distributionZone)
            ->setParameter("names", $names)
            ->getQuery()
            ->getResult();
    }

    public function getNewPlants($zone): array
    {
        $sql = "SELECT ps.*, p.id plant_id FROM plants_sync ps 
        LEFT JOIN plants p ON ps.scientific_name = p.scientific_name
        LEFT JOIN (
        	SELECT dzp.*, pl.scientific_name FROM distribution_zone_plant dzp LEFT JOIN plants pl ON dzp.plant_id = pl.id
        ) dzp ON dzp.scientific_name = ps.scientific_name AND dzp.distribution_zone_id = ps.distribution_zone
        WHERE dzp.id IS NULL AND ps.distribution_zone = $zone";

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
