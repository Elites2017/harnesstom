<?php

namespace App\Controller;

use App\Entity\VariantSet;
use App\Form\VariantSetType;
use App\Form\VariantSetUpdateType;
use App\Repository\VariantSetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/variant/set", name="variant_set_")
 */
class VariantSetController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(VariantSetRepository $variantSetRepo): Response
    {
        $variantSets = [];
        if($this->getUser()) {
            $userRoles = $this->getUser()->getRoles();
            $adm = "ROLE_ADMIN";
            $res = array_search($adm, $userRoles);
            if ($res !== false) {
                $variantSets = $variantSetRepo->findAll();
            } else {
                $variantSets = $variantSetRepo->findReleasedTrialStudySampleVariantSet($this->getUser());
            }
        } else {
            $variantSets = $variantSetRepo->findReleasedTrialStudySampleVariantSet();
        }
        $context = [
            'title' => 'Variant Set List',
            'variantSets' => $variantSets
        ];
        return $this->render('variant_set/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variantSet = new VariantSet();
        $form = $this->createForm(VariantSetType::class, $variantSet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $variantSet->setCreatedBy($this->getUser());
            }
            $variantSet->setIsActive(true);
            $variantSet->setCreatedAt(new \DateTime());
            $entmanager->persist($variantSet);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('variant_set_index'));
        }

        $context = [
            'title' => 'Variant Set Creation',
            'variantSetForm' => $form->createView()
        ];
        return $this->render('variant_set/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(VariantSet $variantSetSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Variant Set Details',
            'variantSet' => $variantSetSelected
        ];
        return $this->render('variant_set/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(VariantSet $variantSet, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('variant_set_edit', $variantSet);
        $form = $this->createForm(VariantSetUpdateType::class, $variantSet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($variantSet);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('variant_set_index'));
        }

        $context = [
            'title' => 'Variant Set Update',
            'variantSetForm' => $form->createView()
        ];
        return $this->render('variant_set/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(VariantSet $variantSet, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($variantSet->getId()) {
            $variantSet->setIsActive(!$variantSet->getIsActive());
        }
        $entmanager->persist($variantSet);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $variantSet->getIsActive()
        ], 200);
    }
}
