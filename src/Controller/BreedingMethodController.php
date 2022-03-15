<?php

namespace App\Controller;

use App\Entity\BreedingMethod;
use App\Form\BreedingMethodType;
use App\Form\BreedingMethodUpdateType;
use App\Repository\BreedingMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 // set a class level route
 /**
 * @Route("breeding/method", name="breeding_method_")
 */
class BreedingMethodController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(BreedingMethodRepository $breedingMethodRepo): Response
    {
        $breedingMethods =  $breedingMethodRepo->findAll();
        $context = [
            'title' => 'Breeding Method List',
            'breedingMethods' => $breedingMethods
        ];
        return $this->render('breeding_method/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $breedingMethod = new BreedingMethod();
        $form = $this->createForm(BreedingMethodType::class, $breedingMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $breedingMethod->setIsActive(true);
            $breedingMethod->setCreatedAt(new \DateTime());
            $entmanager->persist($breedingMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('breeding_method_index'));
        }

        $context = [
            'title' => 'Breeding Method Creation',
            'breedingMethodForm' => $form->createView()
        ];
        return $this->render('breeding_method/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(BreedingMethod $breedingMethodSelected): Response
    {
        $context = [
            'title' => 'Breeding Method Details',
            'breedingMethod' => $breedingMethodSelected
        ];
        return $this->render('breeding_method/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(BreedingMethod $breedingMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $form = $this->createForm(BreedingMethodUpdateType::class, $breedingMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($breedingMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('breeding_method_index'));
        }

        $context = [
            'title' => 'Breeding Method Update',
            'breedingMethodForm' => $form->createView()
        ];
        return $this->render('breeding_method/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(BreedingMethod $breedingMethod, EntityManagerInterface $entmanager): Response
    {
        if ($breedingMethod->getId()) {
            $breedingMethod->setIsActive(!$breedingMethod->getIsActive());
        }
        $entmanager->persist($breedingMethod);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $breedingMethod->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('breedingMethod_home'));
    }
}