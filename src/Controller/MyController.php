<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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