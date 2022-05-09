<?php

namespace App\Controller;

use App\Entity\Sample;
use App\Form\SampleType;
use App\Form\SampleUpdateType;
use App\Repository\SampleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/sample", name="sample_")
 */
class SampleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SampleRepository $sampleRepo): Response
    {
        $samples =  $sampleRepo->findAll();
        $context = [
            'title' => 'Sample List',
            'samples' => $samples
        ];
        return $this->render('sample/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sample = new Sample();
        $form = $this->createForm(SampleType::class, $sample);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $sample->setCreatedBy($this->getUser());
            }
            $sample->setIsActive(true);
            $sample->setCreatedAt(new \DateTime());
            $entmanager->persist($sample);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('sample_index'));
        }

        $context = [
            'title' => 'Sample Creation',
            'sampleForm' => $form->createView()
        ];
        return $this->render('sample/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(sample $sampleSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Sample Details',
            'sample' => $sampleSelected
        ];
        return $this->render('sample/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(sample $sample, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('sample_edit', $sample);
        $form = $this->createForm(SampleUpdateType::class, $sample);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sample->setLastUpdated(new \DateTime());
            $entmanager->persist($sample);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('sample_index'));
        }


        $context = [
            'title' => 'Sample Update',
            'sampleForm' => $form->createView()
        ];
        return $this->render('sample/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(sample $sample, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($sample->getId()) {
            $sample->setIsActive(!$sample->getIsActive());
        }
        $entmanager->persist($sample);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $sample->getIsActive()
        ], 200);
    }
}
