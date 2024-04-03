<?php

namespace App\Controller;

use App\Repository\CrossRepository;
use App\Repository\TrialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/user/log", name="user_log_")
 */
class UserLogController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $context = [
            'title' => 'User Log',
            'user' => $this->getUser()
        ];
        return $this->render('user_log/index.html.twig', $context);
    }

    /**
     * @Route("/cross", name="cross_index")
     */
    public function crossIndex(CrossRepository $crossRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // get the objects created by the logged (current) user
        $crosses = $crossRepo->findBy(['createdBy' => $this->getUser()]);
        $context = [
            'title' => 'User Log Cross',
            'crosses' => $crosses
        ];
        return $this->render('user_log/cross_index.html.twig', $context);
    }

    /**
     * @Route("/trial", name="trial_index")
     */
    public function trialIndex(TrialRepository $trialRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // get the objects created by the logged (current) user
        $trials = $trialRepo->findBy(['createdBy' => $this->getUser()]);
        $context = [
            'title' => 'User Log Trial',
            'trials' => $trials
        ];
        return $this->render('user_log/trial_index.html.twig', $context);
    }
}
