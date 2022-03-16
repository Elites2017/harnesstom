<?php

namespace App\Controller;

use App\Entity\MethodClass;
use App\Form\MethodClassType;
use App\Form\MethodClassUpdateType;
use App\Repository\MethodClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/method/class", name="method_class_")
 */
class MethodClassController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MethodClassRepository $methodClassRepo): Response
    {
        $methodClasses =  $methodClassRepo->findAll();
        $context = [
            'title' => 'Method Class List',
            'methodClasses' => $methodClasses
        ];
        return $this->render('method_class/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $methodClass = new MethodClass();
        $form = $this->createForm(MethodClassType::class, $methodClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $methodClass->setIsActive(true);
            $methodClass->setCreatedAt(new \DateTime());
            $entmanager->persist($methodClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('method_class_index'));
        }

        $context = [
            'title' => 'Method Class Creation',
            'methodClassForm' => $form->createView()
        ];
        return $this->render('method_class/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MethodClass $methodClassSelected): Response
    {
        $context = [
            'title' => 'Method Class Details',
            'methodClass' => $methodClassSelected
        ];
        return $this->render('method_class/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MethodClass $methodClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        $form = $this->createForm(MethodClassUpdateType::class, $methodClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($methodClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('method_class_index'));
        }

        $context = [
            'title' => 'Method Class Update',
            'methodClassForm' => $form->createView()
        ];
        return $this->render('method_class/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MethodClass $methodClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        if ($methodClass->getId()) {
            $methodClass->setIsActive(!$methodClass->getIsActive());
        }
        $entmanager->persist($methodClass);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $methodClass->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
