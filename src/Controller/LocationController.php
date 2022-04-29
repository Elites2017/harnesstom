<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use App\Form\LocationUpdateType;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/location", name="location_")
 */
class LocationController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(LocationRepository $locationRepo): Response
    {
        $locations =  $locationRepo->findAll();
        $context = [
            'title' => 'Location List',
            'locations' => $locations
        ];
        return $this->render('location/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $location->setCreatedBy($this->getUser());
            }
            $location->setIsActive(true);
            $location->setCreatedAt(new \DateTime());
            $entmanager->persist($location);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('location_index'));
        }

        $context = [
            'title' => 'Location Creation',
            'locationForm' => $form->createView()
        ];
        return $this->render('location/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Location $locationSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Location Details',
            'location' => $locationSelected
        ];
        return $this->render('location/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Location $location, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('location_edit', $location);
        $form = $this->createForm(LocationUpdateType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($location);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('location_index'));
        }

        $context = [
            'title' => 'Location Update',
            'locationForm' => $form->createView()
        ];
        return $this->render('location/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Location $location, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($location->getId()) {
            $location->setIsActive(!$location->getIsActive());
        }
        $entmanager->persist($location);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $location->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
