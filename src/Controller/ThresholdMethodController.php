<?php

namespace App\Controller;

use App\Entity\ThresholdMethod;
use App\Form\ThresholdMethodType;
use App\Form\ThresholdMethodUpdateType;
use App\Repository\ThresholdMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/threshold/method", name="threshold_method_")
 */
class ThresholdMethodController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ThresholdMethodRepository $thresholdMethodRepo): Response
    {
        $thresholdMethods =  $thresholdMethodRepo->findAll();
        $context = [
            'title' => 'Threshold Method List',
            'thresholdMethods' => $thresholdMethods
        ];
        return $this->render('threshold_method/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $thresholdMethod = new ThresholdMethod();
        $form = $this->createForm(ThresholdMethodType::class, $thresholdMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $thresholdMethod->setCreatedBy($this->getUser());
            }
            $thresholdMethod->setIsActive(true);
            $thresholdMethod->setCreatedAt(new \DateTime());
            $entmanager->persist($thresholdMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('threshold_method_index'));
        }

        $context = [
            'title' => 'Threshold Method Creation',
            'thresholdMethodForm' => $form->createView()
        ];
        return $this->render('threshold_method/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ThresholdMethod $thresholdMethodSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Threshold Method Details',
            'thresholdMethod' => $thresholdMethodSelected
        ];
        return $this->render('threshold_method/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ThresholdMethod $thresholdMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('threshold_method_edit', $thresholdMethod);
        $form = $this->createForm(ThresholdMethodUpdateType::class, $thresholdMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($thresholdMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('threshold_method_index'));
        }

        $context = [
            'title' => 'Threshold Method Update',
            'thresholdMethodForm' => $form->createView()
        ];
        return $this->render('threshold_method/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ThresholdMethod $thresholdMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($thresholdMethod->getId()) {
            $thresholdMethod->setIsActive(!$thresholdMethod->getIsActive());
        }
        $entmanager->persist($thresholdMethod);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $thresholdMethod->getIsActive()
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
