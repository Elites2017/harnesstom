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
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($name != null) {
                    // check if the data is upload in the database
                    $existingKinshipAlgorithm = $entmanager->getRepository(KinshipAlgorithm::class)->findOneBy(['name' => $name]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingKinshipAlgorithm) {
                        $kinshipAlgorithm = new KinshipAlgorithm();
                        if ($this->getUser()) {
                            $kinshipAlgorithm->setCreatedBy($this->getUser());
                        }
                        $kinshipAlgorithm->setName($name);
                        $kinshipAlgorithm->setIsActive(true);
                        $kinshipAlgorithm->setCreatedAt(new \DateTime());
                        $entmanager->persist($kinshipAlgorithm);
                    }
                }
            }
            $entmanager->flush();
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
    public function factorTypeTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/kinship_algorithm_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'kinship_algorithm_template_example.xls');
        return $response;
       
    }
}