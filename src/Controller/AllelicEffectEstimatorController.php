<?php

namespace App\Controller;

use App\Entity\AllelicEffectEstimator;
use App\Form\AllelicEffectEstimatorType;
use App\Form\AllelicEffectEstimatorUpdateType;
use App\Repository\AllelicEffectEstimatorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// set a class level route
/**
 * @Route("/allelic/effect/estimator", name="allelic_effect_estimator_")
 */
class AllelicEffectEstimatorController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AllelicEffectEstimatorRepository $allelicEffectEstimatorRepo): Response
    {
        $allelicEffectEstimators =  $allelicEffectEstimatorRepo->findAll();
        $context = [
            'title' => 'Allelic Effect Estimator',
            'allelicEffectEstimators' => $allelicEffectEstimators
        ];
        return $this->render('allelic_effect_estimator/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $allelicEffectEstimator = new AllelicEffectEstimator();
        $form = $this->createForm(AllelicEffectEstimatorType::class, $allelicEffectEstimator);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $allelicEffectEstimator->setCreatedBy($this->getUser());
            }
            $allelicEffectEstimator->setIsActive(true);
            $allelicEffectEstimator->setCreatedAt(new \DateTime());
            $entmanager->persist($allelicEffectEstimator);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('allelic_effect_estimator_index'));
        }

        $context = [
            'title' => 'Allelic Effect Estimator Creation',
            'allelicEffectEstimatorForm' => $form->createView()
        ];
        return $this->render('allelic_effect_estimator/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AllelicEffectEstimator $allelicEffectEstimatorselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Allelic Effect Estimator Details',
            'allelicEffectEstimator' => $allelicEffectEstimatorselected
        ];
        return $this->render('allelic_effect_estimator/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AllelicEffectEstimator $allelicEffectEstimator, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(AllelicEffectEstimatorUpdateType::class, $allelicEffectEstimator);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($allelicEffectEstimator);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('allelic_effect_estimator_index'));
        }

        $context = [
            'title' => 'Allelic Effect Estimator Update',
            'allelicEffectEstimatorForm' => $form->createView()
        ];
        return $this->render('allelic_effect_estimator/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AllelicEffectEstimator $allelicEffectEstimator, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($allelicEffectEstimator->getId()) {
            $allelicEffectEstimator->setIsActive(!$allelicEffectEstimator->getIsActive());
        }
        $entmanager->persist($allelicEffectEstimator);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $allelicEffectEstimator->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
