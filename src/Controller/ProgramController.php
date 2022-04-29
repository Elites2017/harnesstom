<?php

namespace App\Controller;

use App\Entity\Program;
use App\Form\ProgramType;
use App\Form\ProgramUpdateType;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/program", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ProgramRepository $programRepo): Response
    {
        $programs =  $programRepo->findAll();
        $context = [
            'title' => 'Program List',
            'programs' => $programs
        ];
        return $this->render('program/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $program->setCreatedBy($this->getUser());
            }
            $program->setIsActive(true);
            $program->setCreatedAt(new \DateTime());
            $entmanager->persist($program);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('program_index'));
        }

        $context = [
            'title' => 'Program Creation',
            'programForm' => $form->createView()
        ];
        return $this->render('program/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Program $programSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Program Details',
            'program' => $programSelected
        ];
        return $this->render('program/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Program $program, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('program_edit', $program);
        $form = $this->createForm(ProgramUpdateType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($program);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('program_index'));
        }

        $context = [
            'title' => 'Program Update',
            'programForm' => $form->createView()
        ];
        return $this->render('program/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Program $program, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($program->getId()) {
            $program->setIsActive(!$program->getIsActive());
        }
        $entmanager->persist($program);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $program->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

