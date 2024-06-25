<?php

namespace App\Controller;

use App\Form\DownloadDataType;
use App\Repository\AccessionRepository;
use App\Repository\AttributeRepository;
use App\Repository\AttributeTraitValueRepository;
use App\Repository\CollectingMissionRepository;
use App\Repository\CollectionClassRepository;
use App\Repository\CrossRepository;
use App\Repository\GermplasmRepository;
use App\Repository\MappingPopulationRepository;
use App\Repository\PedigreeRepository;
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
 * @Route("/download/germplasm", name="download_germplasm_data_")
 */
class DownloadGermplasmDataController extends AbstractController
{
    private $germplasmRepo;
    private $crossRepo;
    private $collectionRepo;
    private $colMissionRepo;
    private $accessionRepo;
    private $attributeRepo;
    private $attTrValRepo;
    private $pedigreeRepo;
    private $mappingPopRepo;

    public function __construct(GermplasmRepository $germplasmRepo, CrossRepository $crossRepo,
                                CollectionClassRepository $collectionRepo, CollectingMissionRepository $colMissionRepo,
                                AccessionRepository $accessionRepo, AttributeRepository $attributeRepo,
                                AttributeTraitValueRepository $attTrValRepo, PedigreeRepository $pedigreeRepo,
                                MappingPopulationRepository $mappingPopRepo) {
        $this->germplasmRepo = $germplasmRepo;
        $this->crossRepo = $crossRepo;
        $this->collectionRepo = $collectionRepo;
        $this->colMissionRepo = $colMissionRepo;
        $this->accessionRepo = $accessionRepo;
        $this->attributeRepo = $attributeRepo;
        $this->attTrValRepo = $attTrValRepo;
        $this->pedigreeRepo = $pedigreeRepo;
        $this->mappingPopRepo = $mappingPopRepo;
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
        // get the germplasms
        $germplasms = $this->germplasmRepo->findAll();
        // create a new document
        $spreadsheet = new Spreadsheet();
        
        // Get active sheet - it is also possible to retrieve a specific sheet
        $germplasmsSheet = $spreadsheet->getActiveSheet();
        $germplasmsSheet->setTitle("Germplasms");

        // Set column names for 
        $germColumnNames = [
            'Germplasm ID',
            'Accession Maintainer Number',
            'Germplasm Preprocessing',
            'INSTCODE',
            'Program Associated Abbreviation'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($germColumnNames, $germplasmsSheet);

        // Add data for each column
        $germColValues = [];
        foreach ($germplasms as $key => $one) {
            # code...
            $germColValues [] = [
                $one->getGermplasmID(),
                $one->getMaintainerNumb(),
                $one->getPreprocessing(),
                $one->getInstcode(),
                $one->getProgram() ? $one->getProgram()->getAbbreviation() : '',
            ]; 
        }
        
        // cell filling program sheet call
        $this->cellFilling($germColValues, $germplasmsSheet);

        
        // get the accessions
        $accessions = $this->accessionRepo->findAll();
        // sheet 2
        $accessionsSheet = $spreadsheet->createSheet(1)->setTitle("Accessions");

        // Set column names for 
        $accessionsColumnNames = [
            'Accession Number',
            'Accession Name',
            'Country of Origin',
            'Municipality',
            'Municipality 1',
            'Municipality 2',
            'Collecting Source',
            'Biological Status',
            'Taxon',
            'Collecting Number',
            'Collecting Institute Code',
            'Collecting Mission Identifier',
            'Collecting Date',
            'Collecting Site Latitude',
            'Collecting Site Longitude',
            'Collecting Site Elevation',
            'Collecting Site',
            'Maintaining Institute Code',
            'Accession PUID',
            'Maintaining Accession Number',
            'Acquisition Date',
            'Storage Type',
            'Donor Institute Code',
            'Donor Number',
            'Breeding Institute Code',
            'Pedigree'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($accessionsColumnNames, $accessionsSheet);

        // Add data for each column
        $accessionsColValues = [];
        foreach ($accessions as $key => $one) {
            # code...
            $accessionsColValues [] = [
                $one->getAccenumb(),
                $one->getAccename(),
                $one->getOrigcty() ? $one->getOrigcty()->getIso3() : '',
                $one->getOrigmuni(),
                $one->getOrigadmin1(),
                $one->getOrigadmin2(),
                $one->getCollsrc() ? $one->getCollsrc()->getOntologyId() : '',
                $one->getSampstat() ? $one->getSampstat()->getOntologyId() : '',
                $one->getTaxon() ? $one->getTaxon()->getTaxonid() : '',
                $one->getCollnumb(),
                $one->getCollcode() ? $one->getCollcode()->getAcronym() : '',
                $one->getCollmissid() ? $one->getCollmissid()->getName() : '',
                $one->getColldate(),
                $one->getDeclatitude(),
                $one->getDeclongitude(),
                $one->getElevation(),
                $one->getCollsite(),
                $one->getMaintainernumb(),
                $one->getAcqdate(),
                $one->getStorage() ? $one->getStorage()->getOntologyId() : '',
                $one->getDonorcode() ? $one->getDonorcode()->getAcronym() : '',
                $one->getDonornumb(),
                $one->getBredcode() ? $one->getBredcode()->getAcronym() : '',
                $one->getBreedingInfo()
            ]; 
        }

        // cell filling accessions sheet call
        $this->cellFilling($accessionsColValues, $accessionsSheet);


        // get the crosses
        $crosses = $this->crossRepo->findAll();
        // sheet 3
        $crossesSheet = $spreadsheet->createSheet(2)->setTitle("Crosses");

        // Set column names for 
        $crossesColumnNames = [
            'Cross Name',
            'Cross Description',
            'Cross Parent 1',
            'Cross Parent 1 Type',
            'Cross Parent 2',
            'Cross Parent 2 Type',
            'Cross Year',
            'Institute',
            'Breeding Method',
            'Study',
            'Publication Reference'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($crossesColumnNames, $crossesSheet);

        // Add data for each column
        $crossesColValues = [];
        foreach ($crosses as $key => $one) {
            # code...
            $crossesColValues [] = [
                $one->getName(),
                $one->getDescription(),
                $one->getParent1()->getGermplasmID(),
                $one->getParent1Type(),
                $one->getParent2()->getGermplasmID(),
                $one->getParent2Type(),
                $one->getYear(),
                $one->getInstitute() ? $one->getInstitute()->getAcronym() : '',
                $one->getBreedingMethod() ? $one->getBreedingMethod()->getOntologyId() : '',
                $one->getStudy() ? $one->getStudy()->getAbbreviation() : '',
                $one->getPublicationReference() ? $one->getPublicationReference()[0] : ''
            ]; 
        }
        
        // cell filling program sheet call
        $this->cellFilling($crossesColValues, $crossesSheet);


        // get the collections
        $collections = $this->collectionRepo->findAll();
        // sheet 4
        $collectionsSheet = $spreadsheet->createSheet(3)->setTitle("Collections");

        // Set column names for 
        $collectionsColumnNames = [
            'Collection Name',
            'Collection Description'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($collectionsColumnNames, $collectionsSheet);

        // Add data for each column
        $collectionsColValues = [];
        foreach ($collections as $key => $one) {
            # code...
            $collectionsColValues [] = [
                $one->getName(),
                $one->getDescription()
            ]; 
        }
        
        // cell filling collection sheet call
        $this->cellFilling($collectionsColValues, $collectionsSheet);

        
        // get the collecting missions
        $collectingMissions = $this->colMissionRepo->findAll();
        // sheet 5
        $collectingMissionsSheet = $spreadsheet->createSheet(4)->setTitle("Collecting Mission");

        // Set column names for 
        $collectingMissionsColumnNames = [
            'Collecting Mission Name',
            'Collecting Mission Species',
            'Institute'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($collectingMissionsColumnNames, $collectingMissionsSheet);

        // Add data for each column
        $collectingMissionsColValues = [];
        foreach ($collectingMissions as $key => $one) {
            # code...
            $collectingMissionsColValues [] = [
                $one->getName(),
                $one->getSpecies(),
                $one->getInstitute() ? $one->getInstitute()->getAcronym() : ''
            ]; 
        }
        
        // cell filling sample sheet call
        $this->cellFilling($collectingMissionsColValues, $collectingMissionsSheet);


        // get the attributes
        $attributes = $this->attributeRepo->findAll();
        // sheet 6
        $attributesSheet = $spreadsheet->createSheet(5)->setTitle("Attributes");

        // Set column names for 
        $attributesColumnNames = [
            'Name',
            'Abbreviation',
            'Description',
            'Category',
            'Publication Reference'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($attributesColumnNames, $attributesSheet);

        // Add data for each column
        $attributesColValues = [];
        foreach ($attributes as $key => $one) {
            # code...
            $attributesColValues [] = [
                $one->getName(),
                $one->getAbbreviation(),
                $one->getDescription(),
                $one->getCategory() ? $one->getCategory()->getOntologyId() : '',
                $one->getPublicationReference() ? $one->getPublicationReference()[0] : ''
            ]; 
        }
        
        // cell filling sample sheet call
        $this->cellFilling($attributesColValues, $attributesSheet);


        // get the attribute trait values
        $attrTrValues = $this->attTrValRepo->findAll();
        // sheet 6
        $attrTrValuesSheet = $spreadsheet->createSheet(7)->setTitle("Attribute Trait Values");

        // Set column names for 
        $attrTrValuesColumnNames = [
            'Accession Number',
            'Attribute Name',
            'Attribute Category',
            'Trait',
            'Value',
            'Metabolic Trait',
            'Publication Reference'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($attrTrValuesColumnNames, $attrTrValuesSheet);

        // Add data for each column
        $attrTrValuesColValues = [];
        foreach ($attrTrValues as $key => $one) {
            # code...
            $attrTrValuesColValues [] = [
                $one->getAccession() ? $one->getAccession()->getAccenumb() : '',
                $one->getAttribute() ? $one->getAttribute()->getAbbreviation() : '',
                $one->getAttribute() ? $one->getAttribute()->getCategory()->getOntologyId() : '',
                $one->getTrait() ? $one->getTrait()->getOntologyId() : '',
                $one->getValue(),
                $one->getMetabolicTrait() ? $one->getMetabolicTrait()->getOntologyId() : '',
                $one->getPublicationReference() ? $one->getPublicationReference()[0] : ''
            ]; 
        }

        // cell filling sample sheet call
        $this->cellFilling($attrTrValuesColValues, $attrTrValuesSheet);


        // get the pedigrees
        $pedigrees = $this->pedigreeRepo->findAll();
        // sheet 7
        $pedigreesSheet = $spreadsheet->createSheet(8)->setTitle("Pedigrees");

        // Set column names for 
        $pedigreesColumnNames = [
            'Pedigree Entry ID',
            'Germplasm',
            'Generation',
            'Ancestor ID',
            'Cross'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($pedigreesColumnNames, $pedigreesSheet);

        // Add data for each column
        $pedigreesColValues = [];
        foreach ($pedigrees as $key => $one) {
            # code...
            $pedigreesColValues [] = [
                $one->getPedigreeAncestorEntryId() ? $one->getPedigreeAncestorEntryId() : '',
                $one->getGermplasm()[0] ? $one->getGermplasm()[0]->getGermplasmID() : '',
                $one->getGeneration() ? $one->getGeneration()->getOntologyId() : '',
                $one->getPedigreeAncestorEntryId() ? $one->getPedigreeAncestorEntryId()->getPedigreeEntryID() : '',
                $one->getPedigreeCross() ? $one->getPedigreeCross()->getCrossName() : ''
            ]; 
        }
        
        // cell filling sample sheet call
        $this->cellFilling($pedigreesColValues, $pedigreesSheet);


        // get the collecting missions
        $mappingPopulations = $this->mappingPopRepo->findAll();
        // sheet 8
        $mappingPopulationsSheet = $spreadsheet->createSheet(9)->setTitle("Mapping Populations");

        // Set column names for 
        $mappingPopulationsColumnNames = [
            'Mapping Population Name',
            'Cross Name',
            'Cross Institute',
            'Cross Study',
            'Parent 1',
            'Parent 1 Type',
            'Parent 2',
            'Parent 2 Type',
            'Cross Year',
            'Pedigree Generation',
            'Publication Reference'

        ];

        // Allow extra columns call
        $this->allowExtraColumn($mappingPopulationsColumnNames, $mappingPopulationsSheet);

        // Add data for each column
        $mappingPopulationsColValues = [];
        foreach ($mappingPopulations as $key => $one) {
            # code...
            $mappingPopulationsColValues [] = [
                $one->getName(),
                $one->getMappingPopulationCross() ? $one->getMappingPopulationCross()->getName() : '',
                $one->getMappingPopulationCross() ? $one->getMappingPopulationCross()->getInstitute()->getAcronym() : '',
                $one->getMappingPopulationCross() ? $one->getMappingPopulationCross()->getStudy()->getAbbreviation() : '',
                $one->getMappingPopulationCross() ? $one->getMappingPopulationCross()->getParent1()->getGermplasmID() : '',
                $one->getMappingPopulationCross() ? $one->getMappingPopulationCross()->getParent1Type() : '',
                $one->getMappingPopulationCross() ? $one->getMappingPopulationCross()->getParent2()->getGermplasmID() : '',
                $one->getMappingPopulationCross() ? $one->getMappingPopulationCross()->getParent2Type() : '',
                $one->getMappingPopulationCross() ? $one->getMappingPopulationCross()->getName() : '',
                $one->getMappingPopulationCross() ? $one->getMappingPopulationCross()->getYear() : '',
                $one->getPedigreeGeneration() ? $one->getPedigreeGeneration()->getOntologyId() : '',
                $one->getPublicationRef() ? $one->getPublicationRef()[0] : ''
            ]; 
        }
        
        // cell filling sample sheet call
        $this->cellFilling($mappingPopulationsColValues, $mappingPopulationsSheet);

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
            $filename = 'HarnesstomDB Germplasm Data.'.$format;

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
            'title' => 'Germplasm Data Download',
            'form' => $form->createView()
        ];

        return $this->render('download_germplasm_data/index.html.twig', $context);
    }
    
}
