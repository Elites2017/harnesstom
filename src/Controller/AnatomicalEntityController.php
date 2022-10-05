<?php

namespace App\Controller;

use App\Entity\AnatomicalEntity;
use App\Form\AnatomicalEntityType;
use App\Form\AnatomicalEntityUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\AnatomicalEntityRepository;
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
 * @Route("anatomical/entity", name="anatomical_entity_")
 */
class AnatomicalEntityController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AnatomicalEntityRepository $anatomicalEntityRepo): Response
    {
        $anatomicalEntities =  $anatomicalEntityRepo->findAll();
        $context = [
            'title' => 'Anatomical Entity List',
            'anatomicalEntities' => $anatomicalEntities
        ];
        return $this->render('anatomical_entity/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $anatomicalEntity = new AnatomicalEntity();
        $form = $this->createForm(AnatomicalEntityType::class, $anatomicalEntity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $anatomicalEntity->setCreatedBy($this->getUser());
            }
            $anatomicalEntity->setIsActive(true);
            $anatomicalEntity->setCreatedAt(new \DateTime());
            $entmanager->persist($anatomicalEntity);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('anatomical_entity_index'));
        }

        $context = [
            'title' => 'Anatomical Entity Creation',
            'anatomicalEntityForm' => $form->createView()
        ];
        return $this->render('anatomical_entity/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AnatomicalEntity $anatomicalEntitySelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Anatomical Entity Details',
            'anatomicalEntity' => $anatomicalEntitySelected
        ];
        return $this->render('anatomical_entity/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AnatomicalEntity $anatomicalEntity, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('anatomical_entity_edit', $anatomicalEntity);
        $form = $this->createForm(AnatomicalEntityUpdateType::class, $anatomicalEntity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($anatomicalEntity);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('anatomical_entity_index'));
        }

        $context = [
            'title' => 'Anatomical Entity Update',
            'anatomicalEntityForm' => $form->createView()
        ];
        return $this->render('anatomical_entity/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AnatomicalEntity $anatomicalEntity, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($anatomicalEntity->getId()) {
            $anatomicalEntity->setIsActive(!$anatomicalEntity->getIsActive());
        }
        $entmanager->persist($anatomicalEntity);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $anatomicalEntity->getIsActive()
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
            $repoAnatomicalEntity = $entmanager->getRepository(AnatomicalEntity::class);
            // Query how many rows are there in the AnatomicalEntity table
            $totalAnatomicalEntityBefore = $repoAnatomicalEntity->createQueryBuilder('tab')
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
                $name = $row['B'];
                $description = $row['C'];
                $ontology_id = $row['A'];
                $parentTermString = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null)  {
                    // check if the data has already been uploaded in the database
                    $existingAnatomicalEntity = $entmanager->getRepository(AnatomicalEntity::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingAnatomicalEntity) {
                        $anatomicalEntity = new AnatomicalEntity();
                        if ($this->getUser()) {
                            $anatomicalEntity->setCreatedBy($this->getUser());
                        }
                        if ($description != null) {
                            $anatomicalEntity->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $anatomicalEntity->setParOnt($parentTermString);
                        }
                        $anatomicalEntity->setName($name);
                        $anatomicalEntity->setOntologyId($ontology_id);
                        $anatomicalEntity->setIsActive(true);
                        $anatomicalEntity->setCreatedAt(new \DateTime());
                        $entmanager->persist($anatomicalEntity);
                    }
                } else {
                    $this->addFlash('danger', "Check your file again, Name and ontology_id must be filled / provided");
                }
            }
            $entmanager->flush();
            // get the connection
            $connexion = $entmanager->getConnection();
            // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $parentTerm != null ) {
                    // check if the data is upload in the database
                    $ontologyIdParentTerm = $entmanager->getRepository(AnatomicalEntity::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\AnatomicalEntity)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(AnatomicalEntity::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE anatomical_entity SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalAnatomicalEntityAfter = $repoAnatomicalEntity->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalAnatomicalEntityBefore == 0) {
                $this->addFlash('success', $totalAnatomicalEntityAfter . " anatomical entities have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalAnatomicalEntityAfter - $totalAnatomicalEntityBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new anatomical entity has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " anatomical entity has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " anatomical entities have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('anatomical_entity_index'));
        }

        $context = [
            'title' => 'Anatomical Entity Upload From Excel',
            'anatomicalEntityUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('anatomical_entity/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/anatomical_entity_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'anatomical_entity_template_example.xls');
        return $response;
       
    }
}
