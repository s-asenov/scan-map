<?php


namespace App\Controller;


use App\Entity\DistributionZone;
use App\Entity\DistributionZonePlant;
use App\Entity\Plant;
use App\Entity\User;
use App\Repository\DistributionZonePlantRepository;
use App\Repository\PlantRepository;
use App\Service\Retriever\PlantsFromZoneRetriever;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class MyController extends AbstractController
{
    /**
     * @return User|UserInterface|null
     */
    public function getUser(): UserInterface|User|null
    {
        return parent::getUser();
    }

    #[Route("/api/test", name: "testyt", methods:["GET"])]
    public function test(PlantRepository $plantRepository, DistributionZonePlantRepository $dzRepository)
    {
        $plants = [];
        $dzs = [];

        $em = $this->getDoctrine()->getManager();
        $zone = $this->getDoctrine()->getRepository(DistributionZone::class)->find(94);

        for ($i = 1; $i < 4000; $i++) {
            $plant = new Plant();

            $plant->setScientificName("test$i");

            $em->persist($plant);

            $dis = new DistributionZonePlant();

            $dis->setPlant($plant);
            $dis->setDistributionZone($zone);

            $plants[] = $plant;
            $dzs[] = $dis;

            if ($i % 500 === 0) {
                $plantRepository->bulkInsert($plants);
                $dzRepository->bulkInsert($dzs);

                $plants = [];
                $dzs = [];
            }
        }


        dd("zdr");
    }
}