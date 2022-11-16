<?php

namespace App\Controller;

use App\Entity\TraitProcessing;
use App\Form\TraitProcessingType;
use App\Form\TraitProcessingUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\TraitProcessingRepository;
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
 * @Route("/trait/processing", name="trait_processing_")
 */
class TraitProcessingController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TraitProcessingRepository $traitProcessingRepo): Response
    {
        $traitProcessings =  $traitProcessingRepo->findAll();
        $context = [
            'title' => 'Trait Processing List',
            'traitProcessings' => $traitProcessings
        ];
        return $this->render('trait_processing/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $traitProcessing = new TraitProcessing();
        $form = $this->createForm(TraitProcessingType::class, $traitProcessing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $traitProcessing->setCreatedBy($this->getUser());
            }
            $traitProcessing->setIsActive(true);
            $traitProcessing->setCreatedAt(new \DateTime());
            $entmanager->persist($traitProcessing);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trait_processing_index'));
        }

        $context = [
            'title' => 'Trait Processing Creation',
            'traitProcessingForm' => $form->createView()
        ];
        return $this->render('trait_processing/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(TraitProcessing $traitProcessingselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Trait Processing Details',
            'traitProcessing' => $traitProcessingselected
        ];
        return $this->render('trait_processing/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(TraitProcessing $traitProcessing, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('trait_processing_edit', $traitProcessing);
        $form = $this->createForm(TraitProcessingUpdateType::class, $traitProcessing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($traitProcessing);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trait_processing_index'));
        }

        $context = [
            'title' => 'Trait Processing Update',
            'traitProcessingForm' => $form->createView()
        ];
        return $this->render('trait_processing/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(TraitProcessing $traitProcessing, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($traitProcessing->getId()) {
            $traitProcessing->setIsActive(!$traitProcessing->getIsActive());
        }
        $entmanager->persist($traitProcessing);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $traitProcessing->getIsActive()
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
            $repoTrait = $entmanager->getRepository(TraitProcessing::class);
            // Query how many rows are there in the trait table
            $totalTraitProcessingBefore = $repoTrait->createQueryBuilder('tab')
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
                $ontology_id = $row['A'];
                $name = $row['B'];
                $description = $row['C'];
                $parentTermString = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null) {
                    // check if the data is upload in the database
                    $existingTraitProcessing = $entmanager->getRepository(TraitProcessing::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingTraitProcessing) {
                        $traitProcessing = new TraitProcessing();
                        if ($this->getUser()) {
                            $traitProcessing->setCreatedBy($this->getUser());
                        }
                        $traitProcessing->setOntologyId($ontology_id);
                        $traitProcessing->setName($name);
                        if ($description != null) {
                            $traitProcessing->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $traitProcessing->setParOnt($parentTermString);
                        }
                        $traitProcessing->setIsActive(true);
                        $traitProcessing->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($traitProcessing);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            //$entmanager->flush();
            // get the connection
            $connexion = $entmanager->getConnection();
            // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $parentTerm != null ) {
                    // check if the data is upload in the database
                    $ontologyIdParentTerm = $entmanager->getRepository(TraitProcessing::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\TraitProcessing)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(TraitProcessing::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE trait_processing SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }
            
            $totalTraitProcessingAfter = $repoTrait->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalTraitProcessingBefore == 0) {
                $this->addFlash('success', $totalTraitProcessingAfter . " traits preprocessing have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalTraitProcessingAfter - $totalTraitProcessingBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new trait preprocessing has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " trait preprocessing has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " traits preprocessing have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('trait_processing_index'));
        }

        $context = [
            'title' => 'Trait Processing Upload From Excel',
            'traitProcessingUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('trait_processing/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/trait_processing_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'trait_processing_template_example.xls');
        return $response;
       
    }
}
