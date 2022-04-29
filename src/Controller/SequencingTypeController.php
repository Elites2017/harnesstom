<?php

namespace App\Controller;

use App\Entity\SequencingType;
use App\Form\SequencingCreateType;
use App\Form\SequencingUpdateType;
use App\Repository\SequencingTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/sequencing/type", name="sequencing_type_")
 */
class SequencingTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SequencingTypeRepository $sequencingTypeRepo): Response
    {
        $sequencingTypes =  $sequencingTypeRepo->findAll();
        $context = [
            'title' => 'Sequencing Type List',
            'sequencingTypes' => $sequencingTypes
        ];
        return $this->render('sequencing_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sequencingType = new SequencingType();
        $form = $this->createForm(SequencingCreateType::class, $sequencingType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $sequencingType->setCreatedBy($this->getUser());
            }
            $sequencingType->setIsActive(true);
            $sequencingType->setCreatedAt(new \DateTime());
            $entmanager->persist($sequencingType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('sequencing_type_index'));
        }

        $context = [
            'title' => 'Sequencing Type Creation',
            'sequencingTypeForm' => $form->createView()
        ];
        return $this->render('sequencing_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(SequencingType $sequencingTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Sequencing Type Details',
            'sequencingType' => $sequencingTypeSelected
        ];
        return $this->render('sequencing_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(SequencingType $sequencingType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('sequencing_type_edit', $sequencingType);
        $form = $this->createForm(SequencingUpdateType::class, $sequencingType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($sequencingType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('sequencing_type_index'));
        }

        $context = [
            'title' => 'Sequencing Type Update',
            'sequencingTypeForm' => $form->createView()
        ];
        return $this->render('sequencing_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(SequencingType $sequencingType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($sequencingType->getId()) {
            $sequencingType->setIsActive(!$sequencingType->getIsActive());
        }
        $entmanager->persist($sequencingType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $sequencingType->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

