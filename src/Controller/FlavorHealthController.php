<?php

namespace App\Controller;

use App\Entity\AnalyteFlavorHealth;
use App\Form\FlavorHealthType;
use App\Form\FlavorHealthUpdateType;
use App\Repository\AnalyteFlavorHealthRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("/analyte/flavor/health", name="flavor_health_")
 */
class FlavorHealthController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AnalyteFlavorHealthRepository $flavorHealthRepo): Response
    {
        $flavorHealths =  $flavorHealthRepo->findAll();
        $context = [
            'title' => 'Analyte Flavor Health List',
            'flavorHealths' => $flavorHealths
        ];
        return $this->render('flavor_health/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $flavorHealth = new AnalyteFlavorHealth();
        $form = $this->createForm(FlavorHealthType::class, $flavorHealth);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $flavorHealth->setCreatedBy($this->getUser());
            }
            $flavorHealth->setIsActive(true);
            $flavorHealth->setCreatedAt(new \DateTime());
            $entmanager->persist($flavorHealth);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('flavor_health_index'));
        }

        $context = [
            'title' => 'Analyte Flavor Health Creation',
            'flavorHealthForm' => $form->createView()
        ];
        return $this->render('flavor_health/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AnalyteFlavorHealth $flavorHealthSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Analyte Flavor Health Details',
            'flavorHealth' => $flavorHealthSelected
        ];
        return $this->render('flavor_health/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AnalyteFlavorHealth $flavorHealth, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('flavor_health_edit', $flavorHealth);
        $form = $this->createForm(FlavorHealthUpdateType::class, $flavorHealth);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($flavorHealth);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('flavor_health_index'));
        }

        $context = [
            'title' => 'Analyte Flavor Health Update',
            'flavorHealthForm' => $form->createView()
        ];
        return $this->render('flavor_health/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AnalyteFlavorHealth $flavorHealth, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($flavorHealth->getId()) {
            $flavorHealth->setIsActive(!$flavorHealth->getIsActive());
        }
        $entmanager->persist($flavorHealth);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $flavorHealth->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }

    // this is to upload data in bulk using an excel file
    /**
     * @Route("/upload-from-excel", name="upload_from_excel")
     */
    public function uploadFromExcel(Request $request, EntityManagerInterface $entmanager): Response
    {
        dd("Good morning SEASON");
    }
}


