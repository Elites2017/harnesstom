<?php

namespace App\Controller;

use App\Entity\VarCallSoftware;
use App\Form\VarCallSoftwareType;
use App\Form\VarCallSoftwareUpdateType;
use App\Repository\VarCallSoftwareRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/var/call/software", name="var_call_software_")
 */
class VarCallSoftwareController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(VarCallSoftwareRepository $varCallSoftwareRepo): Response
    {
        $varCallSoftwares =  $varCallSoftwareRepo->findAll();
        $context = [
            'title' => 'Var Call Software',
            'varCallSoftwares' => $varCallSoftwares
        ];
        return $this->render('var_call_software/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $varCallSoftware = new VarCallSoftware();
        $form = $this->createForm(VarCallSoftwareType::class, $varCallSoftware);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $varCallSoftware->setCreatedBy($this->getUser());
            }
            $varCallSoftware->setIsActive(true);
            $varCallSoftware->setCreatedAt(new \DateTime());
            $entmanager->persist($varCallSoftware);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('var_call_software_index'));
        }

        $context = [
            'title' => 'Var Call Software Creation',
            'varCallSoftwareForm' => $form->createView()
        ];
        return $this->render('var_call_software/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(VarCallSoftware $varCallSoftwareSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Var Call Software Details',
            'varCallSoftware' => $varCallSoftwareSelected
        ];
        return $this->render('var_call_software/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(VarCallSoftware $varCallSoftware, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('var_call_software_edit', $varCallSoftware);
        $form = $this->createForm(VarCallSoftwareUpdateType::class, $varCallSoftware);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($varCallSoftware);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('var_call_software_index'));
        }

        $context = [
            'title' => 'Var Call Software Update',
            'varCallSoftwareForm' => $form->createView()
        ];
        return $this->render('var_call_software/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(VarCallSoftware $varCallSoftware, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($varCallSoftware->getId()) {
            $varCallSoftware->setIsActive(!$varCallSoftware->getIsActive());
        }
        $entmanager->persist($varCallSoftware);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $varCallSoftware->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}