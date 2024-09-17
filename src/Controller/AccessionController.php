<?php

namespace App\Controller;

use App\Entity\Accession;
use App\Entity\BiologicalStatus;
use App\Entity\CollectingMission;
use App\Entity\CollectingSource;
use App\Entity\Country;
use App\Entity\Institute;
use App\Entity\MLSStatus;
use App\Entity\StorageType;
use App\Entity\Taxonomy;
use App\Form\AccessionType;
use App\Form\AccessionUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\AccessionRepository;
use App\Repository\CountryRepository;
use App\Service\Datatable;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/accession", name="accession_")
 */
class AccessionController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AccessionRepository $accessionRepo, CountryRepository $countryRepo, Request $request): Response
    {
        $accessions =  $accessionRepo->findAll();
        $context = [
            'title' => 'Accession List',
            'accessions' => $accessions,
        ];
        return $this->render('accession/index.html.twig', $context);
    }

    /**
     * @Route("/datatable", name="datatable")
     */
    public function datatable(Datatable $datatableService, AccessionRepository $accessionRepo, Request $request)
    {
        $datatableRes = $datatableService->getDatatable($accessionRepo, $request);
        return $datatableRes;
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $accession = new Accession();
        $form = $this->createForm(AccessionType::class, $accession);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $accession->setCreatedBy($this->getUser());
            }
            $accession->setIsActive(true);
            $accession->setCreatedAt(new \DateTime());
            $entmanager->persist($accession);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('accession_index'));
        }

        $context = [
            'title' => 'Accession Creation',
            'accessionForm' => $form->createView()
        ];
        return $this->render('accession/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Accession $accessionSelected, AccessionRepository $acceRepo): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $accessions = $acceRepo->findBy(["accenumb" => $accessionSelected->getAccenumb()]);
        $context = [
            'title' => 'Accession Details',
            'accession' => $accessionSelected,
            'accessions' => $accessions
        ];
        return $this->render('accession/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Accession $accession, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('accession_edit', $accession);
        $form = $this->createForm(AccessionUpdateType::class, $accession);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($accession);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('accession_index'));
        }

        $context = [
            'title' => 'Accession Update',
            'accessionForm' => $form->createView()
        ];
        return $this->render('accession/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Accession $accession, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($accession->getId()) {
            $accession->setIsActive(!$accession->getIsActive());
        }
        $entmanager->persist($accession);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $accession->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }

    /**
     * @Route("/species/", name="species")
     */
    public function retrieveSpecies(AccessionRepository $accessionRepo): Response
    {
        $species =  $accessionRepo->getSpecies();
        $context = [
            'title' => 'Species',
            'species' => $species,
        ];
        return $this->render('accession/species.html.twig', $context);
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
            $repoAccession = $entmanager->getRepository(Accession::class);
            // Query how many rows are there in the Accession table
            $totalAccessionBefore = $repoAccession->createQueryBuilder('tab')
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
                    $this->addFlash('danger', "Fail to upload the file, try again ");
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
                $maintainerNumb = $row['B'];
                $acqDate = $row['C'];
                $storage = $row['D'];
                $donorCode = $row['E'];
                $donorNumb = $row['F'];
                $acceNumb = $row['G'];
                $acceName = $row['H'];
                $accePUI = $row['I'];
                $taxonid = $row['J'];
                $origCountry = $row['K'];
                $origMuni = $row['L'];
                $origAdMuni1 = $row['M'];
                $origAdMuni2 = $row['N'];
                $collSrc = $row['O'];
                $sampStat = $row['P'];
                $mlsStat = $row['Q'];
                $collNumb = $row['R'];
                $collCode = $row['S'];
                $collMissionName = $row['T'];
                $collDate = $row['U'];
                $decLatitude = $row['V'];
                $decLongitude = $row['W'];
                $elevation = $row['X'];
                $collSite = $row['Y'];
                $bredCode = $row['Z'];
                $breedingInfo = $row['AA'];
                $publicationRef = $row['AB'];
                $doi = $row['AC'];
                // check if the file doesn't have empty columns
                if ($acceNumb != null && $maintainerNumb != null && $accePUI) {
                    // check if the data is upload in the database
                    $existingAccession = $entmanager->getRepository(Accession::class)->findOneBy(['puid' => $accePUI]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingAccession) {
                        $accession = new Accession();
                        if ($this->getUser()) {
                            $accession->setCreatedBy($this->getUser());
                        }
                        try {
                            //code...
                            $accessionInstcode = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $instcode]);
                            if (($accessionInstcode != null) && ($accessionInstcode instanceof \App\Entity\Institute)) {
                                $accession->setInstcode($accessionInstcode);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the instcode " .$instcode);
                        }
                        
                        try {
                            //code...
                            $accessionDonorcode = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $donorCode]);
                            if (($accessionDonorcode != null) && ($accessionDonorcode instanceof \App\Entity\Institute)) {
                                $accession->setDonorcode($accessionDonorcode);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the donor code " .$donorCode);
                        }

                        try {
                            //code...
                            $accessionCollcode = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $collCode]);
                            if (($accessionCollcode != null) && ($accessionCollcode instanceof \App\Entity\Institute)) {
                                $accession->setCollcode($accessionCollcode);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the coll code " .$collCode);
                        }
                        
                        try {
                            //code...
                            $accessionBredcode = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $bredCode]);
                            if (($accessionBredcode != null) && ($accessionBredcode instanceof \App\Entity\Institute)) {
                                $accession->setBredcode($accessionBredcode);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the bred code " .$bredCode);
                        }

                        try {
                            //code...
                            $accessionCollSrc = $entmanager->getRepository(CollectingSource::class)->findOneBy(['ontology_id' => $collSrc]);
                            if (($accessionCollSrc != null) && ($accessionCollSrc instanceof \App\Entity\CollectingSource)) {
                                $accession->setCollsrc($accessionCollSrc);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the collecting source code " .$collSrc);
                        }
                        
                        try {
                            //code...
                            $accessionBioStatus = $entmanager->getRepository(BiologicalStatus::class)->findOneBy(['ontology_id' => $sampStat]);
                            if (($accessionBioStatus != null) && ($accessionBioStatus instanceof \App\Entity\BiologicalStatus)) {
                                $accession->setSampstat($accessionBioStatus);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the biological status " .$sampStat);
                        }

                        try {
                            //code...
                            $accessionMLSStatus = $entmanager->getRepository(MLSStatus::class)->findOneBy(['ontology_id' => $mlsStat]);
                            if (($accessionMLSStatus != null) && ($accessionMLSStatus instanceof \App\Entity\MLSStatus)) {
                                $accession->setMlsStatus($accessionMLSStatus);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the mls status " .$mlsStat);
                        }

                        try {
                            //code...
                            $accessionTaxonomy = $entmanager->getRepository(Taxonomy::class)->findOneBy(['taxonid' => $taxonid]);
                            if (($accessionTaxonomy != null) && ($accessionTaxonomy instanceof \App\Entity\Taxonomy)) {
                                $accession->setTaxon($accessionTaxonomy);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the taxonomy " .$taxonid);
                        }

                        try {
                            //code...
                            $accessionCollMissId = $entmanager->getRepository(CollectingMission::class)->findOneBy(['name' => $collMissionName]);
                            if (($accessionCollMissId != null) && ($accessionCollMissId instanceof \App\Entity\CollectingMission)) {
                                $accession->setCollmissid($accessionCollMissId);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the collecting mission " .$collMissionName);
                        }

                        try {
                            //code...
                            $accessionCountry = $entmanager->getRepository(Country::class)->findOneBy(['iso3' => $origCountry]);
                            if (($accessionCountry != null) && ($accessionCountry instanceof \App\Entity\Country)) {
                                $accession->setOrigcty($accessionCountry);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the country " .$origCountry);
                        }

                        try {
                            //code...
                            $accessionStorageType = $entmanager->getRepository(StorageType::class)->findOneBy(['ontology_id' => $storage]);
                            if (($accessionStorageType != null) && ($accessionStorageType instanceof \App\Entity\StorageType)) {
                                $accession->setStorage($accessionStorageType);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the storage type " .$storage);
                        }

                        try {
                            //code...
                            if ($donorNumb) {
                                $accession->setDonornumb($donorNumb);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the donor number " .$donorNumb);
                        }

                        try {
                            //code...
                            if ($collNumb) {
                                $accession->setCollnumb($collNumb);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the collecting number " .$collNumb);
                        }

                        try {
                            //code...
                            $accession->setAccenumb($acceNumb);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession number " .$acceNumb);
                        }

                        try {
                            //code...
                            $accession->setMaintainernumb($maintainerNumb);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the maintainer number " .$maintainerNumb);
                        }

                        try {
                            //code...
                            $accession->setAccename($acceName);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession name " .$acceName);
                        }

                        try {
                            //code...
                            $accession->setPuid($accePUI);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession PUI " .$accePUI);
                        }

                        try {
                            //code...
                            $accession->setOrigmuni($origMuni);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession municipality of origin " .$origMuni);
                        }

                        try {
                            //code...
                            $accession->setOrigadmin1($origAdMuni1);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession municipality of origin " .$origAdMuni1);
                        }

                        try {
                            //code...
                            $accession->setOrigadmin2($origAdMuni2);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession municipality of origin " .$origAdMuni2);
                        }

                        try {
                            //code...
                            $accession->setBreedingInfo($breedingInfo);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession breeding info " .$breedingInfo);
                        }

                        try {
                            //code...
                            $accession->setCollsite($collSite);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession collecting site " .$collSite);
                        }

                        try {
                            //code...
                            $accession->setAcqdate($acqDate);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession acquisition date " .$acqDate);
                        }

                        try {
                            //code...
                            $accession->setColldate($collDate);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession collecting date " .$collDate);
                        }

                        try {
                            //code...
                            $accession->setDeclatitude($decLatitude);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession decimal latitute " .$decLatitude);
                        }

                        try {
                            //code...
                            $accession->setDeclongitude($decLongitude);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession decimal longitude " .$decLongitude);
                        }

                        try {
                            //code...
                            $accession->setElevation($elevation);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession elevation " .$elevation);
                        }

                        
                        try {
                            //code...
                            $accession->setDoi($doi);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession doi " .$doi);
                        }

                        $publicationRef = explode(",", $publicationRef);
                        
                        try {
                            //code...
                            $accession->setPublicationRef($publicationRef);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession publication Reference " .$publicationRef);
                        }
                        
                        //dd($accession);
                        $accession->setIsActive(true);
                        $accession->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($accession);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalAccessionAfter = $repoAccession->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalAccessionBefore == 0) {
                $this->addFlash('success', $totalAccessionAfter . " accessions have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalAccessionAfter - $totalAccessionBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new accession has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " accession has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " accessions have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('accession_index'));
        }

        $context = [
            'title' => 'Accession Upload From Excel',
            'accessionUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('accession/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/accession_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'accession_template_example.xlsx');
        return $response;
       
    }
}

