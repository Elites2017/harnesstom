<?php

namespace App\Controller;

use App\Entity\GenotypingPlatform;
use App\Entity\Marker;
use App\Form\MarkerType;
use App\Form\MarkerUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\MarkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/marker", name="marker_")
 */
class MarkerController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MarkerRepository $markerRepo): Response
    {
        $markers =  $markerRepo->findAll();
        $context = [
            'title' => 'Marker List',
            'markers' => $markers,
        ];
        return $this->render('marker/index.html.twig', $context);
    }

    /**
     * @Route("/datatable", name="datatable")
     */
    public function datatable(MarkerRepository $markerRepo, Request $request)
    {
        // Get the parameters from DataTable Ajax Call
        if ($request->getMethod() == 'POST')
        {
            $draw = intval($request->request->get('draw'));
            // $name = $request->request->get('name');
            // $type = $request->request->get('type');
            // $linkageGroupName = $request->request->get('linkageGroupName');
            // $position = $request->request->get('position');
            // $start = $request->request->get('start');
            // $end = $request->request->get('end');
            // $refAllele = $request->request->get('refAllele');
            // $platformNameBuffer = $request->request->get('platformNameBuffer');

            //https://growingcookies.com/datatables-server-side-processing-in-symfony/

            $start = $request->request->get('start');
            $length = $request->request->get('length');
            $search = $request->request->get('search');
            $orders = $request->request->get('order');
            $columns = $request->request->get('columns');
            //dd($columns);
        }

        foreach ($orders as $key => $order)
        {
            // Orders does not contain the name of the column, but its number,
            // so add the name so we can handle it just like the $columns array
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }

        // Further filtering can be done in the Repository by passing necessary arguments
        $otherConditions = "array or whatever is needed";

        // Get results from the Repository
        $results = $markerRepo->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);

        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $markerRepo->count();
        // Get total number of results
        $selected_objects_count = count($objects);
        // Get total number of filtered data
        $filtered_objects_count = $results["countResult"];
        
        // Construct response
        $response = '{
            "draw": '.$draw.',
            "recordsTotal": '.$total_objects_count.',
            "recordsFiltered": '.$filtered_objects_count.',
            "data": [';
    
        $i = 0;

        foreach ($objects as $key => $marker)
        {
            $response .= '["';
    
            $j = 0; 
            $nbColumn = count($columns);
            foreach ($columns as $key => $column)
            {
            // In all cases where something does not exist or went wrong, return -
            $responseTemp = "-";
    
                switch($column['name'])
                {
                    case 'id':
                    {
                        // We know from the class definition that the postal code cannot be null
                        // But if that werent't the case, its value should have been tested
                        // before assigning it to $responseTemp
                        $responseTemp = $marker->getId();
                        break;
                    }

                    case 'name':
                    {
                        $name = $marker->getName();
    
                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $name = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $name));
    
                        // View permission ?
                        if ($this->get('security.authorization_checker')->isGranted('view_town', $marker))
                        {
                            // Get the ID
                            $id = $marker->getId();
                            // Construct the route
                            $url = $this->generateUrl('playground_town_view', array('id' => $id));
                            // Construct the html code to send back to datatables
                            $responseTemp = "<a href='".$url."' target='_self'>".$ref."</a>";
                        }
                        else
                        {
                            $responseTemp = $name;
                        }
                        break;
                    }
    
                    case 'type':
                    {
                        // We know from the class definition that the postal code cannot be null
                        // But if that werent't the case, its value should have been tested
                        // before assigning it to $responseTemp
                        $responseTemp = $marker->getType();
                        break;
                    }

                    case 'linkageGroupName':
                    {
                        // We know from the class definition that the postal code cannot be null
                        // But if that werent't the case, its value should have been tested
                        // before assigning it to $responseTemp
                        $responseTemp = $marker->getLinkageGroupName();
                        break;
                    }

                    case 'position':
                    {
                        // We know from the class definition that the postal code cannot be null
                        // But if that werent't the case, its value should have been tested
                        // before assigning it to $responseTemp
                        $responseTemp = $marker->getPosition();
                        break;
                    }

                    case 'start':
                    {
                        // We know from the class definition that the postal code cannot be null
                        // But if that werent't the case, its value should have been tested
                        // before assigning it to $responseTemp
                        $responseTemp = $marker->getStart();
                        break;
                    }

                    case 'end':
                    {
                        // We know from the class definition that the postal code cannot be null
                        // But if that werent't the case, its value should have been tested
                        // before assigning it to $responseTemp
                        $responseTemp = $marker->getEnd();
                        break;
                    }

                    case 'refAllele':
                    {
                        // We know from the class definition that the postal code cannot be null
                        // But if that werent't the case, its value should have been tested
                        // before assigning it to $responseTemp
                        $responseTemp = $marker->getRefAllele();
                        break;
                    }

                    case 'platformNameBuffer':
                    {
                        // We know from the class definition that the postal code cannot be null
                        // But if that werent't the case, its value should have been tested
                        // before assigning it to $responseTemp
                        $responseTemp = $marker->getPlatformNameBuffer();
                        break;
                    }
                }
    
                // Add the found data to the json
                $response .= $responseTemp;
    
                if(++$j !== $nbColumn)
                    $response .='","';
            }
    
            $response .= '"]';
    
            // Not on the last item
            if(++$i !== $selected_objects_count)
                $response .= ',';
        }
    
        $response .= ']}';
    
        // Send all this stuff back to DataTables
        $returnResponse = new JsonResponse();
        $returnResponse->setJson($response);
    
        return $returnResponse;
           
        //$markers =  $markerRepo->myMarker();
        //return new JsonResponse($markers);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $marker = new Marker();
        $form = $this->createForm(MarkerType::class, $marker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $marker->setCreatedBy($this->getUser());
            }
            $marker->setIsActive(true);
            $marker->setCreatedAt(new \DateTime());
            $entmanager->persist($marker);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('marker_index'));
        }

        $context = [
            'title' => 'Marker Creation',
            'markerForm' => $form->createView()
        ];
        return $this->render('marker/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Marker $markerSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Marker Details',
            'marker' => $markerSelected
        ];
        return $this->render('marker/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Marker $marker, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('marker_edit', $marker);
        $form = $this->createForm(MarkerUpdateType::class, $marker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($marker);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('marker_index'));
        }

        $context = [
            'title' => 'Marker Update',
            'markerForm' => $form->createView()
        ];
        return $this->render('marker/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Marker $marker, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($marker->getId()) {
            $marker->setIsActive(!$marker->getIsActive());
        }
        $entmanager->persist($marker);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $marker->getIsActive()
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
            $repoMarker = $entmanager->getRepository(Marker::class);
            // Query how many rows are there in the Marker table
            $totalMarkerBefore = $repoMarker->createQueryBuilder('tab')
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
                $genoPlatformName = $row['A'];
                $type = $row['B'];
                $markerName = $row['C'];
                $linkageGroupName = $row['D'];
                $position = $row['E'];
                $start = $row['F'];
                $end = $row['G'];
                $refAllele = $row['H'];
                $altAllele = $row['I'];
                $primerName1 = $row['J'];
                $primerSeq1 = $row['K'];
                $primerName2 = $row['L'];
                $primerSeq2 = $row['M'];
                // check if the file doesn't have empty columns
                if ($genoPlatformName != null & $markerName != null) {
                    // check if the data is upload in the database
                    $existingMarker = $entmanager->getRepository(Marker::class)->findOneBy(['name' => $markerName, 'platformNameBuffer' => $genoPlatformName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingMarker) {
                        $marker = new Marker();
                        $markerGenoPlatform = $entmanager->getRepository(GenotypingPlatform::class)->findOneBy(['name' => $genoPlatformName]);
                        if (($markerGenoPlatform != null) && ($markerGenoPlatform instanceof \App\Entity\GenotypingPlatform)) {
                            $marker->setGenotypingPlatform($markerGenoPlatform);
                            $marker->setPlatformNameBuffer($genoPlatformName);
                        }
                        
                        if ($this->getUser()) {
                            $marker->setCreatedBy($this->getUser());
                        }
                        
                        $altAllele = explode(",", $altAllele);

                        $marker->setType($type);
                        $marker->setLinkageGroupName($linkageGroupName);
                        $marker->setPosition($position);
                        $marker->setStart($start);
                        $marker->setName($markerName);
                        $marker->setEnd($end);
                        $marker->setRefAllele($refAllele);
                        $marker->setAltAllele($altAllele);
                        $marker->setPrimerName1($primerName1);
                        $marker->setPrimerSeq1($primerSeq1);
                        $marker->setPrimerName2($primerName2);
                        $marker->setPrimerSeq2($primerSeq2);
                        $marker->setIsActive(true);
                        $marker->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($marker);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalMarkerAfter = $repoMarker->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalMarkerBefore == 0) {
                $this->addFlash('success', $totalMarkerAfter . " Markers have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalMarkerAfter - $totalMarkerBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new Marker has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " Marker has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " Markers have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('marker_index'));
        }

        $context = [
            'title' => 'Marker Upload From Excel',
            'markerUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('marker/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/marker_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'marker_template_example.xlsx');
        return $response;
       
    }
}


