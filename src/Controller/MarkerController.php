<?php

namespace App\Controller;

use App\Entity\Marker;
use App\Form\MarkerType;
use App\Form\MarkerUpdateType;
use App\Repository\MarkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/marker", name="marker_")
 */
class MarkerController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MarkerRepository $markerRepo): Response
    {
        $markers =  $markerRepo->findAll();
        $context = [
            'title' => 'Marker List',
            'markers' => $markers
        ];
        return $this->render('marker/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $marker = new Marker();
        $form = $this->createForm(MarkerType::class, $marker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $marker->setCreatedBy($this->getUser());
            }
            $marker->setIsActive(true);
            $marker->setCreatedAt(new \DateTime());
            $entmanager->persist($marker);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('marker_index'));
        }

        $context = [
            'title' => 'Marker Creation',
            'markerForm' => $form->createView()
        ];
        return $this->render('marker/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Marker $markerSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Marker Details',
            'marker' => $markerSelected
        ];
        return $this->render('marker/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Marker $marker, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('marker_edit', $marker);
        $form = $this->createForm(MarkerUpdateType::class, $marker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($marker);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('marker_index'));
        }

        $context = [
            'title' => 'Marker Update',
            'markerForm' => $form->createView()
        ];
        return $this->render('marker/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Marker $marker, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($marker->getId()) {
            $marker->setIsActive(!$marker->getIsActive());
        }
        $entmanager->persist($marker);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $marker->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

