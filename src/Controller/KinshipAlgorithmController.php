<?php

namespace App\Controller;

use App\Entity\KinshipAlgorithm;
use App\Form\KinshipAlgorithmType;
use App\Form\KinshipAlgorithmUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\KinshipAlgorithmRepository;
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
 * @Route("kinship/algorithm", name="kinship_algorithm_")
 */
class KinshipAlgorithmController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(KinshipAlgorithmRepository $kinshipAlgorithmRepo): Response
    {
        $kinshipAlgorithms =  $kinshipAlgorithmRepo->findAll();
        $context = [
            'title' => 'Kinship Algorithm List',
            'kinshipAlgorithms' => $kinshipAlgorithms
        ];
        return $this->render('kinship_algorithm/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $kinshipAlgorithm = new KinshipAlgorithm();
        $form = $this->createForm(KinshipAlgorithmType::class, $kinshipAlgorithm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $kinshipAlgorithm->setCreatedBy($this->getUser());
            }
            $kinshipAlgorithm->setIsActive(true);
            $kinshipAlgorithm->setCreatedAt(new \DateTime());
            $entmanager->persist($kinshipAlgorithm);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('kinship_algorithm_index'));
        }

        $context = [
            'title' => 'Kinship Algorithm Creation',
            'kinshipAlgorithmForm' => $form->createView()
        ];
        return $this->render('kinship_algorithm/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(KinshipAlgorithm $kinshipAlgorithmSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Kinship Algorithm Details',
            'kinshipAlgorithm' => $kinshipAlgorithmSelected
        ];
        return $this->render('kinship_algorithm/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(KinshipAlgorithm $kinshipAlgorithm, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('kinship_algorithm_edit', $kinshipAlgorithm);
        $form = $this->createForm(KinshipAlgorithmUpdateType::class, $kinshipAlgorithm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($kinshipAlgorithm);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('kinship_algorithm_index'));
        }

        $context = [
            'title' => 'Kinship Algorithm Update',
            'kinshipAlgorithmForm' => $form->createView()
        ];
        return $this->render('kinship_algorithm/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(kinshipAlgorithm $kinshipAlgorithm, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($kinshipAlgorithm->getId()) {
            $kinshipAlgorithm->setIsActive(!$kinshipAlgorithm->getIsActive());
        }
        $entmanager->persist($kinshipAlgorithm);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $kinshipAlgorithm->getIsActive()
        ], 200);
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
            $repoKinshipAlgorithm = $entmanager->getRepository(KinshipAlgorithm::class);
            // Query how many rows are there in the KinshipAlgorithm table
            $totalKinshipAlgorithmBefore = $repoKinshipAlgorithm->createQueryBuilder('tab')
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
                    $existingKinshipAlgorithm = $entmanager->getRepository(KinshipAlgorithm::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingKinshipAlgorithm) {
                        $kinshipAlgorithm = new KinshipAlgorithm();
                        if ($this->getUser()) {
                            $kinshipAlgorithm->setCreatedBy($this->getUser());
                        }
                        $kinshipAlgorithm->setOntologyId($ontology_id);
                        $kinshipAlgorithm->setName($name);
                        if ($description != null) {
                            $kinshipAlgorithm->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $kinshipAlgorithm->setParOnt($parentTermString);
                        }
                        $kinshipAlgorithm->setIsActive(true);
                        $kinshipAlgorithm->setCreatedAt(new \DateTime());

                        try {
                            //code...
                            $entmanager->persist($kinshipAlgorithm);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(KinshipAlgorithm::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\KinshipAlgorithm)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(KinshipAlgorithm::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE kinship_algorithm SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalKinshipAlgorithmAfter = $repoKinshipAlgorithm->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalKinshipAlgorithmBefore == 0) {
                $this->addFlash('success', $totalKinshipAlgorithmAfter . " kinship algorithms have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalKinshipAlgorithmAfter - $totalKinshipAlgorithmBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new kinship algorithm has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " kinship algorithm has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " kinship algorithms have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('kinship_algorithm_index'));
        }

        $context = [
            'title' => 'Identification Level Upload From Excel',
            'kinshipAlgorithmUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('kinship_algorithm/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/kinship_algorithm_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'kinship_algorithm_template_example.xls');
        return $response;
       
    }
}