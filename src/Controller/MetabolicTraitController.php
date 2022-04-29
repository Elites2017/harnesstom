<?php

namespace App\Controller;

use App\Entity\MetabolicTrait;
use App\Form\MetabolicTraitType;
use App\Form\MetabolicTraitUpdateType;
use App\Repository\MetabolicTraitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/metabolic/trait", name="metabolic_trait_")
 */
class MetabolicTraitController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MetabolicTraitRepository $metabolicTraitRepo): Response
    {
        $metabolicTraits =  $metabolicTraitRepo->findAll();
        $context = [
            'title' => 'Metabolic Trait List',
            'metabolicTraits' => $metabolicTraits
        ];
        return $this->render('metabolic_trait/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $metabolicTrait = new MetabolicTrait();
        $form = $this->createForm(MetabolicTraitType::class, $metabolicTrait);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $metabolicTrait->setCreatedBy($this->getUser());
            }
            $metabolicTrait->setIsActive(true);
            $metabolicTrait->setCreatedAt(new \DateTime());
            $entmanager->persist($metabolicTrait);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolic_trait_index'));
        }

        $context = [
            'title' => 'Metabolic Trait Creation',
            'metabolicTraitForm' => $form->createView()
        ];
        return $this->render('metabolic_trait/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MetabolicTrait $metabolicTraitSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Metabolic Trait Details',
            'metabolicTrait' => $metabolicTraitSelected
        ];
        return $this->render('metabolic_trait/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MetabolicTrait $metabolicTrait, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('metabolic_trait_edit', $metabolicTrait);
        $form = $this->createForm(MetabolicTraitUpdateType::class, $metabolicTrait);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($metabolicTrait);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolic_trait_index'));
        }

        $context = [
            'title' => 'Metabolic Trait Update',
            'metabolicTraitForm' => $form->createView()
        ];
        return $this->render('metabolic_trait/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MetabolicTrait $metabolicTrait, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($metabolicTrait->getId()) {
            $metabolicTrait->setIsActive(!$metabolicTrait->getIsActive());
        }
        $entmanager->persist($metabolicTrait);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $metabolicTrait->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

