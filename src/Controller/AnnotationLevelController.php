<?php

namespace App\Controller;

use App\Entity\AnnotationLevel;
use App\Form\AnnotationLevelType;
use App\Form\AnnotationLevelUpdateType;
use App\Repository\AnnotationLevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("annotation/level", name="annotation_level_")
 */
class AnnotationLevelController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AnnotationLevelRepository $annotationLevelRepo): Response
    {
        $annotationLevels =  $annotationLevelRepo->findAll();
        $context = [
            'title' => 'Annotation Level List',
            'annotationLevels' => $annotationLevels
        ];
        return $this->render('annotation_level/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $annotationLevel = new AnnotationLevel();
        $form = $this->createForm(AnnotationLevelType::class, $annotationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $annotationLevel->setIsActive(true);
            $annotationLevel->setCreatedAt(new \DateTime());
            $entmanager->persist($annotationLevel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('annotation_level_index'));
        }

        $context = [
            'title' => 'Annotation Level Creation',
            'annotationLevelForm' => $form->createView()
        ];
        return $this->render('annotation_level/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AnnotationLevel $annotationLevelSelected): Response
    {
        $context = [
            'title' => 'Annotation Level Details',
            'annotationLevel' => $annotationLevelSelected
        ];
        return $this->render('annotation_level/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AnnotationLevel $annotationLevel, Request $request, EntityManagerInterface $entmanager): Response
    {
        $form = $this->createForm(AnnotationLevelUpdateType::class, $annotationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($annotationLevel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('annotation_level_index'));
        }

        $context = [
            'title' => 'Annotation Level Update',
            'annotationLevelForm' => $form->createView()
        ];
        return $this->render('annotation_level/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AnnotationLevel $annotationLevel, EntityManagerInterface $entmanager): Response
    {
        if ($annotationLevel->getId()) {
            $annotationLevel->setIsActive(!$annotationLevel->getIsActive());
        }
        $entmanager->persist($annotationLevel);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $annotationLevel->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}

