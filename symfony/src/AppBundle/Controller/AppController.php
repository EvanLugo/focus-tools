<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("app/home", name="home")
     */
    public function homeAction()
    {
        return $this->render('home/home.html.twig');
    }
}
