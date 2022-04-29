<?php

namespace App\Controller;

use App\Entity\VariantSetMetadata;
use App\Form\VariantSetMetadataType;
use App\Form\VariantSetMetadataUpdateType;
use App\Repository\VariantSetMetadataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/variant/set/metadata", name="variant_set_metadata_")
 */
class VariantSetMetadataController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(VariantSetMetadataRepository $variantSetMetadataRepo): Response
    {
        $variantSetMetadatas =  $variantSetMetadataRepo->findAll();
        $context = [
            'title' => 'Variant Set Metadata List',
            'variantSetMetadatas' => $variantSetMetadatas
        ];
        return $this->render('variant_set_metadata/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variantSetMetadata = new VariantSetMetadata();
        $form = $this->createForm(VariantSetMetadataType::class, $variantSetMetadata);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $variantSetMetadata->setCreatedBy($this->getUser());
            }
            $variantSetMetadata->setIsActive(true);
            $variantSetMetadata->setCreatedAt(new \DateTime());
            $entmanager->persist($variantSetMetadata);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('variant_set_metadata_index'));
        }

        $context = [
            'title' => 'Variant Set Metadata Creation',
            'variantSetMetadataForm' => $form->createView()
        ];
        return $this->render('variant_set_metadata/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(VariantSetMetadata $variantSetMetadataSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Variant Set Metadata Details',
            'variantSetMetadata' => $variantSetMetadataSelected
        ];
        return $this->render('variant_set_metadata/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(VariantSetMetadata $variantSetMetadata, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('variant_set_metadata_edit', $variantSetMetadata);
        $form = $this->createForm(VariantSetMetadataUpdateType::class, $variantSetMetadata);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($variantSetMetadata);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('variant_set_metadata_index'));
        }

        $context = [
            'title' => 'Variant Set Metadata Update',
            'variantSetMetadataForm' => $form->createView()
        ];
        return $this->render('variant_set_metadata/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(VariantSetMetadata $variantSetMetadata, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($variantSetMetadata->getId()) {
            $variantSetMetadata->setIsActive(!$variantSetMetadata->getIsActive());
        }
        $entmanager->persist($variantSetMetadata);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $variantSetMetadata->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}