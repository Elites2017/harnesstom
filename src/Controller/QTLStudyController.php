<?php

namespace App\Controller;

use App\Entity\QTLStudy;
use App\Form\QTLStudyType;
use App\Form\QTLStudyUpdateType;
use App\Repository\QTLStudyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/qtl/study", name="qtl_study_")
 */
class QTLStudyController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(QTLStudyRepository $qtlStudyRepo): Response
    {
        $qtlStudies =  $qtlStudyRepo->findAll();
        $context = [
            'title' => 'QTL Study',
            'qtlStudies' => $qtlStudies
        ];
        return $this->render('qtl_study/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $qtlStudy = new QTLStudy();
        $form = $this->createForm(QTLStudyType::class, $qtlStudy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $qtlStudy->setCreatedBy($this->getUser());
            }
            $qtlStudy->setIsActive(true);
            $qtlStudy->setCreatedAt(new \DateTime());
            $entmanager->persist($qtlStudy);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_study_index'));
        }

        $context = [
            'title' => 'QTL Study Creation',
            'qtlStudyForm' => $form->createView()
        ];
        return $this->render('qtl_study/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(QTLStudy $qtlStudySelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'QTL Study Details',
            'qtlStudy' => $qtlStudySelected
        ];
        return $this->render('qtl_study/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(QTLStudy $qtlStudy, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('qtl_study_edit', $qtlStudy);
        $form = $this->createForm(QTLStudyUpdateType::class, $qtlStudy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($qtlStudy);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_study_index'));
        }

        $context = [
            'title' => 'QTL Study Update',
            'qtlStudyForm' => $form->createView()
        ];
        return $this->render('qtl_study/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(QTLStudy $qtlStudy, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($qtlStudy->getId()) {
            $qtlStudy->setIsActive(!$qtlStudy->getIsActive());
        }
        $entmanager->persist($qtlStudy);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $qtlStudy->getIsActive()
        ], 200);
    }
}
