<?php

namespace App\Controller;

use App\Entity\StudyImage;
use App\Form\StudyImageType;
use App\Form\StudyImageUpdateType;
use App\Repository\StudyImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("study/image", name="study_image_")
 */
class StudyImageController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(StudyImageRepository $studyImageRepo): Response
    {
        $studyImages =  $studyImageRepo->findAll();
        $context = [
            'title' => 'Study Image List',
            'studyImages' => $studyImages
        ];
        return $this->render('study_image/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $studyImage = new StudyImage();
        $form = $this->createForm(StudyImageType::class, $studyImage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $studyImage->setCreatedBy($this->getUser());
            }
            $studyImage->setIsActive(true);
            $studyImage->setCreatedAt(new \DateTime());
            $entmanager->persist($studyImage);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('study_image_index'));
        }

        $context = [
            'title' => 'Study Image Creation',
            'studyImageForm' => $form->createView()
        ];
        return $this->render('study_image/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(StudyImage $studyImageSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Study Image Details',
            'studyImage' => $studyImageSelected
        ];
        return $this->render('study_image/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(StudyImage $studyImage, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('study_image_edit', $studyImage);
        $form = $this->createForm(StudyImageUpdateType::class, $studyImage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($studyImage);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('study_image_index'));
        }

        $context = [
            'title' => 'Study Image Update',
            'studyImageForm' => $form->createView()
        ];
        return $this->render('study_image/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(StudyImage $studyImage, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($studyImage->getId()) {
            $studyImage->setIsActive(!$studyImage->getIsActive());
        }
        $entmanager->persist($studyImage);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $studyImage->getIsActive()
        ], 200);
    }
}
