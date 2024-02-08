<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/tutorial", name="tutorial_")
 */
class TutorialController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('tutorial/index.html.twig', [
            'controller_name' => 'TutorialController',
            'title' => 'HarnessstomTutorial'
        ]);
    }
}
