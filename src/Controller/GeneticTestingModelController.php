<?php

namespace App\Controller;

use App\Entity\GeneticTestingModel;
use App\Form\GeneticTestingModelType;
use App\Form\GeneticTestingModelUpdateType;
use App\Form\GenetingTestingModelUpdateType;
use App\Repository\GeneticTestingModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("genetic/testing/model", name="genetic_testing_model_")
 */
class GeneticTestingModelController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GeneticTestingModelRepository $geneticTestingModelRepo): Response
    {
        $geneticTestingModels =  $geneticTestingModelRepo->findAll();
        $context = [
            'title' => 'Genetic Testing Model List',
            'geneticTestingModels' => $geneticTestingModels
        ];
        return $this->render('genetic_testing_model/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $geneticTestingModel = new GeneticTestingModel();
        $form = $this->createForm(GeneticTestingModelType::class, $geneticTestingModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $geneticTestingModel->setIsActive(true);
            $geneticTestingModel->setCreatedAt(new \DateTime());
            $entmanager->persist($geneticTestingModel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('genetic_testing_model_index'));
        }

        $context = [
            'title' => 'Genetic Testing Model Create',
            'geneticTestingModelForm' => $form->createView()
        ];
        return $this->render('genetic_testing_model/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GeneticTestingModel $geneticTestingModelSelected): Response
    {
        $context = [
            'title' => 'Genetic Testing Model Details',
            'geneticTestingModel' => $geneticTestingModelSelected
        ];
        return $this->render('genetic_testing_model/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GeneticTestingModel $geneticTestingModel, Request $request, EntityManagerInterface $entmanager): Response
    {
        $form = $this->createForm(GeneticTestingModelUpdateType::class, $geneticTestingModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($geneticTestingModel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('genetic_testing_model_index'));
        }

        $context = [
            'title' => 'Genetic Testing Model Update',
            'geneticTestingModelForm' => $form->createView()
        ];
        return $this->render('genetic_testing_model/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GeneticTestingModel $geneticTestingModel, EntityManagerInterface $entmanager): Response
    {
        if ($geneticTestingModel->getId()) {
            $geneticTestingModel->setIsActive(!$geneticTestingModel->getIsActive());
        }
        $entmanager->persist($geneticTestingModel);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $geneticTestingModel->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}
