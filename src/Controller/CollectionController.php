<?php

namespace App\Controller;

use App\Entity\CollectionClass;
use App\Form\CollectionType;
use App\Form\CollectionUpdateType;
use App\Repository\CollectionClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/collection", name="collection_")
 */
class CollectionController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CollectionClassRepository $collectionRepo): Response
    {
        $collections =  $collectionRepo->findAll();
        $context = [
            'title' => 'Collection List',
            'collections' => $collections
        ];
        return $this->render('collection/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $collection = new CollectionClass();
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $collection->setCreatedBy($this->getUser());
            }
            $collection->setIsActive(true);
            $collection->setCreatedAt(new \DateTime());
            $entmanager->persist($collection);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collection_index'));
        }

        $context = [
            'title' => 'Collection Creation',
            'collectionForm' => $form->createView()
        ];
        return $this->render('collection/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(CollectionClass $collectionSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Collection Details',
            'collection' => $collectionSelected
        ];
        return $this->render('collection/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(CollectionClass $collection, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('collection_edit', $collection);
        $form = $this->createForm(CollectionUpdateType::class, $collection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($collection);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collection_index'));
        }

        $context = [
            'title' => 'Collection Update',
            'collectionForm' => $form->createView()
        ];
        return $this->render('collection/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(CollectionClass $collection, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($collection->getId()) {
            $collection->setIsActive(!$collection->getIsActive());
        }
        $entmanager->persist($collection);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $collection->getIsActive()
        ], 200);
    }
}
