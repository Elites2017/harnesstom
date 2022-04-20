<?php

namespace App\Controller;

use App\Entity\Synonym;
use App\Form\SynonymType;
use App\Form\SynonymUpdateType;
use App\Repository\SynonymRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/synonym", name="synonym_")
 */
class SynonymController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SynonymRepository $synonymRepo): Response
    {
        $synonyms =  $synonymRepo->findAll();
        $context = [
            'title' => 'Synonym List',
            'synonyms' => $synonyms
        ];
        return $this->render('synonym/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $synonym = new Synonym();
        $form = $this->createForm(SynonymType::class, $synonym);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('accession')->getData() instanceof \App\Entity\Accession) {
                $this->addFlash('danger', "You must choose an accession from the liste");
            } else {
                if ($this->getUser()) {
                    $synonym->setCreatedBy($this->getUser());
                }
                $synonym->setIsActive(true);
                $synonym->setCreatedAt(new \DateTime());
                $entmanager->persist($synonym);
                $entmanager->flush();
                return $this->redirect($this->generateUrl('synonym_index'));
            }
        }

        $context = [
            'title' => 'Synonym Creation',
            'synonymForm' => $form->createView()
        ];
        return $this->render('synonym/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Synonym $synonymselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Synonym Details',
            'synonym' => $synonymselected
        ];
        return $this->render('synonym/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Synonym $synonym, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('synonym_edit', $synonym);
        $form = $this->createForm(SynonymUpdateType::class, $synonym);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('accession')->getData() instanceof \App\Entity\Accession) {
                $this->addFlash('danger', "You must choose an accession from the list");
            } else {
                $entmanager->persist($synonym);
                $entmanager->flush();
                return $this->redirect($this->generateUrl('synonym_index'));
            }
        }

        $context = [
            'title' => 'Synonym Update',
            'synonymForm' => $form->createView()
        ];
        return $this->render('synonym/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(Synonym $synonym, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($synonym->getId()) {
            $synonym->setIsActive(!$synonym->getIsActive());
        }
        $entmanager->persist($synonym);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $synonym->getIsActive()
        ], 200);
    }
}
