<?php

namespace App\Controller;

use App\Entity\GenotypingPlatform;
use App\Entity\Software;
use App\Entity\VariantSetMetadata;
use App\Form\UploadFromExcelType;
use App\Form\VariantSetMetadataType;
use App\Form\VariantSetMetadataUpdateType;
use App\Repository\VariantSetMetadataRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;

// set a class level route
/**
 * @Route("/variant/set/metadata", name="variant_set_metadata_")
 */
class VariantSetMetadataController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(VariantSetMetadataRepository $variantSetMetadataRepo): Response
    {
        $variantSetMetadatas =  $variantSetMetadataRepo->findAll();
        $context = [
            'title' => 'Variant Set Metadata List',
            'variantSetMetadatas' => $variantSetMetadatas
        ];
        return $this->render('variant_set_metadata/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variantSetMetadata = new VariantSetMetadata();
        $form = $this->createForm(VariantSetMetadataType::class, $variantSetMetadata);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $variantSetMetadata->setCreatedBy($this->getUser());
            }
            // get the file (name from the form. It takes the name of the form)
            $file = $request->files->get('variant_set_metadata')['dataUpload'];
            // set the folder to send the file to
            $fileFolder = __DIR__ . '/../../public/uploads/vcf/';
            // apply md5 function to generate a unique id for the file and concat it with the original file name
            if ($file->getClientOriginalName()) {
                $filePathName = md5(uniqid()) . $file->getClientOriginalName();
                try {
                    $file->move($fileFolder, $filePathName);
                    $variantSetMetadata->setDataUpload($filePathName);
                    $variantSetMetadata->setFileUrl($filePathName);
                } catch (\Throwable $th) {
                    //throw $th;
                    $this->addFlash('danger', "Fail to upload the VCF file, try again ");
                }
            } else {
                $this->addFlash('danger', "Error in the VCF file name, try to rename the file and try again");
            }
            $variantSetMetadata->setIsActive(true);
            $variantSetMetadata->setCreatedAt(new \DateTime());
            $entmanager->persist($variantSetMetadata);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('variant_set_metadata_index'));
        }

        $context = [
            'title' => 'Variant Set Metadata Creation',
            'variantSetMetadataForm' => $form->createView()
        ];
        return $this->render('variant_set_metadata/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(VariantSetMetadata $variantSetMetadataSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Variant Set Metadata Details',
            'variantSetMetadata' => $variantSetMetadataSelected
        ];
        return $this->render('variant_set_metadata/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(VariantSetMetadata $variantSetMetadata, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('variant_set_metadata_edit', $variantSetMetadata);
        $fileFolder = __DIR__ . '/../../public/uploads/vcf/';
        $currentFile = $variantSetMetadata->getDataUpload();
        if ($currentFile !== null) {
            try {
                $variantSetMetadata->setDataUpload(new File($fileFolder.$currentFile));
            } catch (\Throwable $th) {
                //throw $th;
                $this->addFlash('danger', "The file doesn't exist on the server, try to upload it first ");
            }
        }

        $form = $this->createForm(VariantSetMetadataUpdateType::class, $variantSetMetadata);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // check if there is a new file
            if ($variantSetMetadata->getDataUpload() != null) {
                // get the file (name from the form. It takes the name of the form)
                $file = $request->files->get('variant_set_metadata_update')['dataUpload'];
                // apply md5 function to generate a unique id for the file and concat it with the original file name
                if ($file->getClientOriginalName()) {
                    $filePathName = md5(uniqid()) . $file->getClientOriginalName();
                    try {
                        $file->move($fileFolder, $filePathName);
                        $variantSetMetadata->setDataUpload($filePathName);
                        $variantSetMetadata->setFileUrl($filePathName);
                    } catch (\Throwable $th) {
                        //throw $th;
                        $this->addFlash('danger', "Fail to upload the VCF file, try again ");
                    }
                } else {
                    $this->addFlash('danger', "Error in the VCF file name, try to rename the file and try again");
                }
            } else {
                // restore the current file 
                $variantSetMetadata->setDataUpload($currentFile);
                $variantSetMetadata->setFileUrl($currentFile);
            }
            $entmanager->persist($variantSetMetadata);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('variant_set_metadata_index'));
        }

        $context = [
            'title' => 'Variant Set Metadata Update',
            'variantSetMetadataForm' => $form->createView()
        ];
        return $this->render('variant_set_metadata/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(VariantSetMetadata $variantSetMetadata, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($variantSetMetadata->getId()) {
            $variantSetMetadata->setIsActive(!$variantSetMetadata->getIsActive());
        }
        $entmanager->persist($variantSetMetadata);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $variantSetMetadata->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
    }

    /**
     * @Route("/upload/vcf/{id}", name="upload_vcf")
     */
    public function upload_vcf(VariantSetMetadata $variantSetMetadata, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // get teh file
        //dd($request->files->get('vcf_upload')['vcf']);
        $file = $request->files->get('vcf_upload')['vcf'];
        // set the folder to send the file to
        $fileFolder = __DIR__ . '/../../public/uploads/vcf/';
        // apply md5 function to generate a unique id for the file and concat it with the original file name
        if ($file->getClientOriginalName()) {
            $filePathName = md5(uniqid()) . $file->getClientOriginalName();
            try {
                $file->move($fileFolder, $filePathName);
                if ($variantSetMetadata->getId()) {
                    $variantSetMetadata->setFileUrl($filePathName);
                }
                $entmanager->persist($variantSetMetadata);
                $entmanager->flush();
        
                return $this->redirect($this->generateUrl('variant_set_metadata_index'));
            } catch (\Throwable $th) {
                //throw $th;
                $this->addFlash('danger', "Fail to upload the file, try again");
            }
        } else {
            $this->addFlash('danger', "Error in the file name, try to rename the file and try again");
        }
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
            $repoVariantSetMetadata = $entmanager->getRepository(VariantSetMetadata::class);
            // Query how many rows are there in the VariantSetMetadata table
            $totalVariantSetMetadataBefore = $repoVariantSetMetadata->createQueryBuilder('tab')
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
                $variantSetName = $row['A'];
                $variantSetDesc = $row['B'];
                $genotypingPlatform = $row['C'];
                $variantSetFilters = $row['D'];
                $softwareontologyId = $row['E'];
                $variantCount = $row['F'];
                $dataUploadVCF = $row['G'];
                $fileURL = $row['H'];
                $publicationRef = $row['I'];
                // check if the file doesn't have empty columns
                if ($variantSetName != null & $variantSetDesc != null && $genotypingPlatform != null ) {
                    // check if the data is upload in the database
                    $existingVariantSetMetadata = $entmanager->getRepository(VariantSetMetadata::class)->findOneBy(['name' => $variantSetName]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingVariantSetMetadata) {
                        $variantSetMetadata = new VariantSetMetadata();
                        $variantSetMetadataGenotypingPlatform = $entmanager->getRepository(GenotypingPlatform::class)->findOneBy(['name' => $genotypingPlatform]);
                        if (($variantSetMetadataGenotypingPlatform != null) && ($variantSetMetadataGenotypingPlatform instanceof \App\Entity\GenotypingPlatform)) {
                            $variantSetMetadata->setGenotypingPlatform($variantSetMetadataGenotypingPlatform);
                        }
                        $variantSetMetadataSoftware = $entmanager->getRepository(Software::class)->findOneBy(['ontology_id' => $softwareontologyId]);
                        if (($variantSetMetadataSoftware != null) && ($variantSetMetadataSoftware instanceof \App\Entity\Software)) {
                            $variantSetMetadata->setSoftware($variantSetMetadataSoftware);
                        }
                        if ($this->getUser()) {
                            $variantSetMetadata->setCreatedBy($this->getUser());
                        }
                        if ($variantSetFilters) {
                            try {
                                //code...
                                $variantSetMetadata->setFilters($variantSetFilters);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the variant set metadata variant filters " .$variantSetFilters);
                            }
                        }
                        if ($variantCount) {
                            try {
                                //code...
                                $variantSetMetadata->setVariantCount($variantCount);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the variant set metadata variant count " .$variantCount);
                            }
                        }

                        try {
                            //code...
                            $variantSetMetadata->setName($variantSetName);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the variant set metadata name " .$variantSetName);
                        }
                        
                        try {
                            //code...
                            $variantSetMetadata->setDescription($variantSetDesc);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the variant set metadata description " .$variantSetDesc);
                        }
                        
                        $publicationRef = explode(";", $publicationRef);
                        
                        try {
                            //code...
                            $variantSetMetadata->setPublicationRef($publicationRef);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the variant set metadata publication Reference " .$publicationRef);
                        }

                        if($dataUploadVCF){
                            try {
                                //code...
                                $variantSetMetadata->setDataUpload($dataUploadVCF);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the variant set metadata VCF file " .$dataUploadVCF);
                            }
                        }
                        
                        $variantSetMetadata->setIsActive(true);
                        $variantSetMetadata->setCreatedAt(new \DateTime());

                        try {
                            //code...
                            $entmanager->persist($variantSetMetadata);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            //$entmanager->flush();
            // Query how many rows are there in the Country table
            $totalVariantSetMetadataAfter = $repoVariantSetMetadata->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalVariantSetMetadataBefore == 0) {
                $this->addFlash('success', $totalVariantSetMetadataAfter . " Variant Set Metadata have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalVariantSetMetadataAfter - $totalVariantSetMetadataBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new Variant Set Meta data has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " Variant Set Meta data has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " Variant Set Meta datas have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('variant_set_metadata_index'));
        }

        $context = [
            'title' => 'Variant Set Metadata Upload From Excel',
            'variantSetMetadataUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('variant_set_metadata/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/variant_set_metadata_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'variant_set_metadata_template_example.xlsx');
        return $response;
       
    }
}
