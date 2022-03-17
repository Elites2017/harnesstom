<?php

namespace App\Controller;

use App\Entity\KinshipAlgorithm;
use App\Form\KinshipAlgorithmType;
use App\Form\KinshipAlgorithmUpdateType;
use App\Repository\KinshipAlgorithmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("kinship/algorithm", name="kinship_algorithm_")
 */
class KinshipAlgorithmController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(KinshipAlgorithmRepository $kinshipAlgorithmRepo): Response
    {
        $kinshipAlgorithms =  $kinshipAlgorithmRepo->findAll();
        $context = [
            'title' => 'Kinship Algorithm List',
            'kinshipAlgorithms' => $kinshipAlgorithms
        ];
        return $this->render('kinship_algorithm/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $kinshipAlgorithm = new KinshipAlgorithm();
        $form = $this->createForm(KinshipAlgorithmType::class, $kinshipAlgorithm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $kinshipAlgorithm->setIsActive(true);
            $kinshipAlgorithm->setCreatedAt(new \DateTime());
            $entmanager->persist($kinshipAlgorithm);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('kinship_algorithm_index'));
        }

        $context = [
            'title' => 'Kinship Algorithm Creation',
            'kinshipAlgorithmForm' => $form->createView()
        ];
        return $this->render('kinship_algorithm/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(KinshipAlgorithm $kinshipAlgorithmSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Kinship Algorithm Details',
            'kinshipAlgorithm' => $kinshipAlgorithmSelected
        ];
        return $this->render('kinship_algorithm/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(KinshipAlgorithm $kinshipAlgorithm, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(KinshipAlgorithmUpdateType::class, $kinshipAlgorithm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($kinshipAlgorithm);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('kinship_algorithm_index'));
        }

        $context = [
            'title' => 'Kinship Algorithm Update',
            'kinshipAlgorithmForm' => $form->createView()
        ];
        return $this->render('kinship_algorithm/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(kinshipAlgorithm $kinshipAlgorithm, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($kinshipAlgorithm->getId()) {
            $kinshipAlgorithm->setIsActive(!$kinshipAlgorithm->getIsActive());
        }
        $entmanager->persist($kinshipAlgorithm);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $kinshipAlgorithm->getIsActive()
        ], 200);
    }
}