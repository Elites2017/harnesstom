<?php

namespace App\Controller;

use App\Entity\Parameter;
use App\Form\ParameterType;
use App\Form\ParameterUpdateType;
use App\Repository\ParameterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/parameter", name="parameter_")
 */
class ParameterController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ParameterRepository $parameterRepo): Response
    {
        $parameters =  $parameterRepo->findAll();
        $context = [
            'title' => 'Parameter List',
            'parameters' => $parameters
        ];
        return $this->render('parameter/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $parameter = new Parameter();
        $form = $this->createForm(ParameterType::class, $parameter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $parameter->setCreatedBy($this->getUser());
            }
            $parameter->setIsActive(true);
            $parameter->setCreatedAt(new \DateTime());
            $entmanager->persist($parameter);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('parameter_index'));
        }

        $context = [
            'title' => 'Parameter Creation',
            'parameterForm' => $form->createView()
        ];
        return $this->render('parameter/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Parameter $parameterSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Parameter Details',
            'parameter' => $parameterSelected
        ];
        return $this->render('parameter/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Parameter $parameter, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('parameter_edit', $parameter);
        $form = $this->createForm(ParameterUpdateType::class, $parameter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($parameter);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('parameter_index'));
        }

        $context = [
            'title' => 'Parameter Update',
            'parameterForm' => $form->createView()
        ];
        return $this->render('parameter/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Parameter $parameter, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($parameter->getId()) {
            $parameter->setIsActive(!$parameter->getIsActive());
        }
        $entmanager->persist($parameter);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $parameter->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
