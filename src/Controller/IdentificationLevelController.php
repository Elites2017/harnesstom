<?php

namespace App\Controller;

use App\Entity\IdentificationLevel;
use App\Form\IdentificationLevelType;
use App\Form\IdentificationLevelUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\IdentificationLevelRepository;
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
 * @Route("identification/level", name="identification_level_")
 */
class IdentificationLevelController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(IdentificationLevelRepository $identificationLevelRepo): Response
    {
        $identificationLevels =  $identificationLevelRepo->findAll();
        $parentsOnly = $identificationLevelRepo->getParentsOnly();
        $context = [
            'title' => 'Identification Level List',
            'identificationLevels' => $identificationLevels,
            'parentsOnly' => $parentsOnly
        ];
        return $this->render('identification_level/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $identificationLevel = new IdentificationLevel();
        $form = $this->createForm(IdentificationLevelType::class, $identificationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $identificationLevel->setCreatedBy($this->getUser());
            }
            $identificationLevel->setIsActive(true);
            $identificationLevel->setCreatedAt(new \DateTime());
            $entmanager->persist($identificationLevel);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('identification_level_index'));
        }

        $context = [
            'title' => 'Identification Level Creation',
            'identificationLevelForm' => $form->createView()
        ];
        return $this->render('identification_level/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(IdentificationLevel $identificationLevelSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Identification Level Details',
            'identificationLevel' => $identificationLevelSelected
        ];
        return $this->render('identification_level/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(IdentificationLevel $identificationLevel, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('identification_level_edit', $identificationLevel);
        $form = $this->createForm(IdentificationLevelUpdateType::class, $identificationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($identificationLevel);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('identification_level_index'));
        }

        $context = [
            'title' => 'Identification Level Update',
            'identificationLevelForm' => $form->createView()
        ];
        return $this->render('identification_level/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(IdentificationLevel $identificationLevel, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($identificationLevel->getId()) {
            $identificationLevel->setIsActive(!$identificationLevel->getIsActive());
        }
        $entmanager->persist($identificationLevel);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $identificationLevel->getIsActive()
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
            $repoIdentificationLevel = $entmanager->getRepository(IdentificationLevel::class);
            // Query how many rows are there in the IdentificationLevel table
            $totalIdentificationLevelBefore = $repoIdentificationLevel->createQueryBuilder('tab')
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
                    $existingIdentificationLevel = $entmanager->getRepository(IdentificationLevel::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingIdentificationLevel) {
                        $identificationLevel = new IdentificationLevel();
                        if ($this->getUser()) {
                            $identificationLevel->setCreatedBy($this->getUser());
                        }
                        $identificationLevel->setOntologyId($ontology_id);
                        $identificationLevel->setName($name);
                        if ($description != null) {
                            $identificationLevel->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $identificationLevel->setParOnt($parentTermString);
                        }
                        $identificationLevel->setIsActive(true);
                        $identificationLevel->setCreatedAt(new \DateTime());

                        try {
                            //code...
                            $entmanager->persist($identificationLevel);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(IdentificationLevel::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\IdentificationLevel)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(IdentificationLevel::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE identification_level SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalIdentificationLevelAfter = $repoIdentificationLevel->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalIdentificationLevelBefore == 0) {
                $this->addFlash('success', $totalIdentificationLevelAfter . " identification levels have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalIdentificationLevelAfter - $totalIdentificationLevelBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new identification level has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " identification level has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " identification levels have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('identification_level_index'));
        }

        $context = [
            'title' => 'Identification Level Upload From Excel',
            'identificationLevelUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('identification_level/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/identification_level_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'identification_level_template_example.xls');
        return $response;
       
    }
}