<?php

/* 
    This is the factorTypeController which contains the CRUD method of this object.
    1. The index function is to list all the object from the DB
    
    2. The create function is to create the object by
        2.1 initializes the object
        2.2 create the form from the factorTypeType form and do the binding
        2.2.1 pass the request to the form to handle it
        2.2.2 Analyze the form, if everything is okay, save the object and redirect the user
        if there is any problem, the same page will be display to the user with the context
    
    3. The details function is just to show the details of the selected object to the user.

    4. the update funtion is a little bit similar with the create one, because they almost to the same thing, but
    in the update, we don't initialize the object as it will come from the injection and it is supposed to be existed.

    5. the delete function is to delete the object from the DB, but to keep a trace, it is preferable
    to just change the state of the object.

    March 11, 2022
    David PIERRE
*/

namespace App\Controller;

use App\Entity\FactorType;
use App\Form\FactorCreateType;
use App\Form\FactorUpdateType;
use App\Repository\FactorTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


 // set a class level route
 /**
 * @Route("factor/type", name="factor_type_")
 */
class FactorTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(FactorTypeRepository $factorTypeRepo): Response
    {
        $factorTypes =  $factorTypeRepo->findAll();
        $context = [
            'title' => 'FactorType List',
            'factorTypes' => $factorTypes
        ];
        return $this->render('factor_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $factorType = new FactorType();
        $form = $this->createForm(FactorCreateType::class, $factorType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $factorType->setCreatedBy($this->getUser());
            }
            $factorType->setIsActive(true);
            $factorType->setCreatedAt(new \DateTime());
            $entmanager->persist($factorType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('factor_type_index'));
        }

        $context = [
            'title' => 'Factor Creation',
            'factorTypeForm' => $form->createView()
        ];
        return $this->render('factor_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(FactorType $factorTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'FactorType Details',
            'factorType' => $factorTypeSelected
        ];
        return $this->render('factor_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(FactorType $factorType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('factor_type_edit', $factorType);
        $form = $this->createForm(FactorUpdateType::class, $factorType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($factorType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('factor_type_index'));
        }

        $context = [
            'title' => 'FactorType Update',
            'factorTypeForm' => $form->createView()
        ];
        return $this->render('factor_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(FactorType $factorType, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($factorType->getId()) {
            $factorType->setIsActive(!$factorType->getIsActive());
        }
        $entmanager->persist($factorType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $factorType->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
    }
}
