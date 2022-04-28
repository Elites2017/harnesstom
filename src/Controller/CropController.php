<?php

namespace App\Controller;

use App\Entity\Crop;
use App\Repository\CropRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CropType;
use App\Form\CropUpdateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/crop", name="crop_")
 */
class CropController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CropRepository $cropRepo): Response
    {
        $crops =  $cropRepo->findAll();
        $context = [
            'title' => 'Crops',
            'crops' => $crops
        ];
        return $this->render('crop/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $crop = new Crop();
        $form = $this->createForm(cropType::class, $crop);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $crop->setCreatedBy($this->getUser());
            }
            $crop->setIsActive(true);
            $crop->setCreatedAt(new \DateTime());
            $entmanager->persist($crop);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('crop_index'));
        }

        $context = [
            'title' => 'Crop Creation',
            'cropForm' => $form->createView()
        ];
        return $this->render('crop/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Crop $cropSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Crop Details',
            'crop' => $cropSelected
        ];
        return $this->render('crop/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Crop $crop, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(CropUpdateType::class, $crop);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($crop);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('crop_index'));
        }

        $context = [
            'title' => 'Crop Update',
            'cropForm' => $form->createView()
        ];
        return $this->render('crop/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Crop $crop, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($crop->getId()) {
            $crop->setIsActive(!$crop->getIsActive());
        }
        $entmanager->persist($crop);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $crop->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
