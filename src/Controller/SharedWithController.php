<?php

namespace App\Controller;

use App\Entity\SharedWith;
use App\Form\SharedWithType;
use App\Form\SharedWithUpdateType;
use App\Repository\SharedWithRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/shared/with", name="shared_with_")
 */
class SharedWithController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SharedWithRepository $sharedWithRepo): Response
    {
        $sharedWiths =  $sharedWithRepo->findAll();
        $context = [
            'title' => 'Share With List',
            'sharedWiths' => $sharedWiths
        ];
        return $this->render('shared_with/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sharedWith = new SharedWith();
        $form = $this->createForm(SharedWithType::class, $sharedWith);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('trial')->getData() instanceof \App\Entity\Trial) {
                $this->addFlash('danger', "You must choose a trial from the list");
            } else if (!$form->get('user')->getData() instanceof \App\Entity\User) {
                $this->addFlash('danger', "You must choose a user from the list");
            }
             else {
                if ($this->getUser()) {
                    $sharedWith->setCreatedBy($this->getUser());
                }
                $sharedWith->setIsActive(true);
                $sharedWith->setCreatedAt(new \DateTime());
                $entmanager->persist($sharedWith);
                $entmanager->flush();
                return $this->redirect($this->generateUrl('shared_with_index'));
            }
        }

        $context = [
            'title' => 'Share With Creation',
            'sharedWithForm' => $form->createView()
        ];
        return $this->render('shared_with/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(SharedWith $sharedWithselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Share With Details',
            'sharedWith' => $sharedWithselected
        ];
        return $this->render('shared_with/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(SharedWith $sharedWith, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('shared_with_edit', $sharedWith);
        $form = $this->createForm(SharedWithUpdateType::class, $sharedWith);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('trial')->getData() instanceof \App\Entity\Trial) {
                $this->addFlash('danger', "You must choose a trial from the list");
            } else if (!$form->get('user')->getData() instanceof \App\Entity\User) {
                $this->addFlash('danger', "You must choose a user from the list");
            }
            else {
                $entmanager->persist($sharedWith);
                $entmanager->flush();
                return $this->redirect($this->generateUrl('shared_with_index'));
            }
        }

        $context = [
            'title' => 'Share With Update',
            'sharedWithForm' => $form->createView()
        ];
        return $this->render('shared_with/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(SharedWith $sharedWith, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($sharedWith->getId()) {
            $sharedWith->setIsActive(!$sharedWith->getIsActive());
        }
        $entmanager->persist($sharedWith);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $sharedWith->getIsActive()
        ], 200);
    }
}
