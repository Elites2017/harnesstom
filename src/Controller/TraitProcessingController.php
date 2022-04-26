<?php

namespace App\Controller;

use App\Entity\TraitProcessing;
use App\Form\TraitProcessingType;
use App\Form\TraitProcessingUpdateType;
use App\Repository\TraitProcessingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/trait/processing", name="trait_processing_")
 */
class TraitProcessingController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TraitProcessingRepository $traitProcessingRepo): Response
    {
        $traitProcessings =  $traitProcessingRepo->findAll();
        $context = [
            'title' => 'Trait Processing List',
            'traitProcessings' => $traitProcessings
        ];
        return $this->render('trait_processing/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $traitProcessing = new TraitProcessing();
        $form = $this->createForm(TraitProcessingType::class, $traitProcessing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $traitProcessing->setCreatedBy($this->getUser());
            }
            $traitProcessing->setIsActive(true);
            $traitProcessing->setCreatedAt(new \DateTime());
            $entmanager->persist($traitProcessing);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trait_processing_index'));
        }

        $context = [
            'title' => 'Trait Processing Creation',
            'traitProcessingForm' => $form->createView()
        ];
        return $this->render('trait_processing/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(TraitProcessing $traitProcessingselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Trait Processing Details',
            'traitProcessing' => $traitProcessingselected
        ];
        return $this->render('trait_processing/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(TraitProcessing $traitProcessing, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(TraitProcessingUpdateType::class, $traitProcessing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($traitProcessing);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trait_processing_index'));
        }

        $context = [
            'title' => 'Trait Processing Update',
            'traitProcessingForm' => $form->createView()
        ];
        return $this->render('trait_processing/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(TraitProcessing $traitProcessing, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($traitProcessing->getId()) {
            $traitProcessing->setIsActive(!$traitProcessing->getIsActive());
        }
        $entmanager->persist($traitProcessing);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $traitProcessing->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}