<?php

namespace App\Controller;

use App\Entity\GWASVariant;
use App\Form\GWASVariantType;
use App\Form\GWASVariantUpdateType;
use App\Repository\GWASVariantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/gwas/variant", name="gwas_variant_")
 */
class GWASVariantController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GWASVariantRepository $gwasVariantRepo): Response
    {
        $gwasVariants =  $gwasVariantRepo->findAll();
        $context = [
            'title' => 'GWAS Variant List',
            'gwasVariants' => $gwasVariants
        ];
        return $this->render('gwas_variant/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $gwasVariant = new GWASVariant();
        $form = $this->createForm(GWASVariantType::class, $gwasVariant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $gwasVariant->setCreatedBy($this->getUser());
            }
            $gwasVariant->setIsActive(true);
            $gwasVariant->setCreatedAt(new \DateTime());
            $entmanager->persist($gwasVariant);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_variant_index'));
        }

        $context = [
            'title' => 'GWAS Variant Creation',
            'gwasVariantForm' => $form->createView()
        ];
        return $this->render('gwas_variant/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GWASVariant $gwasVariantSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'GWAS Variant Details',
            'gwas' => $gwasVariantSelected
        ];
        return $this->render('gwas_variant/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GWASVariant $gwasVariant, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('gwas_edit', $gwasVariant);
        $form = $this->createForm(GWASVariantUpdateType::class, $gwasVariant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($gwasVariant);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_variant_index'));
        }

        $context = [
            'title' => 'GWAS Variant Update',
            'gwasVariantForm' => $form->createView()
        ];
        return $this->render('gwas_variant/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GWASVariant $gwasVariant, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($gwasVariant->getId()) {
            $gwasVariant->setIsActive(!$gwasVariant->getIsActive());
        }
        $entmanager->persist($gwasVariant);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $gwasVariant->getIsActive()
        ], 200);
    }
}

