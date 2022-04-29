<?php

namespace App\Controller;

use App\Entity\GWASStatTest;
use App\Form\GWASStatTestType;
use App\Form\GWASStatTestUpdateType;
use App\Repository\GWASStatTestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 // set a class level route
 /**
 * @Route("gwas/stat/test", name="gwas_stat_test_")
 */
class GWASStatTestController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GWASStatTestRepository $gwasStatTestRepo): Response
    {
        $gwasStatTests =  $gwasStatTestRepo->findAll();
        $context = [
            'title' => 'GWAS Stat Test List',
            'gwasStatTests' => $gwasStatTests
        ];
        return $this->render('gwas_stat_test/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $gwasStatTest = new GWASStatTest();
        $form = $this->createForm(GWASStatTestType::class, $gwasStatTest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $gwasStatTest->setCreatedBy($this->getUser());
            }
            $gwasStatTest->setIsActive(true);
            $gwasStatTest->setCreatedAt(new \DateTime());
            $entmanager->persist($gwasStatTest);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_stat_test_index'));
        }

        $context = [
            'title' => 'GWAS Stat Test Creation',
            'gwasStatTestForm' => $form->createView()
        ];
        return $this->render('gwas_stat_test/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GWASStatTest $gwasStatTestSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'GWAS Stat Test Details',
            'gwasStatTest' => $gwasStatTestSelected
        ];
        return $this->render('gwas_stat_test/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GWASStatTest $gwasStatTest, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('gwas_stat_test_edit', $gwasStatTest);
        $form = $this->createForm(GWASStatTestUpdateType::class, $gwasStatTest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($gwasStatTest);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_stat_test_index'));
        }

        $context = [
            'title' => 'GWAS Stat Test Update',
            'gwasStatTestForm' => $form->createView()
        ];
        return $this->render('gwas_stat_test/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GWASStatTest $gwasStatTest, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($gwasStatTest->getId()) {
            $gwasStatTest->setIsActive(!$gwasStatTest->getIsActive());
        }
        $entmanager->persist($gwasStatTest);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $gwasStatTest->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('growthFacility_home'));
    }
}