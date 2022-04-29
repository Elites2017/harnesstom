<?php

namespace App\Controller;

use App\Entity\StudyParameterValue;
use App\Form\StudyParameterValueType;
use App\Form\StudyParameterValueUpdateType;
use App\Repository\StudyParameterValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/study/parameter/value", name="study_parameter_value_")
 */
class StudyParameterValueController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(StudyParameterValueRepository $studyParamValRepo): Response
    {
        $studyParameterValues =  $studyParamValRepo->findAll();
        $context = [
            'title' => 'Study Parameter Value List',
            'studyParameterValues' => $studyParameterValues
        ];
        return $this->render('study_parameter_value/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $studyParamVal = new StudyParameterValue();
        $form = $this->createForm(StudyParameterValueType::class, $studyParamVal);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $studyParamVal->setCreatedBy($this->getUser());
            }
            $studyParamVal->setIsActive(true);
            $studyParamVal->setCreatedAt(new \DateTime());
            $entmanager->persist($studyParamVal);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('study_parameter_value_index'));
        }

        $context = [
            'title' => 'Study Parameter Value Creation',
            'studyParameterValueForm' => $form->createView()
        ];
        return $this->render('study_parameter_value/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(StudyParameterValue $studyParamValSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Study Parameter Value Details',
            'studyParameterValue' => $studyParamValSelected
        ];
        return $this->render('study_parameter_value/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(StudyParameterValue $studyParamVal, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('study_parameter_value_edit', $studyParamVal);
        $form = $this->createForm(StudyParameterValueUpdateType::class, $studyParamVal);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($studyParamVal);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('study_parameter_value_index'));
        }

        $context = [
            'title' => 'Study Parameter Value Update',
            'studyParameterValueForm' => $form->createView()
        ];
        return $this->render('study_parameter_value/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(StudyParameterValue $studyParamVal, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($studyParamVal->getId()) {
            $studyParamVal->setIsActive(!$studyParamVal->getIsActive());
        }
        $entmanager->persist($studyParamVal);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $studyParamVal->getIsActive()
        ], 200);
    }
}
