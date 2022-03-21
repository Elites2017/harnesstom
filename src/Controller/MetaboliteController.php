<?php

namespace App\Controller;

use App\Entity\Metabolite;
use App\Form\MetaboliteType;
use App\Form\MetaboliteUpdateType;
use App\Repository\MetaboliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/metabolite", name="metabolite_")
 */
class MetaboliteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MetaboliteRepository $metaboliteRepo): Response
    {
        $metabolites =  $metaboliteRepo->findAll();
        $context = [
            'title' => 'Metabolite List',
            'metabolites' => $metabolites
        ];
        return $this->render('metabolite/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $metabolite = new Metabolite();
        $form = $this->createForm(MetaboliteType::class, $metabolite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $metabolite->setCreatedBy($this->getUser());
            }
            $metabolite->setIsActive(true);
            $metabolite->setCreatedAt(new \DateTime());
            $entmanager->persist($metabolite);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolite_index'));
        }

        $context = [
            'title' => 'Metabolite Creation',
            'metaboliteForm' => $form->createView()
        ];
        return $this->render('metabolite/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Metabolite $metaboliteSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Metabolite Details',
            'metabolite' => $metaboliteSelected
        ];
        return $this->render('metabolite/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Metabolite $metabolite, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(MetaboliteUpdateType::class, $metabolite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($metabolite);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolite_index'));
        }

        $context = [
            'title' => 'Metabolite Update',
            'metaboliteForm' => $form->createView()
        ];
        return $this->render('metabolite/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Metabolite $metabolite, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($metabolite->getId()) {
            $metabolite->setIsActive(!$metabolite->getIsActive());
        }
        $entmanager->persist($metabolite);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $metabolite->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

