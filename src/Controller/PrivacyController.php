<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/privacy", name="privacy_")
 */
class PrivacyController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $context = [
            'title' => 'Privacy',
        ];
        return $this->render('privacy/index.html.twig', $context);
    }
    
    /**
     * @Route("/license", name="license")
     */
    public function license(): Response
    {
        $context = [
            'title' => 'License',
        ];
        return $this->render('privacy/license.html.twig', $context);
    }

    /**
     * @Route("/terms", name="terms_of_use")
     */
    public function terms(): Response
    {
        $context = [
            'title' => 'Terms of Use',
        ];
        return $this->render('privacy/terms.html.twig', $context);
    }

    /**
     * @Route("/citation", name="citation")
     */
    public function citation(): Response
    {
        $context = [
            'title' => 'Citation'
        ];
        return $this->render('privacy/citation.html.twig', $context);
    }

}
