<?php

namespace App\Controller;

use App\Entity\ObservationLevel;
use App\Form\ObservationLevelType;
use App\Form\ObservationLevelUpdateType;
use App\Repository\ObservationLevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("observation/level", name="observation_level_")
 */
class ObservationLevelController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ObservationLevelRepository $observationLevelRepo): Response
    {
        $observationLevels =  $observationLevelRepo->findAll();
        $context = [
            'title' => 'Observation Level List',
            'observationLevels' => $observationLevels
        ];
        return $this->render('observation_level/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $observationLevel = new ObservationLevel();
        $form = $this->createForm(ObservationLevelType::class, $observationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $observationLevel->setCreatedBy($this->getUser());
            }
            $observationLevel->setIsActive(true);
            $observationLevel->setCreatedAt(new \DateTime());
            $entmanager->persist($observationLevel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('observation_level_index'));
        }

        $context = [
            'title' => 'Observation Level Creation',
            'observationLevelForm' => $form->createView()
        ];
        return $this->render('observation_level/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ObservationLevel $observationLevelSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Observation Level Details',
            'observationLevel' => $observationLevelSelected
        ];
        return $this->render('observation_level/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ObservationLevel $observationLevel, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('observation_level_edit', $observationLevel);
        $form = $this->createForm(ObservationLevelUpdateType::class, $observationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $observationLevel->setLastUpdated(new \DateTime());
            $entmanager->persist($observationLevel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('observation_level_index'));
        }

        $context = [
            'title' => 'Observation Level Update',
            'observationLevelForm' => $form->createView()
        ];
        return $this->render('observation_level/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ObservationLevel $observationLevel, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($observationLevel->getId()) {
            $observationLevel->setIsActive(!$observationLevel->getIsActive());
        }
        $entmanager->persist($observationLevel);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $observationLevel->getIsActive()
        ], 200);
    }
}