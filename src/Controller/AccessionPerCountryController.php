<?php

namespace App\Controller;

use App\Entity\Country;
use App\Repository\AccessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccessionPerCountryController extends AbstractController
{
    /**
     * @Route("/accession/per/country/{id}", name="accession_per_country")
     */
    public function index(AccessionRepository $accessionRepo, Country $country): Response
    {
        $accessions = $accessionRepo->findBy(['origcty' => $country]);
        $name = $country->getName();
        $context = [
            'title' => 'Accession List',
            'listTitle' => 'Accession List For ' . $name .'',
            'accessions' => $accessions
        ];
        return $this->render('accession_per_country/index.html.twig', $context);
    }
}
