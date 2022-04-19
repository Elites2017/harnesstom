<?php

namespace App\Controller;

use App\Entity\Study;
use App\Form\StudyType;
use App\Form\StudyUpdateType;
use App\Repository\StudyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/study", name="study_")
 */
class StudyController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(StudyRepository $studyRepo): Response
    {
        $studies =  $studyRepo->findAll();
        $context = [
            'title' => 'Study List',
            'studies' => $studies
        ];
        return $this->render('study/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $study = new Study();
        $form = $this->createForm(StudyType::class, $study);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $form->get('startDate')->getData();
            $endDate = $form->get('endDate')->getData();
            if ($startDate > $endDate) {
                $this->addFlash('danger', "The end date must be greater than the start date");
            } else {
                if ($this->getUser()) {
                    $study->setCreatedBy($this->getUser());
                }
                $study->setIsActive(true);
                $study->setCreatedAt(new \DateTime());
                $entmanager->persist($study);
                $entmanager->flush();
                return $this->redirect($this->generateUrl('study_index'));
            }
        }

        $context = [
            'title' => 'Study Creation',
            'studyForm' => $form->createView()
        ];
        return $this->render('study/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Study $studieSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Study Details',
            'study' => $studieSelected
        ];
        return $this->render('study/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Study $study, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(StudyUpdateType::class, $study);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $form->get('startDate')->getData();
            $endDate = $form->get('endDate')->getData();
            if ($startDate > $endDate) {
                $this->addFlash('danger', "The end date must be greater than the start date");
            } else {
                $study->setLastUpdated(new \DateTime());
                $entmanager->persist($study);
                $entmanager->flush();
                return $this->redirect($this->generateUrl('study_index'));
            }
        }

        $context = [
            'title' => 'Study Update',
            'studyForm' => $form->createView()
        ];
        return $this->render('study/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(Study $study, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($study->getId()) {
            $study->setIsActive(!$study->getIsActive());
        }
        $entmanager->persist($study);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $study->getIsActive()
        ], 200);
    }
}
