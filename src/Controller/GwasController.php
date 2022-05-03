<?php

namespace App\Controller;

use App\Entity\GWAS;
use App\Form\GWASType;
use App\Form\GWASUpdateType;
use App\Repository\GWASRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/gwas", name="gwas_")
 */
class GwasController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GWASRepository $gwasRepo): Response
    {
        $gwases =  $gwasRepo->findAll();
        $context = [
            'title' => 'GWAS List',
            'gwases' => $gwases
        ];
        return $this->render('gwas/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $gwas = new GWAS();
        $form = $this->createForm(GWASType::class, $gwas);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $gwas->setCreatedBy($this->getUser());
            }
            $gwas->setIsActive(true);
            $gwas->setCreatedAt(new \DateTime());
            $entmanager->persist($gwas);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_index'));
        }

        $context = [
            'title' => 'GWAS Creation',
            'gwasForm' => $form->createView()
        ];
        return $this->render('gwas/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GWAS $gwasSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'GWAS Details',
            'gwas' => $gwasSelected
        ];
        return $this->render('gwas/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GWAS $gwas, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('gwas_edit', $gwas);
        $form = $this->createForm(GWASUpdateType::class, $gwas);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($gwas);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_index'));
        }

        $context = [
            'title' => 'GWAS Update',
            'gwasForm' => $form->createView()
        ];
        return $this->render('gwas/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GWAS $gwas, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($gwas->getId()) {
            $gwas->setIsActive(!$gwas->getIsActive());
        }
        $entmanager->persist($gwas);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $gwas->getIsActive()
        ], 200);
    }
}

