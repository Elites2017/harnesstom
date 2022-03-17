<?php

namespace App\Controller;

use App\Entity\CollectingSource;
use App\Repository\CollectingSourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CollectingSourceType;
use App\Form\CollectingSourceUpdateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("collecting/source", name="collecting_source_")
 */
class CollectingSourceController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CollectingSourceRepository $collectingSourceRepo): Response
    {
        $collectingSources =  $collectingSourceRepo->findAll();
        $context = [
            'title' => 'Collecting Source List',
            'collectingSources' => $collectingSources
        ];
        return $this->render('collecting_source/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $collectingSource = new CollectingSource();
        $form = $this->createForm(CollectingSourceType::class, $collectingSource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $collectingSource->setIsActive(true);
            $collectingSource->setCreatedAt(new \DateTime());
            $entmanager->persist($collectingSource);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collecting_source_index'));
        }

        $context = [
            'title' => 'Collecting Source Creation',
            'collectingSourceForm' => $form->createView()
        ];
        return $this->render('collecting_source/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(collectingSource $collectingSourceselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Collecting Source Details',
            'collectingSource' => $collectingSourceselected
        ];
        return $this->render('collecting_source/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(CollectingSource $collectingSource, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(CollectingSourceUpdateType::class, $collectingSource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($collectingSource);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collecting_source_index'));
        }

        $context = [
            'title' => 'Collecting Source Update',
            'collectingSourceForm' => $form->createView()
        ];
        return $this->render('collecting_source/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(CollectingSource $collectingSource, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($collectingSource->getId()) {
            $collectingSource->setIsActive(!$collectingSource->getIsActive());
        }
        $entmanager->persist($collectingSource);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $collectingSource->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}


