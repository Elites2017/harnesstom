<?php

namespace App\Controller;

use App\Entity\MarkerSynonym;
use App\Form\MarkerSynonymType;
use App\Form\MarkerSynonymUpdateType;
use App\Repository\MarkerSynonymRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/marker/synonym", name="marker_synonym_")
 */
class MarkerSynonymController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MarkerSynonymRepository $markerSynonymRepo): Response
    {
        $markerSynonyms =  $markerSynonymRepo->findAll();
        $context = [
            'title' => 'Marker Synonym List',
            'markerSynonyms' => $markerSynonyms
        ];
        return $this->render('marker_synonym/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $markerSynonym = new MarkerSynonym();
        $form = $this->createForm(MarkerSynonymType::class, $markerSynonym);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $markerSynonym->setCreatedBy($this->getUser());
            }
            $markerSynonym->setIsActive(true);
            $markerSynonym->setCreatedAt(new \DateTime());
            $this->addFlash('success', "A new synonym has been successfuly added");
            $entmanager->persist($markerSynonym);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('marker_synonym_index'));
        }

        $context = [
            'title' => 'Marker Synonym Creation',
            'markerSynonymForm' => $form->createView()
        ];
        return $this->render('marker_synonym/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MarkerSynonym $markerSynonymSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Marker Synonym Details',
            'markerSynonym' => $markerSynonymSelected
        ];
        return $this->render('marker_synonym/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MarkerSynonym $markerSynonym, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('marker_synonym_edit', $markerSynonym);
        $form = $this->createForm(MarkerSynonymUpdateType::class, $markerSynonym);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($markerSynonym);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('marker_synonym_index'));
        }

        $context = [
            'title' => 'Marker Synonym Update',
            'markerSynonymForm' => $form->createView()
        ];
        return $this->render('marker_synonym/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MarkerSynonym $markerSynonym, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($markerSynonym->getId()) {
            $markerSynonym->setIsActive(!$markerSynonym->getIsActive());
        }
        $entmanager->persist($markerSynonym);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $markerSynonym->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }

    

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/marker_synonym_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'marker_synonym_template_example.xlsx');
        return $response;
       
    }
}