<?php

namespace App\Controller;

use App\Entity\AnalyteClass;
use App\Form\AnalyteClassType;
use App\Form\AnalyteClassUpdateType;
use App\Repository\AnalyteClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("analyte/class", name="analyte_class_")
 */
class AnalyteClassController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AnalyteClassRepository $analyteClassRepo): Response
    {
        $analyteClasses =  $analyteClassRepo->findAll();
        $context = [
            'title' => 'Analyte Class List',
            'analyteClasses' => $analyteClasses
        ];
        return $this->render('analyte_class/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $analyteClass = new AnalyteClass();
        $form = $this->createForm(AnalyteClassType::class, $analyteClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $analyteClass->setCreatedBy($this->getUser());
            }
            $analyteClass->setIsActive(true);
            $analyteClass->setCreatedAt(new \DateTime());
            $entmanager->persist($analyteClass);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('analyte_class_index'));
        }

        $context = [
            'title' => 'Analyte Class Creation',
            'analyteClassForm' => $form->createView()
        ];
        return $this->render('analyte_class/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(analyteClass $analyteClassSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Analyte Class Details',
            'analyteClass' => $analyteClassSelected
        ];
        return $this->render('analyte_class/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AnalyteClass $analyteClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('analyte_class_edit', $analyteClass);
        $form = $this->createForm(AnalyteClassUpdateType::class, $analyteClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($analyteClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('analyte_class_index'));
        }

        $context = [
            'title' => 'Analyte Class Update',
            'analyteClassForm' => $form->createView()
        ];
        return $this->render('analyte_class/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AnalyteClass $analyteClass, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($analyteClass->getId()) {
            $analyteClass->setIsActive(!$analyteClass->getIsActive());
        }
        $entmanager->persist($analyteClass);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $analyteClass->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}

