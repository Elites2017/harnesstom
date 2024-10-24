<?php

namespace App\Controller;

use App\Form\DownloadDataType;
use App\Repository\GenotypingPlatformRepository;
use App\Repository\MarkerRepository;
use App\Repository\VariantSetMetadataRepository;
use App\Repository\VariantSetRepository;
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
 * @Route("/download/genotyping", name="download_genotypic_data_")
 */
class DownloadGenotypicDataController extends AbstractController
{
    private $markerRepo;
    private $genoPlatformRepo;
    private $variantSetRepo;
    private $variantSetMetadataRepo;

    public function __construct(MarkerRepository $markerRepo, GenotypingPlatformRepository $genoPlatformRepo,
                                VariantSetRepository $variantSetRepo, VariantSetMetadataRepository $variantSetMetadataRepo) {
        $this->markerRepo = $markerRepo;
        $this->genoPlatformRepo = $genoPlatformRepo;
        $this->variantSetRepo = $variantSetRepo;
        $this->variantSetMetadataRepo = $variantSetMetadataRepo;
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
        // get the markers
        $markers = $this->markerRepo->findAll();
        // create a new document
        $spreadsheet = new Spreadsheet();
        
        // Get active sheet - it is also possible to retrieve a specific sheet
        $markersSheet = $spreadsheet->getActiveSheet();
        $markersSheet->setTitle("Markers");

        // Set column names for 
        $markersColumnNames = [
            'Name',
            'Type',
            'Linkage Group Name',
            'Position',
            'Start',
            'End',
            'Reference Allele',
            'Alternative Allele',
            'Primer Name 1',
            'Primer Seq 1',
            'Primer Name 2',
            'Primer Seq 2',
            'Genotyping Platform Name',
            //'Synonym',
        ];

        // Allow extra columns call
        $this->allowExtraColumn($markersColumnNames, $markersSheet);

        // for collection to array
        $colToArr = [];
        // Add data for each column
        $markersColValues = [];
        foreach ($markers as $key => $one) {
            # code...
            // for the collections to array
            foreach ($one->getMarkerSynonyms() as $oneSynonym) {
                # code...
                $colToArr [] = $oneSynonym->getMarkerName();
            }
            $markersColValues [] = [
                                $one->getName(),
                                $one->getType(),
                                $one->getLinkageGroupName(),
                                $one->getPosition(),
                                $one->getStart(),
                                $one->getEnd(),
                                $one->getRefAllele(),
                                $one->getAltAllele() ? implode('; ', $one->getAltAllele()) : '',
                                $one->getPrimerName1(),
                                $one->getPrimerSeq1(),
                                $one->getPrimerName2(),
                                $one->getPrimerSeq2(),
                                $one->getGenotypingPlatform() ? $one->getGenotypingPlatform()->getName() : '',
                                $colToArr ? implode('; ', $colToArr) : '',
                            ];
            $colToArr = []; 
        }
        
        // cell filling marker sheet call
        $this->cellFilling($markersColValues, $markersSheet);

        
        // get the genoPlatform
        $genoPlatform = $this->genoPlatformRepo->findAll();
        // sheet 2
        $genoPlatformSheet = $spreadsheet->createSheet(1)->setTitle("Genotyping Platforms");

        // Set column names for 
        $genoPlatformColumnNames = [
            'Method Class Name',
            'Method Class Description',
            'Genotyping Platform Name',
            'Genotyping Platform Description',
            'Ref Set Name',
            'Reference Set Published Date',
            'Marker Count',
            'Sequencing Instrument',
            'Var Call Software',
            'Assembly PUI',
            'Bio Project ID',
            'Publication Reference',
        ];

        // Allow extra columns call
        $this->allowExtraColumn($genoPlatformColumnNames, $genoPlatformSheet);

        // Add data for each column
        $genoPlatformColValues = [];
        foreach ($genoPlatform as $key => $one) {
            # code...
            $genoPlatformColValues [] = [
                $one->getSequencingType() ? $one->getSequencingType()->getName() : '',
                $one->getMethodDescription(),
                $one->getName(),
                $one->getDescription(),
                $one->getReferenceSetName(),
                $one->getPublishedDate(),
                $one->getMarkerCount(),
                $one->getSequencingInstrument() ? $one->getSequencingInstrument()->getName() : '',
                $one->getVarCallSoftware() ? $one->getVarCallSoftware()->getName() : '',
                $one->getAssemblyPUI(),
                $one->getBioProjectID(),
                $one->getPublicationRef() ? implode('; ', $one->getPublicationRef()) : '',
            ]; 
        }

        // cell filling genoPlatform sheet call
        $this->cellFilling($genoPlatformColValues, $genoPlatformSheet);


        // get the variantSets
        $variantSets = $this->variantSetRepo->getPublicReleasedData();
        // sheet 3
        $variantSetsSheet = $spreadsheet->createSheet(2)->setTitle("Variant Sets");

        // Set column names for 
        $variantSetsColumnNames = [
            'Sample Name',
            'Marker Name',
            'Value',
            'Variant Set Metadata'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($variantSetsColumnNames, $variantSetsSheet);

        // Add data for each column
        $variantSetsColValues = [];
        foreach ($variantSets as $key => $one) {
            # code...
            $variantSetsColValues [] = [
                $one->getSample() ? $one->getSample()->getName() : '',
                $one->getMarker() ? $one->getMarker()->getName() : '',
                $one->getValue(),
                $one->getVariantSetMetadata() ? $one->getVariantSetMetadata()->getName() : ''
            ]; 
        }
        
        // cell filling variantSets sheet call
        $this->cellFilling($variantSetsColValues, $variantSetsSheet);


        // get the variant set metadata
        $variantSetMetadata = $this->variantSetMetadataRepo->findAll();
        // sheet 4
        $variantSetMetadataSheet = $spreadsheet->createSheet(3)->setTitle("Variant Set Metadata");

        // Set column names for 
        $variantSetMetadataColumnNames = [
            'Name',
            'Description',
            'Filters',
            'Vriant Count',
            'Genotyping Platform Name',
            'Data Upload / File Name'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($variantSetMetadataColumnNames, $variantSetMetadataSheet);

        // Add data for each column
        $variantSetMetadataColValues = [];
        foreach ($variantSetMetadata as $key => $one) {
            # code...
            $variantSetMetadataColValues [] = [
                $one->getName(),
                $one->getDescription(),
                $one->getFilters(),
                $one->getVariantCount(),
                $one->getGenotypingPlatform() ? $one->getGenotypingPlatform()->getName() : '',
                $one->getDataUpload()
            ]; 
        }
        
        // cell filling variant set metadata sheet call
        $this->cellFilling($variantSetMetadataColValues, $variantSetMetadataSheet);

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
            $filename = 'HarnesstomDB Genotypic Data.'.$format;

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
            'title' => 'Genotyping Download',
            'form' => $form->createView()
        ];

        return $this->render('download_genotypic_data/index.html.twig', $context);
    }
    
}
