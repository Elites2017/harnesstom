<?php

namespace App\Controller;

use App\Form\DownloadDataType;
use App\Repository\ObservationLevelRepository;
use App\Repository\ProgramRepository;
use App\Repository\SampleRepository;
use App\Repository\StudyRepository;
use App\Repository\TrialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;


// set a class level route
/**
 * @Route("/download/data", name="download_data_")
 */
class DownloadExperimentalDataController extends AbstractController
{
    private $progRepo;

    public function __construct(ProgramRepository $progRepo) {
        $this->progRepo = $progRepo;
    }

    protected function createSpreadsheet()
    {
        // get the programs
        $programs = $this->progRepo->findAll();
        // create a new document
        $spreadsheet = new Spreadsheet();
        
        // Get active sheet - it is also possible to retrieve a specific sheet
        $programsSheet = $spreadsheet->getActiveSheet();
        $programsSheet->setTitle("Programs");

        // Set cell name and merge cells
        //$programsSheet->setCellValue('A1', 'Browser characteristics')->mergeCells('A1:D1');

        // Set column names for 
        $columnNames = [
            'Program DBID',
            'Program Name',
            'Program Abbreviation',
            'Program Objective',
            'Program External Reference',
        ];

        $columnLetter = 'A';
        foreach ($columnNames as $columnName) {
            // Allow to access AA column if needed and more
            $programsSheet->setCellValue($columnLetter.'1', $columnName);
            $columnLetter++;
        }

        // Add data for each column
        $progColValues = [];
        foreach ($programs as $key => $oneProg) {
            # code...
            $progColValues [] = [$oneProg->getId(), $oneProg->getName(), $oneProg->getAbbreviation(), $oneProg->getObjective(), $oneProg->getExternalRef()]; 
        }
        
        $i = 2; // Beginning row for active sheet
        foreach ($progColValues as $columnValue) {
            $columnLetter = 'A';
            foreach ($columnValue as $value) {
                $programsSheet->setCellValue($columnLetter.$i, $value);
                $columnLetter++;
            }
            $i++;
        }

        // sheet 1
        $trialsSheet = $spreadsheet->createSheet(1)->setTitle("Trials");

        // Set cell name and merge cells
        //$trialsSheet->setCellValue('A1', 'Browser characteristics')->mergeCells('A1:D1');

        // Set column names for 
        $columnNames = [
            'Program DBID',
            'Program Name',
            'Program Abbreviation',
            'Program Objective',
            'Program External Reference',
        ];

        $columnLetter = 'A';
        foreach ($columnNames as $columnName) {
            // Allow to access AA column if needed and more
            $trialsSheet->setCellValue($columnLetter.'2', $columnName);
            $columnLetter++;
        }

        // Add data for each column
        $progColValues = [];
        foreach ($programs as $key => $oneProg) {
            # code...
            $progColValues [] = [$oneProg->getId(), $oneProg->getName(), $oneProg->getAbbreviation(), $oneProg->getObjective(), $oneProg->getExternalRef()]; 
        }
        
        $i = 3; // Beginning row for active sheet
        foreach ($progColValues as $columnValue) {
            $columnLetter = 'A';
            foreach ($columnValue as $value) {
                $trialsSheet->setCellValue($columnLetter.$i, $value);
                $columnLetter++;
            }
            $i++;
        }

        return $spreadsheet;
    }

    protected function formatCase($format, $spreadsheet, $form) {
        switch ($format) {
            case 'ods':
                $contentType = 'application/vnd.oasis.opendocument.spreadsheet';
                $writer = new Ods($spreadsheet);
                break;
            case 'xlsx':
                $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                $writer = new Xlsx($spreadsheet);
                break;
            case 'csv':
                $contentType = 'text/csv';
                $writer = new Csv($spreadsheet);
                break;
            default:
                return $this->render('AppBundle::export.html.twig', [
                    'form' => $form->createView(),
                ]);
        }
    }

    /**
     * @Route("/", name="index")
     */
    public function exportAction(Request $request)
    {
        $form = $this->createForm(DownloadDataType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $format = $data['format'];
            $filename = 'HarnesstomDB Experimental Data.'.$format;

            $spreadsheet = $this->createSpreadsheet();

            switch ($format) {
                case 'ods':
                    $contentType = 'application/vnd.oasis.opendocument.spreadsheet';
                    $writer = new Ods($spreadsheet);
                    break;
                case 'xlsx':
                    $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    $writer = new Xlsx($spreadsheet);
                    break;
                case 'csv':
                    $contentType = 'text/csv';
                    $writer = new Csv($spreadsheet);
                    break;
                default:
                    return $this->render('AppBundle::export.html.twig', [
                        'form' => $form->createView(),
                    ]);
            }

            $response = new StreamedResponse();
            $response->headers->set('Content-Type', $contentType);
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename.'"');
            $response->setPrivate();
            $response->headers->addCacheControlDirective('no-cache', true);
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setCallback(function() use ($writer) {
                $writer->save('php://output');
            });

            return $response;
        }

        return $this->render('download_experimental_data/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}
