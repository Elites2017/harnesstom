<?php

namespace App\Controller;

use App\Entity\Cross;
use App\Form\CrossType;
use App\Form\CrossUpdateType;
use App\Repository\CrossRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/cross", name="cross_")
 */
class CrossController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CrossRepository $crossRepo): Response
    {
        $crosses =  $crossRepo->findAll();
        $context = [
            'title' => 'Cross List',
            'crosses' => $crosses
        ];
        return $this->render('cross/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $cross = new Cross();
        $form = $this->createForm(CrossType::class, $cross);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $cross->setCreatedBy($this->getUser());
            }
            $cross->setIsActive(true);
            $cross->setCreatedAt(new \DateTime());
            $entmanager->persist($cross);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('cross_index'));
        }

        $context = [
            'title' => 'Cross Creation',
            'crossForm' => $form->createView()
        ];
        return $this->render('cross/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Cross $crossSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Cross Details',
            'cross' => $crossSelected
        ];
        return $this->render('cross/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Cross $cross, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('cross_edit', $cross);
        $form = $this->createForm(CrossUpdateType::class, $cross);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($cross);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('cross_index'));
        }

        $context = [
            'title' => 'Cross Update',
            'crossForm' => $form->createView()
        ];
        return $this->render('cross/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Cross $cross, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($cross->getId()) {
            $cross->setIsActive(!$cross->getIsActive());
        }
        $entmanager->persist($cross);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $cross->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
