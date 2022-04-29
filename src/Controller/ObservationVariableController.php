<?php

namespace App\Controller;

use App\Entity\ObservationVariable;
use App\Form\ObservationVariableType;
use App\Form\ObservationVariableUpdateType;
use App\Repository\ObservationVariableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/observation/variable", name="observation_variable_")
 */
class ObservationVariableController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ObservationVariableRepository $observationVariableRepo): Response
    {
        $observationVariables =  $observationVariableRepo->findAll();
        $context = [
            'title' => 'Observation Variable List',
            'observationVariables' => $observationVariables
        ];
        return $this->render('observation_variable/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $observationVariable = new ObservationVariable();
        $form = $this->createForm(ObservationVariableType::class, $observationVariable);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $observationVariable->setCreatedBy($this->getUser());
            }
            $observationVariable->setIsActive(true);
            $observationVariable->setCreatedAt(new \DateTime());
            $entmanager->persist($observationVariable);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('observation_variable_index'));
        }

        $context = [
            'title' => 'Observation Variable Creation',
            'observationVariableForm' => $form->createView()
        ];
        return $this->render('observation_variable/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ObservationVariable $observationVariableSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Observation Variable Details',
            'observationVariable' => $observationVariableSelected
        ];
        return $this->render('observation_variable/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ObservationVariable $observationVariable, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('observation_variable_edit', $observationVariable);
        $form = $this->createForm(ObservationVariableUpdateType::class, $observationVariable);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($observationVariable);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('observation_variable_index'));
        }

        $context = [
            'title' => 'Observation Variable Update',
            'observationVariableForm' => $form->createView()
        ];
        return $this->render('observation_variable/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ObservationVariable $observationVariable, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($observationVariable->getId()) {
            $observationVariable->setIsActive(!$observationVariable->getIsActive());
        }
        $entmanager->persist($observationVariable);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $observationVariable->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

