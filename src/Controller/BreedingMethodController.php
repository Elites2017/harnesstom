<?php

namespace App\Controller;

use App\Entity\BreedingMethod;
use App\Form\BreedingMethodType;
use App\Form\BreedingMethodUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\BreedingMethodRepository;
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
 * @Route("breeding/method", name="breeding_method_")
 */
class BreedingMethodController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(BreedingMethodRepository $breedingMethodRepo): Response
    {
        $breedingMethods =  $breedingMethodRepo->findAll();
        $parentsOnly = $breedingMethodRepo->getParentsOnly();
        $context = [
            'title' => 'Breeding Method List',
            'breedingMethods' => $breedingMethods,
            'parentsOnly' => $$parentsOnly
        ];
        return $this->render('breeding_method/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $breedingMethod = new BreedingMethod();
        $form = $this->createForm(BreedingMethodType::class, $breedingMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $breedingMethod->setCreatedBy($this->getUser());
            }
            $breedingMethod->setIsActive(true);
            $breedingMethod->setCreatedAt(new \DateTime());
            $entmanager->persist($breedingMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('breeding_method_index'));
        }

        $context = [
            'title' => 'Breeding Method Creation',
            'breedingMethodForm' => $form->createView()
        ];
        return $this->render('breeding_method/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(BreedingMethod $breedingMethodSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Breeding Method Details',
            'breedingMethod' => $breedingMethodSelected
        ];
        return $this->render('breeding_method/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(BreedingMethod $breedingMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('breeding_method_edit', $breedingMethod);
        $form = $this->createForm(BreedingMethodUpdateType::class, $breedingMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($breedingMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('breeding_method_index'));
        }

        $context = [
            'title' => 'Breeding Method Update',
            'breedingMethodForm' => $form->createView()
        ];
        return $this->render('breeding_method/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(BreedingMethod $breedingMethod, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($breedingMethod->getId()) {
            $breedingMethod->setIsActive(!$breedingMethod->getIsActive());
        }
        $entmanager->persist($breedingMethod);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $breedingMethod->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('breedingMethod_home'));
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
            $repoBreedingMethod = $entmanager->getRepository(BreedingMethod::class);
            // Query how many rows are there in the BreedingMethod table
            $totalBreedingMethodBefore = $repoBreedingMethod->createQueryBuilder('tab')
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
                    $existingBreedingMethod = $entmanager->getRepository(BreedingMethod::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingBreedingMethod) {
                        $breedingMethod = new BreedingMethod();
                        if ($this->getUser()) {
                            $breedingMethod->setCreatedBy($this->getUser());
                        }
                        $breedingMethod->setOntologyId($ontology_id);
                        $breedingMethod->setName($name);
                        if ($description != null) {
                            $breedingMethod->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $breedingMethod->setParOnt($parentTermString);
                        }
                        $breedingMethod->setIsActive(true);
                        $breedingMethod->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($breedingMethod);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(BreedingMethod::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\BreedingMethod)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(BreedingMethod::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE breeding_method SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalBreedingMethodAfter = $repoBreedingMethod->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalBreedingMethodBefore == 0) {
                $this->addFlash('success', $totalBreedingMethodAfter . " breeding methods have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalBreedingMethodAfter - $totalBreedingMethodBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new breeding method has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " breeding method has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " breeding methods have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('breeding_method_index'));
        }

        $context = [
            'title' => 'Breeding Method Upload From Excel',
            'breedingMethodUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('breeding_method/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/breeding_method_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'breeding_method_template_example.xls');
        return $response;
       
    }
}