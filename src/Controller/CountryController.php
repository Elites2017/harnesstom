<?php

namespace App\Controller;

use App\Form\CountryType;
use App\Form\CountryUpdateType;
use App\Entity\Country;
use App\Form\CountryUploadFromExcelType;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Constraints\Length;

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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $country->setCreatedBy($this->getUser());
            }
            $country->setIsActive(true);
            $country->setCreatedAt(new \DateTime());
            $entmanager->persist($country);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
        $this->denyAccessUnlessGranted('country_edit', $country);
        $form = $this->createForm(CountryUpdateType::class, $country);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($country);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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

    // this is to upload data in bulk using an excel file
    /**
     * @Route("/upload-from-excel", name="upload_from_excel")
     */
    public function uploadFromExcel(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(CountryUploadFromExcelType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Setup repository of some entity
            $repoCountry = $entmanager->getRepository(Country::class);
            // Query how many rows are there in the Country table
            $totalCountryBefore = $repoCountry->createQueryBuilder('c')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(c.id)')
                ->getQuery()
                ->getSingleScalarResult();

            // Return a number as response
            // e.g 972

            // get the file (name from the CountryUploadFromExcelType form)
            $file = $request->files->get('country_upload_from_excel')['file'];
            // set the folder to send the file to
            $fileFolder = __DIR__ . '/../../public/uploads/excel/';
            // apply md5 function to generate a unique id for the file and concat it with the original file name
            if ($file->getClientOriginalName()) {
                $filePathName = md5(uniqid()) . $file->getClientOriginalName();
                try {
                    $file->move($fileFolder, $filePathName);
                } catch (\Throwable $th) {
                    //throw $th;
                    $this->addFlash('danger', "Fail to upload the file, try again");
                }
            } else {
                $this->addFlash('danger', "Error in the file name, try to rename the file and try again");
            }
            // read from the uploaded file
            $spreadsheet = IOFactory::load($fileFolder . $filePathName);
            // remove the first row (title) of the file
            $spreadsheet->getActiveSheet()->removeRow(1);
            // transform the uploaded file to an array
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            // loop over the array to get each row
            foreach ($sheetData as $key => $row) {
                $name = $row['A'];
                $iso3 = $row['B'];
                // check if the file doesn't have empty columns
                if ($name != null && $iso3 != null) {
                    // check if the data is upload in the database
                    $existingCountry = $entmanager->getRepository(Country::class)->findOneBy(['iso3' => $iso3]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingCountry) {
                        $country = new Country();
                        if ($this->getUser()) {
                            $country->setCreatedBy($this->getUser());
                        }
                        $country->setName($name);
                        $country->setIso3($iso3);
                        $country->setIsActive(true);
                        
                        try {
                            //code...
                            $entmanager->persist($country);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the Country table
            $totalCountryAfter = $repoCountry->createQueryBuilder('c')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(c.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalCountryBefore == 0) {
                $this->addFlash('success', $totalCountryAfter . " countries have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalCountryAfter - $totalCountryBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new country have been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " country has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " countries have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('country_index'));
        }

        $context = [
            'title' => 'Country Upload From Excel',
            'countryUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('country/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function countryTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/country_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'country_template_example.xls');
        return $response;
       
    }
}
