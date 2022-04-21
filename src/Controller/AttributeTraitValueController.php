<?php

namespace App\Controller;

use App\Entity\AttributeTraitValue;
use App\Form\AttributeTraitValueType;
use App\Form\AttributeTraitValueUpdateType;
use App\Repository\AttributeTraitValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/attribute/trait/value", name="attribute_trait_value_")
 */
class AttributeTraitValueController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AttributeTraitValueRepository $attributeTraitValueRepo): Response
    {
        $attributeTraitValues =  $attributeTraitValueRepo->findAll();
        $context = [
            'title' => 'Attribute Trait Value List',
            'attributeTraitValues' => $attributeTraitValues
        ];
        return $this->render('attribute_trait_value/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $attributeTraitValue = new AttributeTraitValue();
        $form = $this->createForm(AttributeTraitValueType::class, $attributeTraitValue);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                    $attributeTraitValue->setCreatedBy($this->getUser());
                }
            $attributeTraitValue->setIsActive(true);
            $attributeTraitValue->setCreatedAt(new \DateTime());
            $entmanager->persist($attributeTraitValue);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('attribute_trait_value_index'));
        }

        $context = [
            'title' => 'Attribute Trait Value Creation',
            'attributeTraitValueForm' => $form->createView()
        ];
        return $this->render('attribute_trait_value/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AttributeTraitValue $attributeTraitValueSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Attribute Trait Value Details',
            'attributeTraitValue' => $attributeTraitValueSelected
        ];
        return $this->render('attribute_trait_value/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AttributeTraitValue $attributeTraitValue, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('attribute_trait_value_edit', $attributeTraitValue);
        $form = $this->createForm(AttributeTraitValueUpdateType::class, $attributeTraitValue);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($attributeTraitValue);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('attribute_trait_value_index'));
        }

        $context = [
            'title' => 'Attribute Trait Value Update',
            'attributeTraitValueForm' => $form->createView()
        ];
        return $this->render('attribute_trait_value/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(AttributeTraitValue $attributeTraitValue, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($attributeTraitValue->getId()) {
            $attributeTraitValue->setIsActive(!$attributeTraitValue->getIsActive());
        }
        $entmanager->persist($attributeTraitValue);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $attributeTraitValue->getIsActive()
        ], 200);
    }
}
