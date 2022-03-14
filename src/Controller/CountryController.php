<?php

namespace App\Controller;
use App\Form\CountryType;
use App\Form\CountryUpdateType;
use App\Entity\Country;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/country", name="country_")
 */
class CountryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CountryRepository $countryRepo): Response
    {
        $countries =  $countryRepo->findAll();
        $context = [
            'title' => 'countries',
            'countries' => $countries
        ];
        return $this->render('country/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $country->setIsActive(true);
            $country->setCreatedAt(new \DateTime());
            $entmanager->persist($country);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('country_index'));
        }

        $context = [
            'title' => 'Country Creation',
            'countryForm' => $form->createView()
        ];
        return $this->render('country/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Country $countrySelected): Response
    {
        $context = [
            'title' => 'Country Details',
            'country' => $countrySelected
        ];
        return $this->render('country/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Country $country, Request $request, EntityManagerInterface $entmanager): Response
    {
        $form = $this->createForm(CountryUpdateType::class, $country);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($country);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('country_index'));
        }

        $context = [
            'title' => 'Country Update',
            'countryForm' => $form->createView()
        ];
        return $this->render('country/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Country $country, Request $request, EntityManagerInterface $entmanager): Response
    {
        if ($country->getId()) {
            $country->setIsActive(!$country->getIsActive());
        }
        $entmanager->persist($country);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $country->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }
}
