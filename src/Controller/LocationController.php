<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Location;
use App\Form\LocationType;
use App\Form\LocationUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/location", name="location_")
 */
class LocationController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(LocationRepository $locationRepo): Response
    {
        $locations =  $locationRepo->findAll();
        $context = [
            'title' => 'Location List',
            'locations' => $locations
        ];
        return $this->render('location/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $location->setCreatedBy($this->getUser());
            }
            $location->setIsActive(true);
            $location->setCreatedAt(new \DateTime());
            $entmanager->persist($location);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('location_index'));
        }

        $context = [
            'title' => 'Location Creation',
            'locationForm' => $form->createView()
        ];
        return $this->render('location/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Location $locationSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Location Details',
            'location' => $locationSelected
        ];
        return $this->render('location/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Location $location, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('location_edit', $location);
        $form = $this->createForm(LocationUpdateType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($location);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('location_index'));
        }

        $context = [
            'title' => 'Location Update',
            'locationForm' => $form->createView()
        ];
        return $this->render('location/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Location $location, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($location->getId()) {
            $location->setIsActive(!$location->getIsActive());
        }
        $entmanager->persist($location);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $location->getIsActive()
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
        $form = $this->createForm(UploadFromExcelType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Setup repository of some entity
            $repoLocation = $entmanager->getRepository(Location::class);
            // Query how many rows are there in the Location table
            $totalLocationBefore = $repoLocation->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            // Return a number as response
            // e.g 972

            // get the file (name from the CountryUploadFromExcelType form)
            $file = $request->files->get('upload_from_excel')['file'];
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
                $abbreviation = $row['A'];
                $name = $row['B'];
                $iso3Country = $row['C'];
                $longitude = $row['D'];
                $latitude = $row['E'];
                $altitude = $row['F'];
                $siteStatus = $row['G'];
                // check if the file doesn't have empty columns
                if ($abbreviation != null & $name != null) {
                    // check if the data is upload in the database
                    $existingLocation = $entmanager->getRepository(Location::class)->findOneBy(['abbreviation' => $abbreviation]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingLocation) {
                        $location = new Location();
                        $locationCountry = $entmanager->getRepository(Country::class)->findOneBy(['iso3' => $iso3Country]);
                        if (($locationCountry != null) && ($locationCountry instanceof \App\Entity\Country)) {
                            $location->setCountry($locationCountry);
                        }
                        if ($this->getUser()) {
                            $location->setCreatedBy($this->getUser());
                        }
                        $location->setName($name);
                        $location->setAbbreviation($abbreviation);
                        $location->setLatitudeCo($latitude);
                        $location->setLongitudeCo($longitude);
                        $location->setAltitudeCo($altitude);
                        $location->setSiteStatus($siteStatus);
                        $location->setIsActive(true);
                        $location->setCreatedAt(new \DateTime());
                        $entmanager->persist($location);
                        $entmanager->flush();
                    }
                }
            }
            // Query how many rows are there in the Country table
            $totalLocationAfter = $repoLocation->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalLocationBefore == 0) {
                $this->addFlash('success', $totalLocationAfter . " locations have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalLocationAfter - $totalLocationBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new location has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " location has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " locations have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('location_index'));
        }

        $context = [
            'title' => 'Location Upload From Excel',
            'locationUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('location/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/location_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'location_template_example.xlsx');
        return $response;
       
    }
}
