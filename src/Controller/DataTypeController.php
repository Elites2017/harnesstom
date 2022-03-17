<?php

namespace App\Controller;

use App\Entity\DataType;
use App\Form\DataCreateType;
use App\Form\DataUpdateType;
use App\Repository\DataTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/data/type", name="data_type_")
 */
class DataTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(DataTypeRepository $dataTypeRepo): Response
    {
        $dataTypes =  $dataTypeRepo->findAll();
        $context = [
            'title' => 'Data Type',
            'dataTypes' => $dataTypes
        ];
        return $this->render('data_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $dataType = new DataType();
        $form = $this->createForm(DataCreateType::class, $dataType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dataType->setIsActive(true);
            $dataType->setCreatedAt(new \DateTime());
            $entmanager->persist($dataType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('data_type_index'));
        }

        $context = [
            'title' => 'Data Type Creation',
            'dataTypeForm' => $form->createView()
        ];
        return $this->render('data_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(DataType $dataTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Data Type Details',
            'dataType' => $dataTypeSelected
        ];
        return $this->render('data_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(DataType $dataType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(DataUpdateType::class, $dataType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($dataType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('data_type_index'));
        }

        $context = [
            'title' => 'Data Type Update',
            'dataTypeForm' => $form->createView()
        ];
        return $this->render('data_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(DataType $dataType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($dataType->getId()) {
            $dataType->setIsActive(!$dataType->getIsActive());
        }
        $entmanager->persist($dataType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $dataType->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
