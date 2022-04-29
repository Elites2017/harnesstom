<?php

namespace App\Controller;

use App\Entity\ObservationVariableMethod;
use App\Form\ObservationVariableMethodType;
use App\Form\ObservationVariableMethodUpdateType;
use App\Repository\ObservationVariableMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/observation/variable/method", name="observation_variable_method_")
 */
class ObservationVariableMethodController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ObservationVariableMethodRepository $observationVariableMethodRepo): Response
    {
        $observationVariableMethods =  $observationVariableMethodRepo->findAll();
        $context = [
            'title' => 'Observation Variable Method List',
            'observationVariableMethods' => $observationVariableMethods
        ];
        return $this->render('observation_variable_method/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $observationVariableMethod = new ObservationVariableMethod();
        $form = $this->createForm(ObservationVariableMethodType::class, $observationVariableMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $observationVariableMethod->setCreatedBy($this->getUser());
            }
            $observationVariableMethod->setIsActive(true);
            $observationVariableMethod->setCreatedAt(new \DateTime());
            $entmanager->persist($observationVariableMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('observation_variable_method_index'));
        }

        $context = [
            'title' => 'Observation Variable Method Creation',
            'observationVariableMethodForm' => $form->createView()
        ];
        return $this->render('observation_variable_method/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ObservationVariableMethod $observationVariableMethodSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Observation Variable Method Details',
            'observationVariableMethod' => $observationVariableMethodSelected
        ];
        return $this->render('observation_variable_method/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ObservationVariableMethod $observationVariableMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('observation_variable_method_edit', $observationVariableMethod);
        $form = $this->createForm(ObservationVariableMethodUpdateType::class, $observationVariableMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($observationVariableMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('observation_variable_method_index'));
        }

        $context = [
            'title' => 'Observation Variable Method Update',
            'observationVariableMethodForm' => $form->createView()
        ];
        return $this->render('observation_variable_method/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ObservationVariableMethod $observationVariableMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($observationVariableMethod->getId()) {
            $observationVariableMethod->setIsActive(!$observationVariableMethod->getIsActive());
        }
        $entmanager->persist($observationVariableMethod);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $observationVariableMethod->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

