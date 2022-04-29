<?php

namespace App\Controller;

use App\Entity\GenotypingPlatform;
use App\Form\GenotypingPlatformType;
use App\Form\GenotypingPlatformUpdateType;
use App\Repository\GenotypingPlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/genotyping/platform", name="genotyping_platform_")
 */
class GenotypingPlatformController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GenotypingPlatformRepository $genotypingPlatformRepo): Response
    {
        $genotypingPlatforms =  $genotypingPlatformRepo->findAll();
        $context = [
            'title' => 'Genotyping Platform List',
            'genotypingPlatforms' => $genotypingPlatforms
        ];
        return $this->render('genotyping_platform/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $genotypingPlatform = new GenotypingPlatform();
        $form = $this->createForm(GenotypingPlatformType::class, $genotypingPlatform);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $genotypingPlatform->setCreatedBy($this->getUser());
            }
            $genotypingPlatform->setIsActive(true);
            $genotypingPlatform->setCreatedAt(new \DateTime());
            $entmanager->persist($genotypingPlatform);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('genotyping_platform_index'));
        }

        $context = [
            'title' => 'Genotyping Platform Creation',
            'genotypingPlatformForm' => $form->createView()
        ];
        return $this->render('genotyping_platform/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GenotypingPlatform $genotypingPlatformSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Genotyping Platform Details',
            'genotypingPlatform' => $genotypingPlatformSelected
        ];
        return $this->render('genotyping_platform/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GenotypingPlatform $genotypingPlatform, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('genotyping_platform_edit', $genotypingPlatform);
        $form = $this->createForm(GenotypingPlatformUpdateType::class, $genotypingPlatform);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($genotypingPlatform);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('genotyping_platform_index'));
        }

        $context = [
            'title' => 'Genotyping Platform Update',
            'genotypingPlatformForm' => $form->createView()
        ];
        return $this->render('genotyping_platform/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GenotypingPlatform $genotypingPlatform, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($genotypingPlatform->getId()) {
            $genotypingPlatform->setIsActive(!$genotypingPlatform->getIsActive());
        }
        $entmanager->persist($genotypingPlatform);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $genotypingPlatform->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

