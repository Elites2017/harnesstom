<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Form\ContactUpdateType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/contact", name="contact_")
 */
class ContactController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ContactRepository $contactRepo): Response
    {
        $contacts =  $contactRepo->findAll();
        $context = [
            'title' => 'Contact List',
            'contacts' => $contacts
        ];
        return $this->render('contact/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $contact->setCreatedBy($this->getUser());
            }
            $contact->setIsActive(true);
            $contact->setCreatedAt(new \DateTime());
            $entmanager->persist($contact);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('contact_index'));
        }

        $context = [
            'title' => 'Contact Creation',
            'contactForm' => $form->createView()
        ];
        return $this->render('contact/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Contact $contactSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Contact Details',
            'contact' => $contactSelected
        ];
        return $this->render('contact/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Contact $contact, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('contact_edit', $contact);
        $form = $this->createForm(ContactUpdateType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($contact);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('contact_index'));
        }

        $context = [
            'title' => 'Contact Update',
            'contactForm' => $form->createView()
        ];
        return $this->render('contact/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Contact $contact, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($contact->getId()) {
            $contact->setIsActive(!$contact->getIsActive());
        }
        $entmanager->persist($contact);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $contact->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
