<?php

namespace App\Controller;

use App\Entity\IdentificationLevel;
use App\Form\IdentificationLevelType;
use App\Form\IdentificationLevelUpdateType;
use App\Repository\IdentificationLevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("identification/level", name="identification_level_")
 */
class IdentificationLevelController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(IdentificationLevelRepository $identificationLevelRepo): Response
    {
        $identificationLevels =  $identificationLevelRepo->findAll();
        $context = [
            'title' => 'Identification Level List',
            'identificationLevels' => $identificationLevels
        ];
        return $this->render('identification_level/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $identificationLevel = new IdentificationLevel();
        $form = $this->createForm(IdentificationLevelType::class, $identificationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $identificationLevel->setCreatedBy($this->getUser());
            }
            $identificationLevel->setIsActive(true);
            $identificationLevel->setCreatedAt(new \DateTime());
            $entmanager->persist($identificationLevel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('identification_level_index'));
        }

        $context = [
            'title' => 'Identification Level Creation',
            'identificationLevelForm' => $form->createView()
        ];
        return $this->render('identification_level/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(IdentificationLevel $identificationLevelSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Identification Level Details',
            'identificationLevel' => $identificationLevelSelected
        ];
        return $this->render('identification_level/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(IdentificationLevel $identificationLevel, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('identification_level_edit', $identificationLevel);
        $form = $this->createForm(IdentificationLevelUpdateType::class, $identificationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($identificationLevel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('identification_level_index'));
        }

        $context = [
            'title' => 'Identification Level Update',
            'identificationLevelForm' => $form->createView()
        ];
        return $this->render('identification_level/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(IdentificationLevel $identificationLevel, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($identificationLevel->getId()) {
            $identificationLevel->setIsActive(!$identificationLevel->getIsActive());
        }
        $entmanager->persist($identificationLevel);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $identificationLevel->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}