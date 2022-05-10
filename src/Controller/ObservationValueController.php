<?php

namespace App\Controller;

use App\Entity\ObservationValue;
use App\Form\ObservationValueType;
use App\Form\ObservationValueUpdateType;
use App\Repository\ObservationValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("observation/value", name="observation_value_")
 */
class ObservationValueController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ObservationValueRepository $observationValueRepo): Response
    {
        $observationValues =  $observationValueRepo->findAll();
        $context = [
            'title' => 'Observation Value List',
            'observationValues' => $observationValues
        ];
        return $this->render('observation_value/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $observationValue = new ObservationValue();
        $form = $this->createForm(ObservationValueType::class, $observationValue);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $observationValue->setCreatedBy($this->getUser());
            }
            $observationValue->setIsActive(true);
            $observationValue->setCreatedAt(new \DateTime());
            $entmanager->persist($observationValue);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('observation_value_index'));
        }

        $context = [
            'title' => 'Observation Value Creation',
            'observationValueForm' => $form->createView()
        ];
        return $this->render('observation_value/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ObservationValue $observationValueSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Observation Value Details',
            'observationValue' => $observationValueSelected
        ];
        return $this->render('observation_value/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ObservationValue $observationValue, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('observation_value_edit', $observationValue);
        $form = $this->createForm(ObservationValueUpdateType::class, $observationValue);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($observationValue);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('observation_value_index'));
        }

        $context = [
            'title' => 'Observation Value Update',
            'observationValueForm' => $form->createView()
        ];
        return $this->render('observation_value/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ObservationValue $observationValue, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($observationValue->getId()) {
            $observationValue->setIsActive(!$observationValue->getIsActive());
        }
        $entmanager->persist($observationValue);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $observationValue->getIsActive()
        ], 200);
    }
}