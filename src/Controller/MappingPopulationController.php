<?php

namespace App\Controller;

use App\Entity\MappingPopulation;
use App\Form\MappingPopulationType;
use App\Form\MappingPopulationUpdateType;
use App\Repository\MappingPopulationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/mapping/population", name="mapping_population_")
 */
class MappingPopulationController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MappingPopulationRepository $mappingPopulationRepo): Response
    {
        $mappingPopulations =  $mappingPopulationRepo->findAll();
        $context = [
            'title' => 'Mapping Population List',
            'mappingPopulations' => $mappingPopulations
        ];
        return $this->render('mapping_population/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $mappingPopulation = new MappingPopulation();
        $form = $this->createForm(MappingPopulationType::class, $mappingPopulation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $mappingPopulation->setCreatedBy($this->getUser());
            }
            $mappingPopulation->setIsActive(true);
            $mappingPopulation->setCreatedAt(new \DateTime());
            $entmanager->persist($mappingPopulation);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('mapping_population_index'));
        }

        $context = [
            'title' => 'Mapping Population Creation',
            'mappingPopulationForm' => $form->createView()
        ];
        return $this->render('mapping_population/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MappingPopulation $mappingPopulationSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Mapping Population Details',
            'mappingPopulation' => $mappingPopulationSelected
        ];
        return $this->render('mapping_population/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MappingPopulation $mappingPopulation, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('mapping_population_edit', $mappingPopulation);
        $form = $this->createForm(MappingPopulationUpdateType::class, $mappingPopulation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($mappingPopulation);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('mapping_population_index'));
        }

        $context = [
            'title' => 'Mapping Population Update',
            'mappingPopulationForm' => $form->createView()
        ];
        return $this->render('mapping_population/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MappingPopulation $mappingPopulation, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($mappingPopulation->getId()) {
            $mappingPopulation->setIsActive(!$mappingPopulation->getIsActive());
        }
        $entmanager->persist($mappingPopulation);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $mappingPopulation->getIsActive()
        ], 200);
    }
}
