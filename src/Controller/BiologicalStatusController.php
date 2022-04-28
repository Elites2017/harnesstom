<?php

namespace App\Controller;

use App\Entity\BiologicalStatus;
use App\Form\BiologicalStatusType;
use App\Form\BiologicalStatusUpdateType;
use App\Repository\BiologicalStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("biological/status", name="biological_status_")
 */
class BiologicalStatusController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(BiologicalStatusRepository $biologicalStatusRepo): Response
    {
        $biologicalStatuses =  $biologicalStatusRepo->findAll();
        $context = [
            'title' => 'Biological Status List',
            'biologicalStatuses' => $biologicalStatuses
        ];
        return $this->render('biological_status/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $biologicalStatus = new BiologicalStatus();
        $form = $this->createForm(BiologicalStatusType::class, $biologicalStatus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $biologicalStatus->setCreatedBy($this->getUser());
            }
            $biologicalStatus->setIsActive(true);
            $biologicalStatus->setCreatedAt(new \DateTime());
            $entmanager->persist($biologicalStatus);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('biological_status_index'));
        }

        $context = [
            'title' => 'Biological Status',
            'biologicalStatusForm' => $form->createView()
        ];
        return $this->render('biological_status/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(BiologicalStatus $biologicalStatusSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Biological Status',
            'biologicalStatus' => $biologicalStatusSelected
        ];
        return $this->render('biological_status/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(BiologicalStatus $biologicalStatus, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(BiologicalStatusUpdateType::class, $biologicalStatus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($biologicalStatus);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('biological_status_index'));
        }

        $context = [
            'title' => 'Biologiccal Status Update',
            'biologicalStatusForm' => $form->createView()
        ];
        return $this->render('biological_status/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(BiologicalStatus $biologicalStatus, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($biologicalStatus->getId()) {
            $biologicalStatus->setIsActive(!$biologicalStatus->getIsActive());
        }
        $entmanager->persist($biologicalStatus);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $biologicalStatus->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}


