<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\User;
use App\Form\PersonType;
use App\Form\PersonUpdateType;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/person", name="person_")
 */
class PersonController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PersonRepository $personRepo): Response
    {
        $persons =  $personRepo->findAll();
        $context = [
            'title' => 'Person',
            'persons' => $persons
        ];
        return $this->render('person/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $person = new person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $person->setCreatedBy($this->getUser());
            }
            $person->setIsActive(true);
            $person->setCreatedAt(new \DateTime());
            $entmanager->persist($person);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('person_index'));
        }

        $context = [
            'title' => 'Person Creation',
            'personForm' => $form->createView()
        ];
        return $this->render('person/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Person $personSelected): Response
    {
        $context = [
            'title' => 'Person Details',
            'person' => $personSelected
        ];
        return $this->render('person/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Person $person, Request $request, EntityManagerInterface $entmanager): Response
    {
        $form = $this->createForm(PersonUpdateType::class, $person);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($person);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('person_index'));
        }

        $context = [
            'title' => 'Person Update',
            'personForm' => $form->createView()
        ];
        return $this->render('person/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Person $person, Request $request, EntityManagerInterface $entmanager): Response
    {
        if ($person->getId()) {
            $person->setIsActive(!$person->getIsActive());
        }
        $entmanager->persist($person);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $person->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
