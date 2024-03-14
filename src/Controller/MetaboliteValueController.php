<?php

namespace App\Controller;

use App\Entity\MetaboliteValue;
use App\Form\MetaboliteValueType;
use App\Form\MetaboliteValueUpdateType;
use App\Repository\MetaboliteValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/metabolite/value", name="metabolite_value_")
 */
class MetaboliteValueController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MetaboliteValueRepository $metaboliteValueRepo): Response
    {
        $metaboliteValues =  $metaboliteValueRepo->findAll();
        $context = [
            'title' => 'Metabolite Value List',
            'metaboliteValues' => $metaboliteValues
        ];
        return $this->render('metabolite_value/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $metaboliteValue = new MetaboliteValue();
        $form = $this->createForm(MetaboliteValueType::class, $metaboliteValue);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $metaboliteValue->setCreatedBy($this->getUser());
            }
            $metaboliteValue->setIsActive(true);
            $metaboliteValue->setCreatedAt(new \DateTime());
            $entmanager->persist($metaboliteValue);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('metabolite_value_index'));
        }

        $context = [
            'title' => 'Metabolite Value Creation',
            'metaboliteValueForm' => $form->createView()
        ];
        return $this->render('metabolite_value/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MetaboliteValue $metaboliteValueSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Metabolite Value Details',
            'metaboliteValue' => $metaboliteValueSelected
        ];
        return $this->render('metabolite_value/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MetaboliteValue $metaboliteValue, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('metabolite_value_edit', $metaboliteValue);
        $form = $this->createForm(MetaboliteValueUpdateType::class, $metaboliteValue);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($metaboliteValue);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolite_value_index'));
        }

        $context = [
            'title' => 'Metabolite Value Update',
            'metaboliteValueForm' => $form->createView()
        ];
        return $this->render('metabolite_value/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MetaboliteValue $metaboliteValue, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($metaboliteValue->getId()) {
            $metaboliteValue->setIsActive(!$metaboliteValue->getIsActive());
        }
        $entmanager->persist($metaboliteValue);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $metaboliteValue->getIsActive()
        ], 200);
    }
}
