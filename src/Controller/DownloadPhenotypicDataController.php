<?php

namespace App\Controller;

use App\Form\DownloadDataType;
use App\Repository\AnalyteRepository;
use App\Repository\MetaboliteRepository;
use App\Repository\ObservationValueOriginalRepository;
use App\Repository\ObservationVariableMethodRepository;
use App\Repository\ObservationVariableRepository;
use App\Repository\TraitClassRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;


// set a class level route
/**
 * @Route("/download/phenotyping", name="download_phenotypic_data_")
 */
class DownloadPhenotypicDataController extends AbstractController
{
    private $metaboliteRepo;
    private $analyteRepo;
    private $traitRepo;
    private $obsValOriginalRepo;
    private $obsVarMethodRepo;
    private $obsVarRepo;

    public function __construct(MetaboliteRepository $metaboliteRepo, AnalyteRepository $analyteRepo,
                                TraitClassRepository $traitRepo, ObservationValueOriginalRepository $obsValOriginalRepo,
                                ObservationVariableMethodRepository $obsVarMethodRepo, ObservationVariableRepository $obsVarRepo) {
        $this->metaboliteRepo = $metaboliteRepo;
        $this->analyteRepo = $analyteRepo;
        $this->traitRepo = $traitRepo;
        $this->obsValOriginalRepo = $obsValOriginalRepo;
        $this->obsVarMethodRepo = $obsVarMethodRepo;
        $this->obsVarRepo = $obsVarRepo;
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
        // get the metabolites
        $metabolites = $this->metaboliteRepo->findAll();
        // create a new document
        $spreadsheet = new Spreadsheet();
        
        // Get active sheet - it is also possible to retrieve a specific sheet
        $metabolitesSheet = $spreadsheet->getActiveSheet();
        $metabolitesSheet->setTitle("Metabolites");

        // Set column names for 
        $metabolitesColumnNames = [
            'Analyte Name',
            'Metabolic Trait',
            'Scale',
        ];

        // Allow extra columns call
        $this->allowExtraColumn($metabolitesColumnNames, $metabolitesSheet);

        // Add data for each column
        $metabolitesColValues = [];
        foreach ($metabolites as $key => $one) {
            # code...
            $metabolitesColValues [] = [
                                $one->getAnalyte(),
                                $one->getMetabolicTrait(),
                                $one->getScale()
                            ]; 
        }
        
        // cell filling program sheet call
        $this->cellFilling($metabolitesColValues, $metabolitesSheet);

        
        // get the analytes
        $analytes = $this->analyteRepo->findAll();
        // sheet 2
        $analytesSheet = $spreadsheet->createSheet(1)->setTitle("Analytes");

        // Set column names for 
        $analytesColumnNames = [
            'Analyte Name',
            'Analyte Code',
            'Retention Time',
            'Mass To Charge Ratio',
            'Annotation Level',
            'Identification Level',
            'Observation Variable Method',
            'Metabolite class',
            'Health & Flavor'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($analytesColumnNames, $analytesSheet);

        // Add data for each column
        $analytesColValues = [];
        foreach ($analytes as $key => $one) {
            # code...
            $analytesColValues [] = [
                $one->getName(),
                $one->getAnalyteCode(),
                $one->getRetentionTime(),
                $one->getMassToChargeRatio(),
                $one->getAnnotationLevel() ? $one->getAnnotationLevel()->getName() : '',
                $one->getIdentificationLevel() ? $one->getIdentificationLevel()->getName() : '',
                $one->getObservationVariableMethod() ? $one->getObservationVariableMethod()->getName() : '',
                $one->getMetaboliteClass() ? $one->getMetaboliteClass()->getOntologyId() : '',
                $one->getHealthAndFlavor() ? $one->getHealthAndFlavor()->getOntologyId() : ''
            ]; 
        }

        // cell filling analytes sheet call
        $this->cellFilling($analytesColValues, $analytesSheet);


        // get the traits
        $traits = $this->traitRepo->findAll();
        // sheet 3
        $traitsSheet = $spreadsheet->createSheet(2)->setTitle("Traits");

        // Set column names for 
        $traitsColumnNames = [
            'Ontology ID',
            'Trait Name',
            'Parent Term Ontology ID',
            'Variable Of'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($traitsColumnNames, $traitsSheet);

        // Add data for each column
        $traitsColValues = [];
        foreach ($traits as $key => $one) {
            # code...
            $traitsColValues [] = [
                $one->getOntologyId(),
                $one->getName(),
                $one->getParentTerm()[0] ? $one->getParentTerm()[0]->getOntologyId() : '',
                $one->getVarOf()[0] ? $one->getVarOf()[0]->getOntologyId() : ''
            ]; 
        }
        
        // cell filling traits sheet call
        $this->cellFilling($traitsColValues, $traitsSheet);


        // get the observation values
        $observationValues = $this->obsValOriginalRepo->getPublicReleasedData();
        // sheet 4
        $observationValuesSheet = $spreadsheet->createSheet(3)->setTitle("Observation Values");

        // Set column names for 
        $observationValuesColumnNames = [
            'Observation Variable Name',
            'Observation Level Name',
            'Value'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($observationValuesColumnNames, $observationValuesSheet);

        // Add data for each column
        $observationValuesColValues = [];
        foreach ($observationValues as $key => $one) {
            # code...
            $observationValuesColValues [] = [
                $one->getObservationVariableOriginal() ? $one->getObservationVariableOriginal()->getName() : '',
                $one->getUnitName() ? $one->getUnitName()->getUnitname() : '',
                $one->getValue()
            ]; 
        }
        
        // cell filling observation values original (3 cols) sheet call
        $this->cellFilling($observationValuesColValues, $observationValuesSheet);

        
        // get the observation variable methods
        $obsVarMethods = $this->obsVarMethodRepo->findAll();
        // sheet 5
        $obsVarMethodsSheet = $spreadsheet->createSheet(4)->setTitle("Observation Variable Method");

        // Set column names for 
        $obsVarMethodsColumnNames = [
            'Observation Variable Method Name',
            'Observation Variable Method Description',
            'Instrument',
            'Software',
            'Method Class',
            'Publication Reference'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($obsVarMethodsColumnNames, $obsVarMethodsSheet);

        // Add data for each column
        $obsVarMethodsColValues = [];
        foreach ($obsVarMethods as $key => $one) {
            # code...
            $obsVarMethodsColValues [] = [
                $one->getName(),
                $one->getDescription(),
                $one->getInstrument(),
                $one->getSoftware(),
                $one->getMethodClass() ? $one->getMethodClass()->getOntologyId() : '',
                $one->getPublicationReference() ? implode('; ', $one->getPublicationReference()) : '',
            ]; 
        }
        
        // cell filling sample sheet call
        $this->cellFilling($obsVarMethodsColValues, $obsVarMethodsSheet);




        // get the observation variable 
        $obsVariables = $this->obsVarRepo->findAll();
        // sheet 6
        $obsVariablesSheet = $spreadsheet->createSheet(5)->setTitle("Observation Variable");

        // Set column names for 
        $obsVariablesColumnNames = [
            'Observation Variable Name',
            'Observation Main Abbreviation',
            'Observation Variable Description',
            'Trait',
            'Scale',
            'Observation Variable Name Method Class'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($obsVariablesColumnNames, $obsVariablesSheet);

        // Add data for each column
        $obsVariablesColValues = [];
        foreach ($obsVariables as $key => $one) {
            # code...
            $obsVariablesColValues [] = [
                $one->getName(),
                $one->getMainAbbreviaition(),
                $one->getDescription(),
                $one->getTrait() ? $one->getTrait()->getOntologyId() : '',
                $one->getScale() ? $one->getScale()->getName() : '',
                $one->getObservationVariableMethod() ? $one->getObservationVariableMethod()->getName() : ''
            ]; 
        }
        
        // cell filling sample sheet call
        $this->cellFilling($obsVariablesColValues, $obsVariablesSheet);

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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(DownloadDataType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $format = $data['format'];
            $filename = 'HarnesstomDB Phenotypic Data.'.$format;

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

        $context = [
            'title' => 'Phenotyping Download',
            'form' => $form->createView()
        ];

        return $this->render('download_phenotypic_data/index.html.twig', $context);
    }
    
}
