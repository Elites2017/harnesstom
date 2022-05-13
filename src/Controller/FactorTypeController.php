<?php

/* 
    This is the factorTypeController which contains the CRUD method of this object.
    1. The index function is to list all the object from the DB
    
    2. The create function is to create the object by
        2.1 initializes the object
        2.2 create the form from the factorTypeType form and do the binding
        2.2.1 pass the request to the form to handle it
        2.2.2 Analyze the form, if everything is okay, save the object and redirect the user
        if there is any problem, the same page will be display to the user with the context
    
    3. The details function is just to show the details of the selected object to the user.

    4. the update funtion is a little bit similar with the create one, because they almost to the same thing, but
    in the update, we don't initialize the object as it will come from the injection and it is supposed to be existed.

    5. the delete function is to delete the object from the DB, but to keep a trace, it is preferable
    to just change the state of the object.

    March 11, 2022
    David PIERRE
*/

namespace App\Controller;

use App\Entity\FactorType;
use App\Form\FactorCreateType;
use App\Form\FactorUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\FactorTypeRepository;
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
 * @Route("factor/type", name="factor_type_")
 */
class FactorTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(FactorTypeRepository $factorTypeRepo): Response
    {
        $factorTypes =  $factorTypeRepo->findAll();
        $context = [
            'title' => 'FactorType List',
            'factorTypes' => $factorTypes
        ];
        return $this->render('factor_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $factorType = new FactorType();
        $form = $this->createForm(FactorCreateType::class, $factorType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $factorType->setCreatedBy($this->getUser());
            }
            $factorType->setIsActive(true);
            $factorType->setCreatedAt(new \DateTime());
            $entmanager->persist($factorType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('factor_type_index'));
        }

        $context = [
            'title' => 'Factor Creation',
            'factorTypeForm' => $form->createView()
        ];
        return $this->render('factor_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(FactorType $factorTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'FactorType Details',
            'factorType' => $factorTypeSelected
        ];
        return $this->render('factor_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(FactorType $factorType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('factor_type_edit', $factorType);
        $form = $this->createForm(FactorUpdateType::class, $factorType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($factorType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('factor_type_index'));
        }

        $context = [
            'title' => 'FactorType Update',
            'factorTypeForm' => $form->createView()
        ];
        return $this->render('factor_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(FactorType $factorType, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($factorType->getId()) {
            $factorType->setIsActive(!$factorType->getIsActive());
        }
        $entmanager->persist($factorType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $factorType->getIsActive()
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
            $repoFactorType = $entmanager->getRepository(FactorType::class);
            // Query how many rows are there in the FactorType table
            $totalFactorTypeBefore = $repoFactorType->createQueryBuilder('tab')
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
                $name = $row['A'];
                $description = $row['B'];
                $ontology_id = $row['C'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($name != null && $description != null && $ontology_id != null && $parentTerm != null) {
                    // check if the data is upload in the database
                    $existingFactorType = $entmanager->getRepository(FactorType::class)->findOneBy(['name' => $name]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingFactorType) {
                        $factorType = new FactorType();
                        if ($this->getUser()) {
                            $factorType->setCreatedBy($this->getUser());
                        }
                        $factorType->setName($name);
                        $factorType->setDescription($description);
                        $factorType->setOntologyId($ontology_id);
                        $factorType->setDescription($parentTerm);
                        $factorType->setIsActive(true);
                        $factorType->setCreatedAt(new \DateTime());
                        $entmanager->persist($factorType);
                    }
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the Country table
            $totalFactorTypeAfter = $repoFactorType->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalFactorTypeBefore == 0) {
                $this->addFlash('success', $totalFactorTypeAfter . " factor types have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalFactorTypeAfter - $totalFactorTypeBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new factor type has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " factor type has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " factor types have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('factor_type_index'));
        }

        $context = [
            'title' => 'Factor Type Upload From Excel',
            'factorTypeUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('factor_type/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function factorTypeTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/factor_type_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'factor_type_template_example.xls');
        return $response;
       
    }
}
