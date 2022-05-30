<?php

namespace App\Controller;

use App\Entity\Software;
use App\Form\SoftwareType;
use App\Form\SoftwareUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\SoftwareRepository;
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
 * @Route("software", name="software_")
 */
class SoftwareController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SoftwareRepository $softwareRepo): Response
    {
        $softwares =  $softwareRepo->findAll();
        $context = [
            'title' => 'Software List',
            'softwares' => $softwares
        ];
        return $this->render('software/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $software = new Software();
        $form = $this->createForm(SoftwareType::class, $software);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $software->setCreatedBy($this->getUser());
            }
            $software->setIsActive(true);
            $software->setCreatedAt(new \DateTime());
            $entmanager->persist($software);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('software_index'));
        }

        $context = [
            'title' => 'Software Creation',
            'softwareForm' => $form->createView()
        ];
        return $this->render('software/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Software $softwareSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Software Details',
            'software' => $softwareSelected
        ];
        return $this->render('software/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Software $software, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('software_edit', $software);
        $form = $this->createForm(SoftwareUpdateType::class, $software);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($software);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('software_index'));
        }

        $context = [
            'title' => 'Software Update',
            'softwareForm' => $form->createView()
        ];
        return $this->render('software/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Software $software, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($software->getId()) {
            $software->setIsActive(!$software->getIsActive());
        }
        $entmanager->persist($software);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $software->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('software_home'));
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
            $repoSoftware = $entmanager->getRepository(Software::class);
            // Query how many rows are there in the Software table
            $totalSoftwareBefore = $repoSoftware->createQueryBuilder('tab')
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
                $parentTerm = $row['C'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null & $name != null) {
                    // check if the data is upload in the database
                    $existingSoftware = $entmanager->getRepository(Software::class)->findOneBy(['name' => $name]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingSoftware) {
                        $software = new Software();
                        if ($this->getUser()) {
                            $software->setCreatedBy($this->getUser());
                        }
                        $software->setName($name);
                        $software->setOntologyId($ontology_id);
                        $software->setParentTerm($parentTerm);
                        $software->setIsActive(true);
                        $software->setCreatedAt(new \DateTime());
                        $entmanager->persist($software);
                    }
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the Country table
            $totalSoftwareAfter = $repoSoftware->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalSoftwareBefore == 0) {
                $this->addFlash('success', $totalSoftwareAfter . " softwares have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalSoftwareAfter - $totalSoftwareBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new software has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " software has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " softwares have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('software_index'));
        }

        $context = [
            'title' => 'Software Upload From Excel',
            'softwareUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('software/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/software_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'software_template_example.xls');
        return $response;
       
    }
}
