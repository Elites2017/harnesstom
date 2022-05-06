<?php

namespace App\Controller;

use App\Entity\GermplasmStudyImage;
use App\Form\GermplasmStudyImageType;
use App\Form\GermplasmStudyImageUpdateType;
use App\Repository\GermplasmStudyImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/germplasm/study/image", name="germplasm_study_")
 */
class GermplasmStudyImageController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GermplasmStudyImageRepository $germplasmStudyImageRepo): Response
    {
        $studies =  $germplasmStudyImageRepo->findAll();
        $context = [
            'title' => 'Germplasm Study Image List',
            'studies' => $studies
        ];
        return $this->render('germplasm_study_image/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $germplasmStudyImage = new GermplasmStudyImage();
        $form = $this->createForm(GermplasmStudyImageType::class, $germplasmStudyImage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $germplasmStudyImage->setCreatedBy($this->getUser());
            }
            $germplasmStudyImage->setIsActive(true);
            $germplasmStudyImage->setCreatedAt(new \DateTime());
            $entmanager->persist($germplasmStudyImage);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('study_index'));
        }

        $context = [
            'title' => 'Germplasm Study Image Creation',
            'germplasmStudyImageForm' => $form->createView()
        ];
        return $this->render('germplasm_study_image/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GermplasmStudyImage $studieSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Germplasm Study Image Details',
            'study' => $studieSelected
        ];
        return $this->render('germplasm_study_image/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GermplasmStudyImage $germplasmStudyImage, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('study_edit', $germplasmStudyImage);
        $form = $this->createForm(GermplasmStudyImageUpdateType::class, $germplasmStudyImage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($germplasmStudyImage);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('study_index'));
        }

        $context = [
            'title' => 'Germplasm Study Image Update',
            'germplasmStudyImageForm' => $form->createView()
        ];
        return $this->render('germplasm_study_image/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(GermplasmStudyImage $germplasmStudyImage, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($germplasmStudyImage->getId()) {
            $germplasmStudyImage->setIsActive(!$germplasmStudyImage->getIsActive());
        }
        $entmanager->persist($germplasmStudyImage);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $germplasmStudyImage->getIsActive()
        ], 200);
    }
}
