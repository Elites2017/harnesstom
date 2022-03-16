<?php

namespace App\Controller;

use App\Entity\StructureMethod;
use App\Form\StructureMethodType;
use App\Form\StructureMethodUpdateType;
use App\Repository\StructureMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("structure/method", name="structure_method_")
 */
class StructureMethodController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(StructureMethodRepository $structureMethodRepo): Response
    {
        $structureMethods =  $structureMethodRepo->findAll();
        $context = [
            'title' => 'Structure Method List',
            'structureMethods' => $structureMethods
        ];
        return $this->render('structure_method/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $structureMethod = new StructureMethod();
        $form = $this->createForm(StructureMethodType::class, $structureMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $structureMethod->setIsActive(true);
            $structureMethod->setCreatedAt(new \DateTime());
            $entmanager->persist($structureMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('structure_method_index'));
        }

        $context = [
            'title' => 'Structure Method Creation',
            'structureMethodForm' => $form->createView()
        ];
        return $this->render('structure_method/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(StructureMethod $structureMethodSelected): Response
    {
        $context = [
            'title' => 'Structure Method Details',
            'structureMethod' => $structureMethodSelected
        ];
        return $this->render('structure_method/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(StructureMethod $structureMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $form = $this->createForm(StructureMethodUpdateType::class, $structureMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($structureMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('structure_method_index'));
        }

        $context = [
            'title' => 'Structure Method Update',
            'structureMethodForm' => $form->createView()
        ];
        return $this->render('structure_method/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(StructureMethod $structureMethod, EntityManagerInterface $entmanager): Response
    {
        if ($structureMethod->getId()) {
            $structureMethod->setIsActive(!$structureMethod->getIsActive());
        }
        $entmanager->persist($structureMethod);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $structureMethod->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('structure_method_home'));
    }
}
