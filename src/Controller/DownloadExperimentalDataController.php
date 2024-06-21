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
    private $sampleRepo;
    private $observationLevelRepo;

    public function __construct(ProgramRepository $progRepo, TrialRepository $trialRepo,
                                StudyRepository $studyRepo, SampleRepository $sampleRepo,
                                ObservationLevelRepository $observationLevelRepo) {
        $this->progRepo = $progRepo;
        $this->trialRepo = $trialRepo;
        $this->studyRepo = $studyRepo;
        $this->sampleRepo = $sampleRepo;
        $this->observationLevelRepo = $observationLevelRepo;
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
            'Program Associated Abbreviation'
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


        // get the studies
        $studies = $this->studyRepo->findAll();
        // sheet 3
        $studiesSheet = $spreadsheet->createSheet(2)->setTitle("Studies");

        // Set column names for 
        $studiesColumnNames = [
            'Study Name',
            'Study Abbreviation',
            'Study Description',
            'Study Start Date',
            'Study End Date',
            'Study Cultural Practice',
            'Study Last Updated',
            'Trial Associated Abbreviation',
            'Factor Associated Name',
            'Season Associated Name',
            'Institute Associated Name',
            'Location Associated Name',
            'Growth Facility Associated Name',
            'Experimental Design Type Associated Name'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($studiesColumnNames, $studiesSheet);

        // Add data for each column
        $studiesColValues = [];
        foreach ($studies as $key => $oneStudy) {
            # code...
            $studiesColValues [] = [
                $oneStudy->getName(),
                $oneStudy->getAbbreviation(),
                $oneStudy->getDescription(),
                $oneStudy->getStartDate(),
                $oneStudy->getEndDate(),
                $oneStudy->getCulturalPractice(),
                $oneStudy->getLastUpdated(),
                $oneStudy->getTrial() ? $oneStudy->getTrial()->getAbbreviation() : '',
                $oneStudy->getFactor() ? $oneStudy->getFactor()->getName() : '',
                $oneStudy->getSeason() ? $oneStudy->getSeason()->getName() : '',
                $oneStudy->getInstitute() ? $oneStudy->getInstitute()->getName() : '',
                $oneStudy->getLocation() ? $oneStudy->getLocation()->getName() : '',
                $oneStudy->getGrowthFacility() ? $oneStudy->getGrowthFacility()->getName() : '',
                $oneStudy->getExperimentalDesignType() ? $oneStudy->getExperimentalDesignType()->getName() : '',
            ]; 
        }
        
        // cell filling program sheet call
        $this->cellFilling($studiesColValues, $studiesSheet);


        // get the samples
        $samples = $this->sampleRepo->findAll();
        // sheet 4
        $samplesSheet = $spreadsheet->createSheet(3)->setTitle("Samples");

        // Set column names for 
        $samplesColumnNames = [
            'Sample Name',
            'Sample Replicate',
            'Sample Description',
            'Sample Last Updated',
            'Study Associated Abbreviation',
            'Germplasm Associated Name',
            'Developmental Stage Associated Name',
            'Anatomical Entity Associated Name',
            'Observation Level Associated Name'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($samplesColumnNames, $samplesSheet);

        // Add data for each column
        $samplesColValues = [];
        foreach ($samples as $key => $oneSample) {
            # code...
            $samplesColValues [] = [
                $oneSample->getName(),
                $oneSample->getReplicate(),
                $oneSample->getDescription(),
                $oneSample->getLastUpdated(),
                $oneSample->getStudy() ? $oneSample->getStudy()->getAbbreviation() : '',
                $oneSample->getGermplasm() ? $oneSample->getGermplasm()->getGermplasmName() : '',
                $oneSample->getDevelopmentalStage() ? $oneSample->getDevelopmentalStage()->getName() : '',
                $oneSample->getAnatomicalEntity() ? $oneSample->getAnatomicalEntity()->getName() : '',
                $oneSample->getObservationLevel() ? $oneSample->getObservationLevel()->getName() : ''
            ]; 
        }
        
        // cell filling sample sheet call
        $this->cellFilling($samplesColValues, $samplesSheet);

        
        // get the observation levels
        $observationLevels = $this->observationLevelRepo->findAll();
        // sheet 5
        $observationLevelsSheet = $spreadsheet->createSheet(4)->setTitle("Observation Levels");

        // Set column names for 
        $observationLevelsColumnNames = [
            'Observation Level Unitname',
            'Observation Level Name',
            'Observation Level Block Number',
            'Observation Level Sub Block Number',
            'Observation Level Plot Number',
            'Observation Level Plant Number',
            'Observation Level Replicate',
            'Observation Level Unit Position',
            'Observation Level Coordinate X',
            'Observation Level Coordinate Y',
            'Observation Level Coordinate X Type',
            'Observation Level Coordinate Y Type',
            'Observation Level Last Updated',
            'Germplasm Associated Name',
            'Study Associated Abbreviation'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($observationLevelsColumnNames, $observationLevelsSheet);

        // Add data for each column
        $observationLevelsColValues = [];
        foreach ($observationLevels as $key => $oneSample) {
            # code...
            $observationLevelsColValues [] = [
                $oneSample->getUnitname(),
                $oneSample->getName(),
                $oneSample->getBlockNumber(),
                $oneSample->getSubBlockNumber(),
                $oneSample->getPlotNumber(),
                $oneSample->getPlantNumber(),
                $oneSample->getReplicate(),
                $oneSample->getUnitPosition(),
                $oneSample->getUnitCoordinateX(),
                $oneSample->getUnitCoordinateY(),
                $oneSample->getUnitCoordinateXType(),
                $oneSample->getUnitCoordinateYType(),
                $oneSample->getLastUpdated(),
                $oneSample->getStudy() ? $oneSample->getStudy()->getAbbreviation() : '',
                $oneSample->getGermplasm() ? $oneSample->getGermplasm()->getGermplasmName() : ''
            ]; 
        }
        
        // cell filling sample sheet call
        $this->cellFilling($observationLevelsColValues, $observationLevelsSheet);

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
