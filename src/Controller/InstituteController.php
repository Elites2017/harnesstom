<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Institute;
use App\Form\InstituteType;
use App\Form\InstituteUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\InstituteRepository;
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
 * @Route("/institute", name="institute_")
 */
class InstituteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(InstituteRepository $instituteRepo): Response
    {
        $institutes =  $instituteRepo->findAll();
        $context = [
            'title' => 'Institute List',
            'institutes' => $institutes
        ];
        return $this->render('institute/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $institute = new Institute();
        $form = $this->createForm(InstituteType::class, $institute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $institute->setCreatedBy($this->getUser());
            }
            $institute->setIsActive(true);
            $institute->setCreatedAt(new \DateTime());
            $entmanager->persist($institute);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('institute_index'));
        }

        $context = [
            'title' => 'Institute Creation',
            'instituteForm' => $form->createView()
        ];
        return $this->render('institute/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Institute $instituteSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Institute Details',
            'institute' => $instituteSelected
        ];
        return $this->render('institute/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Institute $institute, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('institute_edit', $institute);
        $form = $this->createForm(InstituteUpdateType::class, $institute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($institute);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('institute_index'));
        }

        $context = [
            'title' => 'Institute Update',
            'instituteForm' => $form->createView()
        ];
        return $this->render('institute/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Institute $institute, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($institute->getId()) {
            $institute->setIsActive(!$institute->getIsActive());
        }
        $entmanager->persist($institute);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $institute->getIsActive()
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
            $repoInstitute = $entmanager->getRepository(Institute::class);
            // Query how many rows are there in the Institute table
            $totalInstituteBefore = $repoInstitute->createQueryBuilder('tab')
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
                $instcode = $row['A'];
                $acronym = $row['B'];
                $name = $row['C'];
                $streetNumber = $row['D'];
                $postalCode = $row['E'];
                $city = $row['F'];
                $countryISO3 = $row['G'];
                // check if the file doesn't have empty columns
                if ($instcode != null & $name != null) {
                    // check if the data is upload in the database
                    $existingInstitute = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $instcode]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingInstitute) {
                        $institute = new Institute();
                        $instituteCountry = $entmanager->getRepository(Country::class)->findOneBy(['iso3' => $countryISO3]);
                        if (($instituteCountry != null) && ($instituteCountry instanceof \App\Entity\Country)) {
                            $institute->setCountry($instituteCountry);
                        }
                        if ($this->getUser()) {
                            $institute->setCreatedBy($this->getUser());
                        }
                        if ($acronym == null) {
                            $acronym = $instcode;
                        }
                        $institute->setInstcode($instcode);
                        $institute->setAcronym($acronym);
                        $institute->setName($name);
                        $institute->setStreetNumber($streetNumber);
                        $institute->setPostalCode($postalCode);
                        $institute->setCity($city);
                        $institute->setIsActive(true);
                        $institute->setCreatedAt(new \DateTime());
                        $entmanager->persist($institute);
                    }
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the Country table
            $totalInstituteAfter = $repoInstitute->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalInstituteBefore == 0) {
                $this->addFlash('success', $totalInstituteAfter . " institutes have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalInstituteAfter - $totalInstituteBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new institute has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " institute has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " institutes have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('institute_index'));
        }

        $context = [
            'title' => 'Institute Upload From Excel',
            'instituteUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('institute/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/institute_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'institute_template_example.xls');
        return $response;
       
    }
}
