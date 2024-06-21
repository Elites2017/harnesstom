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
    private $trialRepo;
    private $studyRepo;

    public function __construct(ProgramRepository $progRepo, TrialRepository $trialRepo, StudyRepository $studyRepo) {
        $this->progRepo = $progRepo;
        $this->trialRepo = $trialRepo;
        $this->studyRepo = $studyRepo;
    }

    // to fill the cell
    public function cellFilling($objColValues, $objectSheet) {
        $i = 2; // Beginning row for active sheet
        foreach ($objColValues as $columnValue) {
            $columnLetter = 'A';
            foreach ($columnValue as $value) {
                $objectSheet->setCellValue($columnLetter.$i, $value);
                $columnLetter++;
            }
            $i++;
        }
    }

    // to allow access to extra columns like AA column in needed
    public function allowExtraColumn($objectColumnNames, $objectSheet) {
        $columnLetter = 'A';
        foreach ($objectColumnNames as $columnName) {
            // Allow to access AA column if needed and more
            $objectSheet->setCellValue($columnLetter.'1', $columnName);
            $columnLetter++;
        }
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
        $progColumnNames = [
            'Program Name',
            'Program Abbreviation',
            'Program Objective',
            'Program External Reference',
        ];

        // Allow extra columns call
        $this->allowExtraColumn($progColumnNames, $programsSheet);

        // Add data for each column
        $progColValues = [];
        foreach ($programs as $key => $oneProg) {
            # code...
            $progColValues [] = [$oneProg->getName(), $oneProg->getAbbreviation(), $oneProg->getObjective(), $oneProg->getExternalRef()]; 
        }
        
        // cell filling program sheet call
        $this->cellFilling($progColValues, $programsSheet);

        
        // get the trials
        $trials = $this->trialRepo->findAll();
        // sheet 2
        $trialsSheet = $spreadsheet->createSheet(1)->setTitle("Trials");

        // Set column names for 
        $trialsColumnNames = [
            'Trial Name',
            'Trial Description',
            'Trial Abbreviation',
            'Trial Start Date',
            'Trial End Date',
            'Trial Public Release Date',
            'Trial License',
            'Trial PUI',
            'Trial Publication Reference',
            'Program Associated Name'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($trialsColumnNames, $trialsSheet);

        // Add data for each column
        $trialsColValues = [];
        foreach ($trials as $key => $oneTrial) {
            # code...
            $trialsColValues [] = [
                $oneTrial->getName(),
                $oneTrial->getDescription(),
                $oneTrial->getAbbreviation(),
                $oneTrial->getStartDate(),
                $oneTrial->getEndDate(),
                $oneTrial->getPublicReleaseDate(),
                $oneTrial->getLicense(),
                $oneTrial->getPui(),
                $oneTrial->getPublicationReference() ? $oneTrial->getPublicationReference()[0] : '',
                $oneTrial->getProgram() ? $oneTrial->getProgram()->getAbbreviation() : ''
            ]; 
        }

        // cell filling trials sheet call
        $this->cellFilling($trialsColValues, $trialsSheet);


        

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
