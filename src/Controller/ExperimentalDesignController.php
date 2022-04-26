<?php

namespace App\Controller;

use App\Entity\ExperimentalDesignType;
use App\Form\ExperimentalDesignCreateType;
use App\Form\ExperimentalDesignUpdateType;
use App\Repository\ExperimentalDesignTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("experimental/design", name="experimental_design_")
 */
class ExperimentalDesignController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ExperimentalDesignTypeRepository $experimentalDesign): Response
    {
        $experimentalDesigns =  $experimentalDesign->findAll();
        $context = [
            'title' => 'Experimental Design List',
            'experimentalDesigns' => $experimentalDesigns
        ];
        return $this->render('experimental_design/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $experimentalDesign = new ExperimentalDesignType();
        $form = $this->createForm(ExperimentalDesignCreateType::class, $experimentalDesign);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $experimentalDesign->setCreatedBy($this->getUser());
            }
            $experimentalDesign->setIsActive(true);
            $experimentalDesign->setCreatedAt(new \DateTime());
            $entmanager->persist($experimentalDesign);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('experimental_design_index'));
        }

        $context = [
            'title' => 'Experimental Design Creation',
            'experimentalDesignForm' => $form->createView()
        ];
        return $this->render('experimental_design/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ExperimentalDesignType $experimentalDesignSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Experimental Design Details',
            'experimentalDesign' => $experimentalDesignSelected
        ];
        return $this->render('experimental_design/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ExperimentalDesignType $experimentalDesign, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(ExperimentalDesignUpdateType::class, $experimentalDesign);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($experimentalDesign);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('experimental_design_index'));
        }

        $context = [
            'title' => 'Experimental Design Update',
            'experimentalDesignForm' => $form->createView()
        ];
        return $this->render('experimental_design/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ExperimentalDesignType $experimentalDesign, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($experimentalDesign->getId()) {
            $experimentalDesign->setIsActive(!$experimentalDesign->getIsActive());
        }
        $entmanager->persist($experimentalDesign);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $experimentalDesign->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('experimentalDesign_home'));
    }
}