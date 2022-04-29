<?php

namespace App\Controller;

use App\Entity\Institute;
use App\Form\InstituteType;
use App\Form\InstituteUpdateType;
use App\Repository\InstituteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/institute", name="institute_")
 */
class InstituteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(InstituteRepository $instituteRepo): Response
    {
        $institutes =  $instituteRepo->findAll();
        $context = [
            'title' => 'Institute List',
            'institutes' => $institutes
        ];
        return $this->render('institute/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $institute = new Institute();
        $form = $this->createForm(InstituteType::class, $institute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $institute->setCreatedBy($this->getUser());
            }
            $institute->setIsActive(true);
            $institute->setCreatedAt(new \DateTime());
            $entmanager->persist($institute);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('institute_index'));
        }

        $context = [
            'title' => 'Institute Creation',
            'instituteForm' => $form->createView()
        ];
        return $this->render('institute/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Institute $instituteSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Institute Details',
            'institute' => $instituteSelected
        ];
        return $this->render('institute/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Institute $institute, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('institute_edit', $institute);
        $form = $this->createForm(InstituteUpdateType::class, $institute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($institute);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('institute_index'));
        }

        $context = [
            'title' => 'Institute Update',
            'instituteForm' => $form->createView()
        ];
        return $this->render('institute/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Institute $institute, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($institute->getId()) {
            $institute->setIsActive(!$institute->getIsActive());
        }
        $entmanager->persist($institute);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $institute->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
