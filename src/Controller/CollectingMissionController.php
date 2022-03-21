<?php

namespace App\Controller;

use App\Entity\CollectingMission;
use App\Form\CollectingMissionType;
use App\Form\CollectingMissionUpdateType;
use App\Repository\CollectingMissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/collecting/mission", name="collecting_mission_")
 */
class CollectingMissionController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CollectingMissionRepository $collectingMissionRepo): Response
    {
        $collectingMissions =  $collectingMissionRepo->findAll();
        $context = [
            'title' => 'Collecting Mission List',
            'collectingMissions' => $collectingMissions
        ];
        return $this->render('collecting_mission/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $collectingMission = new CollectingMission();
        $form = $this->createForm(CollectingMissionType::class, $collectingMission);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $collectingMission->setCreatedBy($this->getUser());
            }
            $collectingMission->setIsActive(true);
            $collectingMission->setCreatedAt(new \DateTime());
            $entmanager->persist($collectingMission);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collecting_mission_index'));
        }

        $context = [
            'title' => 'Collecting Mission Creation',
            'collectingMissionForm' => $form->createView()
        ];
        return $this->render('collecting_mission/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(CollectingMission $collectingMissionSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Collecting Mission Details',
            'collectingMission' => $collectingMissionSelected
        ];
        return $this->render('collecting_mission/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(CollectingMission $collectingMission, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(CollectingMissionUpdateType::class, $collectingMission);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($collectingMission);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collecting_mission_index'));
        }

        $context = [
            'title' => 'Collecting Mission Update',
            'collectingMissionForm' => $form->createView()
        ];
        return $this->render('collecting_mission/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(CollectingMission $collectingMission, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($collectingMission->getId()) {
            $collectingMission->setIsActive(!$collectingMission->getIsActive());
        }
        $entmanager->persist($collectingMission);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $collectingMission->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
