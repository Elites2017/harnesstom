<?php

namespace App\Controller;

use App\Entity\MLSStatus;
use App\Form\MLSStatusType;
use App\Form\MLSStatusUpdateType;
use App\Repository\MLSStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/mls/status", name="mls_status_")
 */
class MLSStatusController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MLSStatusRepository $mlsStatusRepo): Response
    {
        $mlsStatuses =  $mlsStatusRepo->findAll();
        $context = [
            'title' => 'MLS Staus List',
            'mlsStatuses' => $mlsStatuses
        ];
        return $this->render('mls_status/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $mlsStatus = new MLSStatus();
        $form = $this->createForm(MLSStatusType::class, $mlsStatus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $mlsStatus->setCreatedBy($this->getUser());
            }
            $mlsStatus->setIsActive(true);
            $mlsStatus->setCreatedAt(new \DateTime());
            $entmanager->persist($mlsStatus);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('mls_status_index'));
        }

        $context = [
            'title' => 'MLS Staus Creation',
            'mlsStatusForm' => $form->createView()
        ];
        return $this->render('mls_status/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MLSStatus $mlsStatusSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'MLS Staus Details',
            'mlsStatus' => $mlsStatusSelected
        ];
        return $this->render('mls_status/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MLSStatus $mlsStatus, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(MLSStatusUpdateType::class, $mlsStatus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($mlsStatus);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('mls_status_index'));
        }

        $context = [
            'title' => 'MLS Staus Update',
            'mlsStatusForm' => $form->createView()
        ];
        return $this->render('mls_status/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MLSStatus $mlsStatus, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($mlsStatus->getId()) {
            $mlsStatus->setIsActive(!$mlsStatus->getIsActive());
        }
        $entmanager->persist($mlsStatus);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $mlsStatus->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
