<?php

namespace App\Controller;

use App\Entity\CiCriteria;
use App\Form\CiCriteriaType;
use App\Form\CiCriteriaUpdateType;
use App\Repository\CiCriteriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("ci/criteria", name="ci_criteria_")
 */
class CiCriteriaController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CiCriteriaRepository $ciCriteriaRepo): Response
    {
        $ciCriterias =  $ciCriteriaRepo->findAll();
        $context = [
            'title' => 'Ci Criteria List',
            'ciCriterias' => $ciCriterias
        ];
        return $this->render('ci_criteria/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $ciCriteria = new CiCriteria();
        $form = $this->createForm(CiCriteriaType::class, $ciCriteria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $ciCriteria->setCreatedBy($this->getUser());
            }
            $ciCriteria->setIsActive(true);
            $ciCriteria->setCreatedAt(new \DateTime());
            $entmanager->persist($ciCriteria);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('ci_criteria_index'));
        }

        $context = [
            'title' => 'Ci Criteria Creation',
            'ciCriteriaForm' => $form->createView()
        ];
        return $this->render('ci_criteria/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(CiCriteria $ciCriteriaSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Ci Criteria Details',
            'ciCriteria' => $ciCriteriaSelected
        ];
        return $this->render('ci_criteria/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(CiCriteria $ciCriteria, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(CiCriteriaUpdateType::class, $ciCriteria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($ciCriteria);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('ci_criteria_index'));
        }

        $context = [
            'title' => 'Ci Criteria Update',
            'ciCriteriaForm' => $form->createView()
        ];
        return $this->render('ci_criteria/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(CiCriteria $ciCriteria, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($ciCriteria->getId()) {
            $ciCriteria->setIsActive(!$ciCriteria->getIsActive());
        }
        $entmanager->persist($ciCriteria);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $ciCriteria->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('ciCriteria_home'));
    }
}