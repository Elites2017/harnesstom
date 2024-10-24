<?php

namespace App\Controller;

use App\Form\DownloadDataType;
use App\Repository\GWASRepository;
use App\Repository\GWASVariantRepository;
use App\Repository\QTLEpistasisEffectRepository;
use App\Repository\QTLStudyRepository;
use App\Repository\QTLVariantRepository;
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
 * @Route("/download/variant", name="download_variant_data_")
 */
class DownloadVariantDataController extends AbstractController
{
    private $qtlStudyRepo;
    private $gwasRepo;
    private $qtlVariantRepo;
    private $gwasVariantRepo;
    private $qtlEpistasisRepo;

    public function __construct(QTLStudyRepository $qtlStudyRepo, GWASRepository $gwasRepo,
                                QTLVariantRepository $qtlVariantRepo,
                                GWASVariantRepository $gwasVariantRepo,
                                QTLEpistasisEffectRepository $qtlEpistasisRepo) {
        $this->qtlStudyRepo = $qtlStudyRepo;
        $this->gwasRepo = $gwasRepo;
        $this->qtlVariantRepo = $qtlVariantRepo;
        $this->gwasVariantRepo = $gwasVariantRepo;
        $this->qtlEpistasisRepo = $qtlEpistasisRepo;
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
        // get the qtl studies
        $qtlStudies = $this->qtlStudyRepo->getPublicReleasedData();
        // create a new document
        $spreadsheet = new Spreadsheet();
        
        // Get active sheet - it is also possible to retrieve a specific sheet
        $qtlStudiesSheet = $spreadsheet->getActiveSheet();
        $qtlStudiesSheet->setTitle("QTL Studies");

        // Set column names for 
        $qtlStudiesColumnNames = [
            'QTL Study Name',
            'QTL Method',
            'QTL Count',
            'Ci Criteria',
            'Genome Map Unit',
            'Threshold Method',
            'Threshold Value',
            'Software',
            'Mapping Population',
            'Variant Set Metadata',
            'Multi Environment Statistic',
            'QTL Statistic',
            'Publication Reference',
            'Related Studies'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($qtlStudiesColumnNames, $qtlStudiesSheet);

        // for collection to array
        $colToArr = [];
        // Add data for each column
        $qtlStudiesColValues = [];
        foreach ($qtlStudies as $key => $one) {
            // for the collections to array
            foreach ($one->getStudyList() as $oneStudy) {
                # code...
                $colToArr [] = $oneStudy->getAbbreviation();
            }
            # code...
            $qtlStudiesColValues [] = [
                $one->getName(),
                $one->getMethod() ? $one->getMethod()->getName() : '',
                $one->getQtlCount(),
                $one->getCiCriteria() ? $one->getCiCriteria()->getName() : '',
                $one->getGenomeMapUnit() ? $one->getGenomeMapUnit()->getName() : '',
                $one->getThresholdMethod(),
                $one->getThresholdValue(),
                $one->getSoftware() ? $one->getSoftware()->getName() : '',
                $one->getMappingPopulation() ? $one->getMappingPopulation()->getName() : '',
                $one->getVariantSetMetadata() ? $one->getVariantSetMetadata()->getName() : '',
                $one->getMultiEnvironmentStat() ? $one->getMultiEnvironmentStat()->getName() : '',
                $one->getStatistic() ? $one->getStatistic()->getName() : '',
                $one->getPublicationReference() ? implode('; ', $one->getPublicationReference()) : '',
                $colToArr ? implode('; ', $colToArr) : '',
                            ];
            $colToArr = []; 
        }
        
        // cell filling qtl studies sheet call
        $this->cellFilling($qtlStudiesColValues, $qtlStudiesSheet);

        
        // get the gwas
        $gwas = $this->gwasRepo->getPublicReleasedData();
        // sheet 2
        $gwasSheet = $spreadsheet->createSheet(1)->setTitle("GWAS");

        // Set column names for 
        $gwasColumnNames = [
            'GWAS Name',
            'Threshold Method',
            'Threshold Value',
            'GWAS Model',
            'Software',
            'Structure Method',
            'Preprocessing',
            'GWAS Stat Test',
            'Kinship Algorithm',
            'Variant Set Metadata',
            'Allelic Effect Estimator',
            'Publication Reference',
            'Related Studies'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($gwasColumnNames, $gwasSheet);

        // Add data for each column
        $gwasColValues = [];
        foreach ($gwas as $key => $one) {
            // for the collections to array
            foreach ($one->getStudyList() as $oneStudy) {
                # code...
                $colToArr [] = $oneStudy->getAbbreviation();
            }
            # code...
            $gwasColValues [] = [
                $one->getName(),
                $one->getThresholdMethod() ? $one->getThresholdMethod()->getName() : '',
                $one->getThresholdValue(),
                $one->getGwasModel() ? $one->getGwasModel()->getName() : '',
                $one->getSoftware() ? $one->getSoftware()->getName() : '',
                $one->getStructureMethod() ? $one->getStructureMethod()->getName() : '',
                $one->getPreprocessing(),
                $one->getGwasStatTest() ? $one->getGwasStatTest()->getName() : '',
                $one->getKinshipAlgorithm() ? $one->getKinshipAlgorithm()->getName() : '',
                $one->getVariantSetMetadata() ? $one->getVariantSetMetadata()->getName() : '',
                $one->getAllelicEffectEstimator() ? $one->getAllelicEffectEstimator()->getName() : '',
                $one->getPublicationReference() ? implode('; ', $one->getPublicationReference()) : '',
                $colToArr ? implode('; ', $colToArr) : ''
            ];
            $colToArr = []; 
        }

        // cell filling gwas sheet call
        $this->cellFilling($gwasColValues, $gwasSheet);


        // get the qtl variants
        $qtlVariants = $this->qtlVariantRepo->getPublicReleasedData();
        // sheet 3
        $qtlVariantsSheet = $spreadsheet->createSheet(2)->setTitle("QTL Variants");

        // Set column names for 
        $qtlVariantsColumnNames = [
            'QTL Variant Name',
            'QTL Study Name',
            'Detect Name',
            'Original Trait Name',
            'Locus',
            'Locus Name',
            'R2',
            'R2 Global',
            'R2 QTL XE',
            'Statistic QTL X Evalue',
            'DA',
            'Dominance',
            'QTL Stat Value',
            'Additive',
            'Metabolite',
            'Observation Variable',
            'Closest Marker',
            'Flanking Marker Start',
            'Flanking Marker End',
            'Position Allele',
            'Positive Allele Parent',
            'Ci Start',
            'Ci End',
            'Linkage Group Name',
            'Peak Position',
            'Publication Reference'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($qtlVariantsColumnNames, $qtlVariantsSheet);

        // Add data for each column
        $qtlVariantsColValues = [];
        foreach ($qtlVariants as $key => $one) {
            # code...
            $qtlVariantsColValues [] = [
                $one->getName(),
                $one->getQtlStudy() ? $one->getQtlStudy()->getName() : '',
                $one->getDetectName(),
                $one->getOriginalTraitName(),
                $one->getLocus(),
                $one->getLocusName(),
                $one->getR2(),
                $one->getR2Global(),
                $one->getR2QTLxE(),
                $one->getStatisticQTLxEValue(),
                $one->getDA(),
                $one->getDominance(),
                $one->getQtlStatsValue(),
                $one->getAdditive(),
                $one->getMetabolite() ? $one->getMetabolite()->getMetabolicTrait() : '',
                $one->getObservationVariable() ? $one->getObservationVariable() : '',
                $one->getClosestMarker() ? $one->getClosestMarker()->getName() : '',
                $one->getFlankingMarkerStart() ? $one->getFlankingMarkerStart()->getName() : '',
                $one->getFlankingMarkerEnd() ? $one->getFlankingMarkerEnd()->getName() : '',
                $one->getPositiveAllele(),
                $one->getPositiveAlleleParent() ? $one->getPositiveAlleleParent()->getGermplasmID() : '',
                $one->getCiStart(),
                $one->getCiEnd(),
                $one->getLinkageGroupName(),
                $one->getPeakPosition(),
                $one->getPublicationReference() ? implode('; ', $one->getPublicationReference()) : '',
            ]; 
        }
        
        // cell filling qtlVariants sheet call
        $this->cellFilling($qtlVariantsColValues, $qtlVariantsSheet);


        // get the gwas variant
        $gwasVariant = $this->gwasVariantRepo->getPublicReleasedData();
        // sheet 4
        $gwasVariantSheet = $spreadsheet->createSheet(3)->setTitle("GWAS Variants");

        // Set column names for 
        $gwasVariantColumnNames = [
            'GWAS Variant Name',
            'GWAS Name',
            'MAF',
            'Sample Size',
            'SNP P Value',
            'Adjusted P Value',
            'Allelic Effect',
            'Allelic Effect Stat',
            'Allelic Effect DF',
            'Allelic Effect StdE',
            'Beta',
            'Beta StdE',
            'Metabolite',
            'Observation Variable',
            'Marker',
            'Odds Ratio',
            'R Square Of Mode',
            'R Square Of Mode With SNP',
            'R Square Of Mode Without SNP',
            'Reference Allele',
            'Ci Lower',
            'Ci Upper'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($gwasVariantColumnNames, $gwasVariantSheet);

        // Add data for each column
        $gwasVariantColValues = [];
        foreach ($gwasVariant as $key => $one) {
            # code...
            $gwasVariantColValues [] = [
                $one->getName(),
                $one->getGwas() ? $one->getGwas()->getName() : '',
                $one->getMaf(),
                $one->getSampleSize(),
                $one->getSnppValue(),
                $one->getAdjustedPValue(),
                $one->getAllelicEffect(),
                $one->getAllelicEffectStat(),
                $one->getAllelicEffectdf(),
                $one->getAllelicEffStdE(),
                $one->getBeta(),
                $one->getBetaStdE(),
                $one->getMetabolite() ? $one->getMetabolite()->getMetabolicTrait() : '',
                $one->getObservationVariable() ? $one->getObservationVariable() : '',
                $one->getMarker() ? $one->getMarker()->getName() : '',
                $one->getOddsRatio(),
                $one->getRSquareOfMode(),
                $one->getRSquareOfModeWithSNP(),
                $one->getRSquareOfModeWithoutSNP(),
                $one->getRefAllele(),
                $one->getCiLower(),
                $one->getCiUpper()
            ]; 
        }
        
        // cell filling variant set metadata sheet call
        $this->cellFilling($gwasVariantColValues, $gwasVariantSheet);


        // get the qtl epistasis effect
        $qtlEpistasis = $this->qtlEpistasisRepo->getPublicReleasedData();
        // sheet 5
        $qtlEpistasisSheet = $spreadsheet->createSheet(4)->setTitle("QTL Epistasis Effect");

        // Set column names for 
        $qtlEpistasisColumnNames = [
            'QTL Variant 1 Name',
            'QTL Variant 2 Name',
            'Epistatistic Epi',
            'R2 Epi',
            'Add Epi',
            'R2 Add',
            'Epistatistic Add'
        ];

        // Allow extra columns call
        $this->allowExtraColumn($qtlEpistasisColumnNames, $qtlEpistasisSheet);

        // Add data for each column
        $qtlEpistasisColValues = [];
        foreach ($qtlEpistasis as $key => $one) {
            # code...
            $qtlEpistasisColValues [] = [
                $one->getQtlVariant1() ? $one->getQtlVariant1()->getName() : '',
                $one->getQtlVariant1() ? $one->getQtlVariant2()->getName() : '',
                $one->getEpistatisticEpi(),
                $one->getR2Epi(),
                $one->getAddEpi(),
                $one->getR2Add(),
                $one->getEpistatisticAdd(),
            ];
        }

        // cell filling qtlEpistasis sheet call
        $this->cellFilling($qtlEpistasisColValues, $qtlEpistasisSheet);

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
            $filename = 'HarnesstomDB Variant Data.'.$format;

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
            'title' => 'Variant Download',
            'form' => $form->createView()
        ];

        return $this->render('download_variant_data/index.html.twig', $context);
    }
    
}
