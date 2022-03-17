<?php

namespace App\Controller;

use App\Entity\SequencingInstrument;
use App\Form\SequencingInstrumentType;
use App\Form\SequencingInstrumentUpdateType;
use App\Repository\SequencingInstrumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/sequencing/instrument", name="sequencing_instrument_")
 */
class SequencingInstrumentController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SequencingInstrumentRepository $sequencingInstrumentRepo): Response
    {
        $sequencingInstruments =  $sequencingInstrumentRepo->findAll();
        $context = [
            'title' => 'Sequencing Instrument List',
            'sequencingInstruments' => $sequencingInstruments
        ];
        return $this->render('sequencing_instrument/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sequencingInstrument = new SequencingInstrument();
        $form = $this->createForm(SequencingInstrumentType::class, $sequencingInstrument);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sequencingInstrument->setIsActive(true);
            $sequencingInstrument->setCreatedAt(new \DateTime());
            $entmanager->persist($sequencingInstrument);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('sequencing_instrument_index'));
        }

        $context = [
            'title' => 'Sequencing Instrument Creation',
            'sequencingInstrumentForm' => $form->createView()
        ];
        return $this->render('sequencing_instrument/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(SequencingInstrument $sequencingInstrumentSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Sequencing Instrument Details',
            'sequencingInstrument' => $sequencingInstrumentSelected
        ];
        return $this->render('sequencing_instrument/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(SequencingInstrument $sequencingInstrument, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(SequencingInstrumentUpdateType::class, $sequencingInstrument);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($sequencingInstrument);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('sequencing_instrument_index'));
        }

        $context = [
            'title' => 'Sequencing Instrument Update',
            'sequencingInstrumentForm' => $form->createView()
        ];
        return $this->render('sequencing_instrument/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(SequencingInstrument $sequencingInstrument, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($sequencingInstrument->getId()) {
            $sequencingInstrument->setIsActive(!$sequencingInstrument->getIsActive());
        }
        $entmanager->persist($sequencingInstrument);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $sequencingInstrument->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
