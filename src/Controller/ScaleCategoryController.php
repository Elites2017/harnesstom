<?php

namespace App\Controller;

use App\Entity\ScaleCategory;
use App\Form\ScaleCategoryType;
use App\Form\ScaleCategoryUpdateType;
use App\Repository\ScaleCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("scale/category", name="scale_category_")
 */
class ScaleCategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ScaleCategoryRepository $scaleCategoryRepo): Response
    {
        $scaleCategories =  $scaleCategoryRepo->findAll();
        $context = [
            'title' => 'Scale Category List',
            'scaleCategories' => $scaleCategories
        ];
        return $this->render('scale_category/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $scaleCategory = new ScaleCategory();
        $form = $this->createForm(ScaleCategoryType::class, $scaleCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $scaleCategory->setCreatedBy($this->getUser());
            }
            $scaleCategory->setIsActive(true);
            $scaleCategory->setCreatedAt(new \DateTime());
            $entmanager->persist($scaleCategory);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('scale_category_index'));
        }

        $context = [
            'title' => 'Scale Category Creation',
            'scaleCategoryForm' => $form->createView()
        ];
        return $this->render('scale_category/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ScaleCategory $scaleCategorySelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Scale Category Details',
            'scaleCategory' => $scaleCategorySelected
        ];
        return $this->render('scale_category/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ScaleCategory $scaleCategory, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(ScaleCategoryUpdateType::class, $scaleCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($scaleCategory);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('scale_category_index'));
        }

        $context = [
            'title' => 'Scale Category Update',
            'scaleCategoryForm' => $form->createView()
        ];
        return $this->render('scale_category/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ScaleCategory $scaleCategory, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($scaleCategory->getId()) {
            $scaleCategory->setIsActive(!$scaleCategory->getIsActive());
        }
        $entmanager->persist($scaleCategory);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $scaleCategory->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
