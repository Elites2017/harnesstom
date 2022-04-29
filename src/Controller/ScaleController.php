<?php

namespace App\Controller;

use App\Entity\Scale;
use App\Form\ScaleType;
use App\Form\ScaleUpdateType;
use App\Repository\ScaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/scale", name="scale_")
 */
class ScaleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ScaleRepository $scaleRepo): Response
    {
        $scales =  $scaleRepo->findAll();
        $context = [
            'title' => 'Scale List',
            'scales' => $scales
        ];
        return $this->render('scale/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $scale = new Scale();
        $form = $this->createForm(ScaleType::class, $scale);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $scale->setCreatedBy($this->getUser());
            }
            $scale->setIsActive(true);
            $scale->setCreatedAt(new \DateTime());
            $entmanager->persist($scale);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('scale_index'));
        }

        $context = [
            'title' => 'Scale Creation',
            'scaleForm' => $form->createView()
        ];
        return $this->render('scale/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Scale $scaleSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Scale Details',
            'scale' => $scaleSelected
        ];
        return $this->render('scale/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Scale $scale, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('scale_edit', $scale);
        $form = $this->createForm(ScaleUpdateType::class, $scale);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($scale);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('scale_index'));
        }

        $context = [
            'title' => 'Scale Update',
            'scaleForm' => $form->createView()
        ];
        return $this->render('scale/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Scale $scale, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($scale->getId()) {
            $scale->setIsActive(!$scale->getIsActive());
        }
        $entmanager->persist($scale);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $scale->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
