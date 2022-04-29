<?php

namespace App\Controller;

use App\Entity\Attribute;
use App\Form\AttributeType;
use App\Form\AttributeUpdateType;
use App\Repository\AttributeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/attribute", name="attribute_")
 */
class AttributeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AttributeRepository $attributeRepo): Response
    {
        $attributes =  $attributeRepo->findAll();
        $context = [
            'title' => 'Attribute List',
            'attributes' => $attributes
        ];
        return $this->render('attribute/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $attribute = new Attribute();
        $form = $this->createForm(AttributeType::class, $attribute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $attribute->setCreatedBy($this->getUser());
            }
            $attribute->setIsActive(true);
            $attribute->setCreatedAt(new \DateTime());
            $entmanager->persist($attribute);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('attribute_index'));
        }

        $context = [
            'title' => 'Attribute Creation',
            'attributeForm' => $form->createView()
        ];
        return $this->render('attribute/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Attribute $attributeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Attribute Details',
            'attribute' => $attributeSelected
        ];
        return $this->render('attribute/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Attribute $attribute, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('attribute_edit', $attribute);
        $form = $this->createForm(AttributeUpdateType::class, $attribute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($attribute);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('attribute_index'));
        }

        $context = [
            'title' => 'Attribute Update',
            'attributeForm' => $form->createView()
        ];
        return $this->render('attribute/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Attribute $attribute, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($attribute->getId()) {
            $attribute->setIsActive(!$attribute->getIsActive());
        }
        $entmanager->persist($attribute);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $attribute->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
