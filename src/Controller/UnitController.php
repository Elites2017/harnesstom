<?php

namespace App\Controller;

use App\Entity\Unit;
use App\Form\UnitType;
use App\Form\UnitUpdateType;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/unit", name="unit_")
 */
class UnitController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(UnitRepository $unitRepo): Response
    {
        $units =  $unitRepo->findAll();
        $context = [
            'title' => 'Units',
            'units' => $units
        ];
        return $this->render('unit/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $unit = new Unit();
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $unit->setCreatedBy($this->getUser());
            }
            $unit->setIsActive(true);
            $unit->setCreatedAt(new \DateTime());
            $entmanager->persist($unit);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('unit_index'));
        }

        $context = [
            'title' => 'Unit Creation',
            'unitForm' => $form->createView()
        ];
        return $this->render('unit/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Unit $unitSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Unit Details',
            'unit' => $unitSelected
        ];
        return $this->render('unit/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Unit $unit, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('unit_edit', $unit);
        $form = $this->createForm(UnitUpdateType::class, $unit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($unit);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('unit_index'));
        }

        $context = [
            'title' => 'Unit Update',
            'unitForm' => $form->createView()
        ];
        return $this->render('unit/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Unit $unit, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($unit->getId()) {
            $unit->setIsActive(!$unit->getIsActive());
        }
        $entmanager->persist($unit);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $unit->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('unit_home'));
    }
}
