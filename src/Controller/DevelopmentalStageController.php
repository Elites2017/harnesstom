<?php

namespace App\Controller;

use App\Entity\DevelopmentalStage;
use App\Form\DevelomentalStageType;
use App\Form\DevelomentalStageUpdateType;
use App\Repository\DevelopmentalStageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("developmental/stage", name="developmental_stage_")
 */
class DevelopmentalStageController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(DevelopmentalStageRepository $developmentalStageRepo): Response
    {
        $developmentalStages =  $developmentalStageRepo->findAll();
        $context = [
            'title' => 'Developmental Stage List',
            'developmentalStages' => $developmentalStages
        ];
        return $this->render('developmental_stage/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $developmentalStage = new DevelopmentalStage();
        $form = $this->createForm(DevelomentalStageType::class, $developmentalStage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $developmentalStage->setIsActive(true);
            $developmentalStage->setCreatedAt(new \DateTime());
            $entmanager->persist($developmentalStage);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('developmental_stage_index'));
        }

        $context = [
            'title' => 'Developmental Stage Creation',
            'developmentalStageForm' => $form->createView()
        ];
        return $this->render('developmental_stage/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(DevelopmentalStage $developmentalStageSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Developmental Stage Details',
            'developmentalStage' => $developmentalStageSelected
        ];
        return $this->render('developmental_stage/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(DevelopmentalStage $developmentalStage, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(DevelomentalStageUpdateType::class, $developmentalStage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($developmentalStage);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('developmental_stage_index'));
        }

        $context = [
            'title' => 'Developmental Stage Update',
            'developmentalStageForm' => $form->createView()
        ];
        return $this->render('developmental_stage/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(DevelopmentalStage $developmentalStage, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($developmentalStage->getId()) {
            $developmentalStage->setIsActive(!$developmentalStage->getIsActive());
        }
        $entmanager->persist($developmentalStage);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $developmentalStage->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('developmentalStage_home'));
    }
}
