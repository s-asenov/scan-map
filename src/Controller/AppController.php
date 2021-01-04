<?php


namespace App\Controller;

use App\Service\DistributionZonesUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function test(DistributionZonesUploader  $uploader)
    {
        $uploader->uploadToDB();
    }
    /**
     * @Route ("/", name="app_homepage")
     * @Route("/{reactRoute}", name="app_react")
     */
    public function index()
    {
        return $this->render('react/react.html.twig');
    }
}