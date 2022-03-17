<?php

namespace App\Controller;

use App\Entity\GWASModel;
use App\Form\GWASModelType;
use App\Form\GWASModelUpdateType;
use App\Repository\GWASModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 // set a class level route
 /**
 * @Route("gwas/model", name="gwas_model_")
 */
class GWASModelController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GWASModelRepository $gwasModelRepo): Response
    {
        $gwasModels =  $gwasModelRepo->findAll();
        $context = [
            'title' => 'GWAS Model List',
            'gwasModels' => $gwasModels
        ];
        return $this->render('gwas_model/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $gwasModel = new GWASModel();
        $form = $this->createForm(GWASModelType::class, $gwasModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $gwasModel->setIsActive(true);
            $gwasModel->setCreatedAt(new \DateTime());
            $entmanager->persist($gwasModel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_model_index'));
        }

        $context = [
            'title' => 'GWAS Model Creation',
            'gwasModelForm' => $form->createView()
        ];
        return $this->render('gwas_model/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GWASModel $gwasModelSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'GWAS Model Details',
            'gwasModel' => $gwasModelSelected
        ];
        return $this->render('gwas_model/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GWASModel $gwasModel, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(GWASModelUpdateType::class, $gwasModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($gwasModel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_model_index'));
        }

        $context = [
            'title' => 'GWAS Model Update',
            'gwasModelForm' => $form->createView()
        ];
        return $this->render('gwas_model/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GWASModel $gwasModel, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($gwasModel->getId()) {
            $gwasModel->setIsActive(!$gwasModel->getIsActive());
        }
        $entmanager->persist($gwasModel);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $gwasModel->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('growthFacility_home'));
    }
}