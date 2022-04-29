<?php

namespace App\Controller;

use App\Entity\TrialType;
use App\Form\TrialCreateType;
use App\Form\TrialUpdateType;
use App\Repository\TrialTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("trial/type", name="trial_type_")
 */
class TrialTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TrialTypeRepository $trialTypeRepo): Response
    {
        $trialTypes =  $trialTypeRepo->findAll();
        $context = [
            'title' => 'Trial Type List',
            'trialTypes' => $trialTypes
        ];
        return $this->render('trial_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $trialType = new TrialType();
        $form = $this->createForm(TrialCreateType::class, $trialType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $trialType->setCreatedBy($this->getUser());
            }
            $trialType->setIsActive(true);
            $trialType->setCreatedAt(new \DateTime());
            $entmanager->persist($trialType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trial_type_index'));
        }

        $context = [
            'title' => 'Trial Type Creation',
            'trialTypeForm' => $form->createView()
        ];
        return $this->render('trial_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(TrialType $trialTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Trial Type Details',
            'trialType' => $trialTypeSelected
        ];
        return $this->render('trial_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(TrialType $trialType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('trial_type_edit', $trialType);
        $form = $this->createForm(TrialUpdateType::class, $trialType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($trialType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trial_type_index'));
        }

        $context = [
            'title' => 'Trial Type Update',
            'trialTypeForm' => $form->createView()
        ];
        return $this->render('trial_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(TrialType $trialType, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($trialType->getId()) {
            $trialType->setIsActive(!$trialType->getIsActive());
        }
        $entmanager->persist($trialType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $trialType->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}

