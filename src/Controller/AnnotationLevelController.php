<?php

namespace App\Controller;

use App\Entity\AnnotationLevel;
use App\Form\AnnotationLevelType;
use App\Form\AnnotationLevelUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\AnnotationLevelRepository;
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
 * @Route("annotation/level", name="annotation_level_")
 */
class AnnotationLevelController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AnnotationLevelRepository $annotationLevelRepo): Response
    {
        $annotationLevels =  $annotationLevelRepo->findAll();
        $parentsOnly = $annotationLevelRepo->getParentsOnly();
        $context = [
            'title' => 'Annotation Level List',
            'annotationLevels' => $annotationLevels,
            'parentsOnly' => $parentsOnly
        ];
        return $this->render('annotation_level/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $annotationLevel = new AnnotationLevel();
        $form = $this->createForm(AnnotationLevelType::class, $annotationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $annotationLevel->setCreatedBy($this->getUser());
            }
            $annotationLevel->setIsActive(true);
            $annotationLevel->setCreatedAt(new \DateTime());
            $entmanager->persist($annotationLevel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('annotation_level_index'));
        }

        $context = [
            'title' => 'Annotation Level Creation',
            'annotationLevelForm' => $form->createView()
        ];
        return $this->render('annotation_level/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AnnotationLevel $annotationLevelSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Annotation Level Details',
            'annotationLevel' => $annotationLevelSelected
        ];
        return $this->render('annotation_level/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AnnotationLevel $annotationLevel, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('annotation_level_edit', $annotationLevel);
        $form = $this->createForm(AnnotationLevelUpdateType::class, $annotationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($annotationLevel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('annotation_level_index'));
        }

        $context = [
            'title' => 'Annotation Level Update',
            'annotationLevelForm' => $form->createView()
        ];
        return $this->render('annotation_level/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AnnotationLevel $annotationLevel, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($annotationLevel->getId()) {
            $annotationLevel->setIsActive(!$annotationLevel->getIsActive());
        }
        $entmanager->persist($annotationLevel);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $annotationLevel->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
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
            $repoAnnotationLevel = $entmanager->getRepository(AnnotationLevel::class);
            // Query how many rows are there in the AnnotationLevel table
            $totalAnnotationLevelBefore = $repoAnnotationLevel->createQueryBuilder('tab')
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
                $ontology_id = $row['A'];
                $name = $row['B'];
                $description = $row['C'];
                $parentTermString = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null) {
                    // check if the data is upload in the database
                    $existingAnnotationLevel = $entmanager->getRepository(AnnotationLevel::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingAnnotationLevel) {
                        $annotationLevel = new AnnotationLevel();
                        if ($this->getUser()) {
                            $annotationLevel->setCreatedBy($this->getUser());
                        }
                        $annotationLevel->setOntologyId($ontology_id);
                        $annotationLevel->setName($name);
                        if ($description != null) {
                            $annotationLevel->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $annotationLevel->setParOnt($parentTermString);
                        }
                        $annotationLevel->setIsActive(true);
                        $annotationLevel->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($annotationLevel);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(AnnotationLevel::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\AnnotationLevel)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(AnnotationLevel::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE annotation_level SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalAnnotationLevelAfter = $repoAnnotationLevel->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalAnnotationLevelBefore == 0) {
                $this->addFlash('success', $totalAnnotationLevelAfter . " annotation levels have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalAnnotationLevelAfter - $totalAnnotationLevelBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new annotation level has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " annotation level has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " annotation levels have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('annotation_level_index'));
        }

        $context = [
            'title' => 'Annotation Level Upload From Excel',
            'annotationLevelUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('annotation_level/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/annotation_level_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'annotation_level_template_example.xls');
        return $response;
       
    }
}

