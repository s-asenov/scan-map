<?php

namespace App\Repository;

use App\Entity\Plant;
use App\Util\MyHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Plant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plant[]    findAll()
 * @method Plant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private MyHelper $helper)
    {
        parent::__construct($registry, Plant::class);
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
     * @param Plant $plant
     * @param string $path
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addModelToPlant(Plant $plant, string $path)
    {
        $plant->setModelPath($path);

        $this->_em->persist($plant);
        $this->_em->flush();
    }

    /**
     * @param Plant[] $plants
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function bulkInsert(array $plants)
    {
        $fields = ['id', 'scientific_name', 'common_name', 'description', 'image_url', 'model_path'];
        $values = [];
        $question_marks = [];

        foreach ($plants as $plant) {
            $question_marks[] = '('  . $this->helper->placeholders("?", count($fields)) . ')';

            array_push($values, null, $plant->getScientificName(), $plant->getCommonName(), $plant->getDescription(), $plant->getImageUrl(), $plant->getModelPath());
        }

        $sql = "INSERT INTO plants (" . implode(",", $fields ) . ") VALUES " .
            implode(',', $question_marks) . " ON DUPLICATE KEY UPDATE id=id";

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute($values);
    }
}
