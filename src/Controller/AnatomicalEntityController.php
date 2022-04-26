<?php

namespace App\Controller;

use App\Entity\AnatomicalEntity;
use App\Form\AnatomicalEntityType;
use App\Form\AnatomicalEntityUpdateType;
use App\Repository\AnatomicalEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("anatomical/entity", name="anatomical_entity_")
 */
class AnatomicalEntityController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AnatomicalEntityRepository $anatomicalEntityRepo): Response
    {
        $anatomicalEntities =  $anatomicalEntityRepo->findAll();
        $context = [
            'title' => 'Anatomical Entity List',
            'anatomicalEntities' => $anatomicalEntities
        ];
        return $this->render('anatomical_entity/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $anatomicalEntity = new AnatomicalEntity();
        $form = $this->createForm(AnatomicalEntityType::class, $anatomicalEntity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $anatomicalEntity->setCreatedBy($this->getUser());
            }
            $anatomicalEntity->setIsActive(true);
            $anatomicalEntity->setCreatedAt(new \DateTime());
            $entmanager->persist($anatomicalEntity);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('anatomical_entity_index'));
        }

        $context = [
            'title' => 'Anatomical Entity Creation',
            'anatomicalEntityForm' => $form->createView()
        ];
        return $this->render('anatomical_entity/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AnatomicalEntity $anatomicalEntitySelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Anatomical Entity Details',
            'anatomicalEntity' => $anatomicalEntitySelected
        ];
        return $this->render('anatomical_entity/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AnatomicalEntity $anatomicalEntity, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(AnatomicalEntityUpdateType::class, $anatomicalEntity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($anatomicalEntity);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('anatomical_entity_index'));
        }

        $context = [
            'title' => 'Anatomical Entity Update',
            'anatomicalEntityForm' => $form->createView()
        ];
        return $this->render('anatomical_entity/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AnatomicalEntity $anatomicalEntity, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($anatomicalEntity->getId()) {
            $anatomicalEntity->setIsActive(!$anatomicalEntity->getIsActive());
        }
        $entmanager->persist($anatomicalEntity);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $anatomicalEntity->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}
