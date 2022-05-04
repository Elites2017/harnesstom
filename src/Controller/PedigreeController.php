<?php

namespace App\Controller;

use App\Entity\Pedigree;
use App\Form\PedigreeType;
use App\Form\PedigreeUpdateType;
use App\Repository\PedigreeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/pedigree", name="pedigree_")
 */
class PedigreeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PedigreeRepository $pedigreeRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $pedigrees =  $pedigreeRepo->findAll();
        $context = [
            'title' => 'Pedigree',
            'pedigrees' => $pedigrees
        ];
        return $this->render('pedigree/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $pedigree = new Pedigree();
        $form = $this->createForm(PedigreeType::class, $pedigree);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $pedigree->setCreatedBy($this->getUser());
            }
            $pedigree->setIsActive(true);
            $pedigree->setCreatedAt(new \DateTime());
            $entmanager->persist($pedigree);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('pedigree_index'));
        }

        $context = [
            'title' => 'Pedigree Creation',
            'pedigreeForm' => $form->createView()
        ];
        return $this->render('pedigree/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Pedigree $pedigreeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Pedigree Details',
            'pedigree' => $pedigreeSelected
        ];
        return $this->render('pedigree/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Pedigree $pedigree, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('pedigree_edit', $pedigree);
        $form = $this->createForm(PedigreeUpdateType::class, $pedigree);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($pedigree);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('pedigree_index'));
        }

        $context = [
            'title' => 'Pedigree Update',
            'pedigreeForm' => $form->createView()
        ];
        return $this->render('pedigree/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Pedigree $pedigree, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($pedigree->getId()) {
            $pedigree->setIsActive(!$pedigree->getIsActive());
        }
        $entmanager->persist($pedigree);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $pedigree->getIsActive()
        ], 200);
    }
}