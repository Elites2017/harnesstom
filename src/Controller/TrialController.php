<?php

namespace App\Controller;

use App\Entity\Trial;
use App\Form\TrialType;
use App\Form\TrialUpType;
use App\Repository\TrialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/trial", name="trial_")
 */
class TrialController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TrialRepository $trialRepo): Response
    {
        $trials =  $trialRepo->findAll();
        $context = [
            'title' => 'Trial List',
            'trials' => $trials
        ];
        return $this->render('trial/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $trial = new Trial();
        $form = $this->createForm(TrialType::class, $trial);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $trial->setCreatedBy($this->getUser());
            }
            $trial->setIsActive(true);
            $trial->setCreatedAt(new \DateTime());
            $entmanager->persist($trial);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trial_index'));
        }

        $context = [
            'title' => 'Trial Creation',
            'trialForm' => $form->createView()
        ];
        return $this->render('trial/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Trial $trialSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Trial Details',
            'trial' => $trialSelected
        ];
        return $this->render('trial/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Trial $trial, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(TrialUpType::class, $trial);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($trial);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trial_index'));
        }

        $context = [
            'title' => 'Trial Update',
            'trialForm' => $form->createView()
        ];
        return $this->render('trial/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Trial $trial, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($trial->getId()) {
            $trial->setIsActive(!$trial->getIsActive());
        }
        $entmanager->persist($trial);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $trial->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}


