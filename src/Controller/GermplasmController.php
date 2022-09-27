<?php

namespace App\Controller;

use App\Entity\Accession;
use App\Entity\Germplasm;
use App\Entity\Institute;
use App\Form\GermplasmType;
use App\Form\GermplasmUpdateType;
use App\Repository\GermplasmRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/germplasm", name="germplasm_")
 */
class GermplasmController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GermplasmRepository $germplasmRepo): Response
    {
        $germplasms =  $germplasmRepo->findAll();
        $context = [
            'title' => 'Germplasm List',
            'germplasms' => $germplasms
        ];
        return $this->render('germplasm/index.html.twig', $context);
    }


    public function findAccessionMaintainerNumb($numb = 1){
        $accessionRepo = $this->getDoctrine()->getRepository(Accession::class);
        $acc = $accessionRepo->findBy(['instcode' => $numb]);
        return $acc;
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $germplasm = new Germplasm();
        $form = $this->createForm(GermplasmType::class, $germplasm);
        //dd($this->findAccessionMaintainerNumb(1));

        //$accessionRepo = $this->getDoctrine()->getRepository(Accession::class);
        //$acc = $accessionRepo->findBy(['instcode' => 1]);
        //dd($acc);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                    $germplasm->setCreatedBy($this->getUser());
                }
            $germplasm->setInstcode($form->get('maintainerInstituteCode')->getData());    
            $germplasm->setMaintainerNumb($form->get('maintainerNumb')->getData()->getMaintainerNumb());
            $germplasm->setIsActive(true);
            $germplasm->setCreatedAt(new \DateTime());
            $entmanager->persist($germplasm);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('germplasm_index'));
        }

        $context = [
            'title' => 'Germplasm Creation',
            'acc' => $this->findAccessionMaintainerNumb(1),
            'germplasmForm' => $form->createView()
        ];
        return $this->render('germplasm/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Germplasm $germplasmselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Germplasm Details',
            'germplasm' => $germplasmselected
        ];
        return $this->render('germplasm/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Germplasm $germplasm, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('germplasm_edit', $germplasm);
        $form = $this->createForm(GermplasmUpdateType::class, $germplasm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($germplasm);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('germplasm_index'));
        }

        $context = [
            'title' => 'Germplasm Update',
            'germplasmForm' => $form->createView()
        ];
        return $this->render('germplasm/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(Germplasm $germplasm, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($germplasm->getId()) {
            $germplasm->setIsActive(!$germplasm->getIsActive());
        }
        $entmanager->persist($germplasm);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $germplasm->getIsActive()
        ], 200);
    }
}
