<?php

namespace App\Controller;

use App\Entity\Accession;
use App\Form\AccessionType;
use App\Form\AccessionUpdateType;
use App\Repository\AccessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/accession", name="accession_")
 */
class AccessionController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AccessionRepository $accessionRepo): Response
    {
        $accessions =  $accessionRepo->findAll();
        $context = [
            'title' => 'Accession List',
            'accessions' => $accessions
        ];
        return $this->render('accession/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $accession = new Accession();
        $form = $this->createForm(AccessionType::class, $accession);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $accession->setCreatedBy($this->getUser());
            }
            $accession->setIsActive(true);
            $accession->setCreatedAt(new \DateTime());
            $entmanager->persist($accession);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('accession_index'));
        }

        $context = [
            'title' => 'Accession Creation',
            'accessionForm' => $form->createView()
        ];
        return $this->render('accession/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Accession $accessionSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Accession Details',
            'accession' => $accessionSelected
        ];
        return $this->render('accession/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Accession $accession, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(AccessionUpdateType::class, $accession);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($accession);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('accession_index'));
        }

        $context = [
            'title' => 'Accession Update',
            'accessionForm' => $form->createView()
        ];
        return $this->render('accession/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Accession $accession, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($accession->getId()) {
            $accession->setIsActive(!$accession->getIsActive());
        }
        $entmanager->persist($accession);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $accession->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
