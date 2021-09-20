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

}