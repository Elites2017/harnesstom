<?php

namespace App\Controller;

use App\Entity\AttributeCategory;
use App\Form\AttributeCategoryType;
use App\Form\AttributeCategoryUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\AttributeCategoryRepository;
use Attribute;
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
 * @Route("attribute/category", name="attribute_category_")
 */
class AttributeCategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AttributeCategoryRepository $attributeCategoryRepo): Response
    {
        $attributeCategories =  $attributeCategoryRepo->findAll();
        $parentsOnly = $attributeCategoryRepo->getParentsOnly();
        $context = [
            'title' => 'Anatomical Entity List',
            'attributeCategories' => $attributeCategories,
            'parentsOnly' => $parentsOnly
        ];
        return $this->render('attribute_category/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $attributeCategory = new AttributeCategory();
        $form = $this->createForm(AttributeCategoryType::class, $attributeCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $attributeCategory->setCreatedBy($this->getUser());
            }
            $attributeCategory->setIsActive(true);
            $attributeCategory->setCreatedAt(new \DateTime());
            $entmanager->persist($attributeCategory);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('attribute_category_index'));
        }

        $context = [
            'title' => 'Attribute Category',
            'attributeCategoryForm' => $form->createView()
        ];
        return $this->render('attribute_category/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AttributeCategory $attributeCategorySelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Attribute Category',
            'attributeCategory' => $attributeCategorySelected
        ];
        return $this->render('attribute_category/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AttributeCategory $attributeCategory, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('attribute_category_edit', $attributeCategory);
        $form = $this->createForm(AttributeCategoryUpdateType::class, $attributeCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($attributeCategory);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('attribute_category_index'));
        }

        $context = [
            'title' => 'Attribute Category Update',
            'attributeCategoryForm' => $form->createView()
        ];
        return $this->render('attribute_category/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AttributeCategory $attributeCategory, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($attributeCategory->getId()) {
            $attributeCategory->setIsActive(!$attributeCategory->getIsActive());
        }
        $entmanager->persist($attributeCategory);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $attributeCategory->getIsActive()
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
            $repoAttributeCategory = $entmanager->getRepository(AttributeCategory::class);
            // Query how many rows are there in the AttributeCategory table
            $totalAttributeCategoryBefore = $repoAttributeCategory->createQueryBuilder('tab')
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
                    $existingAttributeCategory = $entmanager->getRepository(AttributeCategory::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingAttributeCategory) {
                        $attributeCategory = new AttributeCategory();
                        if ($this->getUser()) {
                            $attributeCategory->setCreatedBy($this->getUser());
                        }
                        $attributeCategory->setOntologyId($ontology_id);
                        $attributeCategory->setName($name);
                        if ($description != null) {
                            $attributeCategory->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $attributeCategory->setParOnt($parentTermString);
                        }
                        $attributeCategory->setIsActive(true);
                        $attributeCategory->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($attributeCategory);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(AttributeCategory::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\AttributeCategory)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(AttributeCategory::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE attribute_category SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalAttributeCategoryAfter = $repoAttributeCategory->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalAttributeCategoryBefore == 0) {
                $this->addFlash('success', $totalAttributeCategoryAfter . " attribute categories have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalAttributeCategoryAfter - $totalAttributeCategoryBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new attribute category has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " attribute category has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " attribute categories have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('attribute_category_index'));
        }

        $context = [
            'title' => 'Attribute Category Upload From Excel',
            'attributeCategoryUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('attribute_category/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/attribute_category_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'attribute_category_template_example.xls');
        return $response;
       
    }
}
