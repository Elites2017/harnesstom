<?php

namespace App\Controller;

use App\Entity\Software;
use App\Form\SoftwareType;
use App\Form\SoftwareUpdateType;
use App\Repository\SoftwareRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("software", name="software_")
 */
class SoftwareController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SoftwareRepository $softwareRepo): Response
    {
        $softwares =  $softwareRepo->findAll();
        $context = [
            'title' => 'Software List',
            'softwares' => $softwares
        ];
        return $this->render('software/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $software = new Software();
        $form = $this->createForm(SoftwareType::class, $software);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $software->setIsActive(true);
            $software->setCreatedAt(new \DateTime());
            $entmanager->persist($software);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('software_index'));
        }

        $context = [
            'title' => 'Software Creation',
            'softwareForm' => $form->createView()
        ];
        return $this->render('software/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Software $softwareSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Software Details',
            'software' => $softwareSelected
        ];
        return $this->render('software/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Software $software, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(SoftwareUpdateType::class, $software);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($software);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('software_index'));
        }

        $context = [
            'title' => 'Software Update',
            'softwareForm' => $form->createView()
        ];
        return $this->render('software/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Software $software, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($software->getId()) {
            $software->setIsActive(!$software->getIsActive());
        }
        $entmanager->persist($software);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $software->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('software_home'));
    }
}
