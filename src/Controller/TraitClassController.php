<?php

namespace App\Controller;

use App\Entity\TraitClass;
use App\Form\TraitClassType;
use App\Form\TraitClassUpdateType;
use App\Repository\TraitClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/trait", name="trait_class_")
 */
class TraitClassController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TraitClassRepository $traitClassRepo): Response
    {
        $traitClasses =  $traitClassRepo->findAll();
        $context = [
            'title' => 'Trait List',
            'traitClasses' => $traitClasses
        ];
        return $this->render('trait_class/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $traitClass = new TraitClass();
        $form = $this->createForm(TraitClassType::class, $traitClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $traitClass->setCreatedBy($this->getUser());
            }
            $traitClass->setIsActive(true);
            $traitClass->setCreatedAt(new \DateTime());
            $entmanager->persist($traitClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trait_class_index'));
        }

        $context = [
            'title' => 'Trait Creation',
            'traitClassForm' => $form->createView()
        ];
        return $this->render('trait_class/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(TraitClass $traitClasseSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Trait Details',
            'traitClass' => $traitClasseSelected
        ];
        return $this->render('trait_class/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(TraitClass $traitClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('trait_class_edit', $traitClass);
        $form = $this->createForm(TraitClassUpdateType::class, $traitClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($traitClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trait_class_index'));
        }

        $context = [
            'title' => 'Trait Update',
            'traitClassForm' => $form->createView()
        ];
        return $this->render('trait_class/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(TraitClass $traitClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($traitClass->getId()) {
            $traitClass->setIsActive(!$traitClass->getIsActive());
        }
        $entmanager->persist($traitClass);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $traitClass->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }

    // this is to upload data in bulk using an excel file
    /**
     * @Route("/upload-from-excel", name="upload_from_excel")
     */
    public function uploadFromExcel(Request $request, EntityManagerInterface $entmanager): Response
    {
        dd("Good morning SEASON");
    }
}