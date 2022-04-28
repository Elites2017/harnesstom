<?php

namespace App\Controller;

use App\Entity\Enzyme;
use App\Form\EnzymeType;
use App\Form\EnzymeUpdateType;
use App\Repository\EnzymeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/enzyme", name="enzyme_")
 */
class EnzymeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EnzymeRepository $enzymeRepo): Response
    {
        $enzymes =  $enzymeRepo->findAll();
        $context = [
            'title' => 'Enzyme',
            'enzymes' => $enzymes
        ];
        return $this->render('enzyme/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $enzyme = new Enzyme();
        $form = $this->createForm(EnzymeType::class, $enzyme);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $enzyme->setCreatedBy($this->getUser());
            }
            $enzyme->setIsActive(true);
            $enzyme->setCreatedAt(new \DateTime());
            $entmanager->persist($enzyme);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('enzyme_index'));
        }

        $context = [
            'title' => 'Enzyme Creation',
            'enzymeForm' => $form->createView()
        ];
        return $this->render('enzyme/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Enzyme $enzymeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Enzyme Details',
            'enzyme' => $enzymeSelected
        ];
        return $this->render('enzyme/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Enzyme $enzyme, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(EnzymeUpdateType::class, $enzyme);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($enzyme);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('enzyme_index'));
        }

        $context = [
            'title' => 'Enzyme Update',
            'enzymeForm' => $form->createView()
        ];
        return $this->render('enzyme/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Enzyme $enzyme, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($enzyme->getId()) {
            $enzyme->setIsActive(!$enzyme->getIsActive());
        }
        $entmanager->persist($enzyme);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $enzyme->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
