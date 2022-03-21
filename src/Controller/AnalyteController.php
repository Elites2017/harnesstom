<?php

namespace App\Controller;

use App\Entity\Analyte;
use App\Form\AnalyteType;
use App\Form\AnalyteUpdateType;
use App\Repository\AnalyteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/analyte", name="analyte_")
 */
class AnalyteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AnalyteRepository $analyteRepo): Response
    {
        $analytes =  $analyteRepo->findAll();
        $context = [
            'title' => 'Analyte List',
            'analytes' => $analytes
        ];
        return $this->render('analyte/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $analyte = new Analyte();
        $form = $this->createForm(AnalyteType::class, $analyte);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $analyte->setCreatedBy($this->getUser());
            }
            $analyte->setIsActive(true);
            $analyte->setCreatedAt(new \DateTime());
            $entmanager->persist($analyte);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('analyte_index'));
        }

        $context = [
            'title' => 'Analyte Creation',
            'analyteForm' => $form->createView()
        ];
        return $this->render('analyte/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Analyte $analyteSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Analyte Details',
            'analyte' => $analyteSelected
        ];
        return $this->render('analyte/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Analyte $analyte, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(AnalyteUpdateType::class, $analyte);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($analyte);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('analyte_index'));
        }

        $context = [
            'title' => 'Analyte Update',
            'analyteForm' => $form->createView()
        ];
        return $this->render('analyte/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Analyte $analyte, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($analyte->getId()) {
            $analyte->setIsActive(!$analyte->getIsActive());
        }
        $entmanager->persist($analyte);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $analyte->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

