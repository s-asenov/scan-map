<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route ("/", name="app_homepage")
     * @Route("/{reactRoute}", name="app_react")
     */
    public function index()
    {
        return $this->render('react/react.html.twig');
    }
}