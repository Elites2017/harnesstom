<?php

namespace App\Controller;

use App\Entity\StorageType;
use App\Form\StorageCreateType;
use App\Form\StorageUpdateType;
use App\Repository\StorageTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("storage/type", name="storage_type_")
 */
class StorageTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(StorageTypeRepository $storageTypeRepo): Response
    {
        $storageTypes =  $storageTypeRepo->findAll();
        $context = [
            'title' => 'Storage Type List',
            'storageTypes' => $storageTypes
        ];
        return $this->render('storage_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $storageType = new StorageType();
        $form = $this->createForm(StorageCreateType::class, $storageType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $storageType->setCreatedBy($this->getUser());
            }
            $storageType->setIsActive(true);
            $storageType->setCreatedAt(new \DateTime());
            $entmanager->persist($storageType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('storage_type_index'));
        }

        $context = [
            'title' => 'Storage Type Creation',
            'storageTypeForm' => $form->createView()
        ];
        return $this->render('storage_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(StorageType $storageTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Storage Type Details',
            'storageType' => $storageTypeSelected
        ];
        return $this->render('storage_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(StorageType $storageType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('storage_type_edit', $storageType);
        $form = $this->createForm(StorageUpdateType::class, $storageType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($storageType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('storage_type_index'));
        }

        $context = [
            'title' => 'Storage Type Update',
            'storageTypeForm' => $form->createView()
        ];
        return $this->render('storage_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(StorageType $storageType, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($storageType->getId()) {
            $storageType->setIsActive(!$storageType->getIsActive());
        }
        $entmanager->persist($storageType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $storageType->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}
