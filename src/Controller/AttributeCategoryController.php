<?php

namespace App\Controller;

use App\Entity\AttributeCategory;
use App\Form\AttributeCategoryType;
use App\Form\AttributeCategoryUpdateType;
use App\Repository\AttributeCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("attribute/category", name="attribute_category_")
 */
class AttributeCategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AttributeCategoryRepository $attributeCategoryRepo): Response
    {
        $attributeCategories =  $attributeCategoryRepo->findAll();
        $context = [
            'title' => 'Anatomical Entity List',
            'attributeCategories' => $attributeCategories
        ];
        return $this->render('attribute_category/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $attributeCategory = new AttributeCategory();
        $form = $this->createForm(AttributeCategoryType::class, $attributeCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $attributeCategory->setIsActive(true);
            $attributeCategory->setCreatedAt(new \DateTime());
            $entmanager->persist($attributeCategory);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('attribute_category_index'));
        }

        $context = [
            'title' => 'Attribute Category',
            'attributeCategoryForm' => $form->createView()
        ];
        return $this->render('attribute_category/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AttributeCategory $attributeCategorySelected): Response
    {
        $context = [
            'title' => 'Attribute Category',
            'attributeCategory' => $attributeCategorySelected
        ];
        return $this->render('attribute_category/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AttributeCategory $attributeCategory, Request $request, EntityManagerInterface $entmanager): Response
    {
        $form = $this->createForm(AttributeCategoryUpdateType::class, $attributeCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($attributeCategory);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('attribute_category_index'));
        }

        $context = [
            'title' => 'Attribute Category Update',
            'attributeCategoryForm' => $form->createView()
        ];
        return $this->render('attribute_category/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AttributeCategory $attributeCategory, EntityManagerInterface $entmanager): Response
    {
        if ($attributeCategory->getId()) {
            $attributeCategory->setIsActive(!$attributeCategory->getIsActive());
        }
        $entmanager->persist($attributeCategory);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $attributeCategory->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}
