<?php

namespace App\Controller;

use App\Entity\GrowthFacilityType;
use App\Form\GrowthFacilityCreateType;
use App\Form\GrowthFacilityUpdateType;
use App\Repository\GrowthFacilityTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 // set a class level route
 /**
 * @Route("growth/facility/type", name="growth_facility_type_")
 */
class GrowthFacilityTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GrowthFacilityTypeRepository $growthFacilityTypeRepo): Response
    {
        $growthFacilityTypes =  $growthFacilityTypeRepo->findAll();
        $context = [
            'title' => 'Growth Facility Type List',
            'growthFacilityTypes' => $growthFacilityTypes
        ];
        return $this->render('growth_facility_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $growthFacilityType = new GrowthFacilityType();
        $form = $this->createForm(GrowthFacilityCreateType::class, $growthFacilityType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $growthFacilityType->setCreatedBy($this->getUser());
            }
            $growthFacilityType->setIsActive(true);
            $growthFacilityType->setCreatedAt(new \DateTime());
            $entmanager->persist($growthFacilityType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('growth_facility_type_index'));
        }

        $context = [
            'title' => 'Growth Facility Type Creation',
            'growthFacilityTypeForm' => $form->createView()
        ];
        return $this->render('growth_facility_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GrowthFacilityType $growthFacilityTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Growth Facility Type Details',
            'growthFacilityType' => $growthFacilityTypeSelected
        ];
        return $this->render('growth_facility_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GrowthFacilityType $growthFacilityType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(GrowthFacilityUpdateType::class, $growthFacilityType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($growthFacilityType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('growth_facility_type_index'));
        }

        $context = [
            'title' => 'Growth Facility Type Update',
            'growthFacilityTypeForm' => $form->createView()
        ];
        return $this->render('growth_facility_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GrowthFacilityType $growthFacilityType, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($growthFacilityType->getId()) {
            $growthFacilityType->setIsActive(!$growthFacilityType->getIsActive());
        }
        $entmanager->persist($growthFacilityType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $growthFacilityType->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('growthFacility_home'));
    }
}