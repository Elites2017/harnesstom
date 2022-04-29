<?php

namespace App\Controller;

use App\Entity\QTLMethod;
use App\Form\QTLMethodType;
use App\Form\QTLMethodUpdateType;
use App\Repository\QTLMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/qtl/method", name="qtl_method_")
 */
class QTLMethodController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(QTLMethodRepository $qtlMethodRepo): Response
    {
        $qtlMethods =  $qtlMethodRepo->findAll();
        $context = [
            'title' => 'QTL Method List',
            'qtlMethods' => $qtlMethods
        ];
        return $this->render('qtl_method/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $qtlMethod = new QTLMethod();
        $form = $this->createForm(QTLMethodType::class, $qtlMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $qtlMethod->setCreatedBy($this->getUser());
            }
            $qtlMethod->setIsActive(true);
            $qtlMethod->setCreatedAt(new \DateTime());
            $entmanager->persist($qtlMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_method_index'));
        }

        $context = [
            'title' => 'QTL Method Creation',
            'qtlMethodForm' => $form->createView()
        ];
        return $this->render('qtl_method/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(QTLMethod $qtlMethodSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'QTL Method Details',
            'qtlMethod' => $qtlMethodSelected
        ];
        return $this->render('qtl_method/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(QTLMethod $qtlMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('qtl_method_edit', $qtlMethod);
        $form = $this->createForm(QTLMethodUpdateType::class, $qtlMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($qtlMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_method_index'));
        }

        $context = [
            'title' => 'QTL Method Update',
            'qtlMethodForm' => $form->createView()
        ];
        return $this->render('qtl_method/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(QTLMethod $qtlMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($qtlMethod->getId()) {
            $qtlMethod->setIsActive(!$qtlMethod->getIsActive());
        }
        $entmanager->persist($qtlMethod);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $qtlMethod->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

