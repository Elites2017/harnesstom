<?php

namespace App\Controller;

use App\Entity\QTLStatistic;
use App\Form\QTLStatisticType;
use App\Form\QTLStatisticUpdateType;
use App\Repository\QTLStatisticRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/qtl/statistic", name="qtl_statistic_")
 */
class QTLStatisticController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(QTLStatisticRepository $qtlStatisticRepo): Response
    {
        $qtlStatistics =  $qtlStatisticRepo->findAll();
        $context = [
            'title' => 'QTL Statistic',
            'qtlStatistics' => $qtlStatistics
        ];
        return $this->render('qtl_statistic/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $qtlStatistic = new QTLStatistic();
        $form = $this->createForm(QTLStatisticType::class, $qtlStatistic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $qtlStatistic->setCreatedBy($this->getUser());
            }
            $qtlStatistic->setIsActive(true);
            $qtlStatistic->setCreatedAt(new \DateTime());
            $entmanager->persist($qtlStatistic);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_statistic_index'));
        }

        $context = [
            'title' => 'QTL Statistic Creation',
            'qtlStatisticForm' => $form->createView()
        ];
        return $this->render('qtl_statistic/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(QTLStatistic $qtlStatisticSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'QTL Statistic Details',
            'qtlStatistic' => $qtlStatisticSelected
        ];
        return $this->render('qtl_statistic/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(QTLStatistic $qtlStatistic, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('qtl_statistic_edit', $qtlStatistic);
        $form = $this->createForm(QTLStatisticUpdateType::class, $qtlStatistic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($qtlStatistic);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_statistic_index'));
        }

        $context = [
            'title' => 'QTL Statistic Update',
            'qtlStatisticForm' => $form->createView()
        ];
        return $this->render('qtl_statistic/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(QTLStatistic $qtlStatistic, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($qtlStatistic->getId()) {
            $qtlStatistic->setIsActive(!$qtlStatistic->getIsActive());
        }
        $entmanager->persist($qtlStatistic);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $qtlStatistic->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}

