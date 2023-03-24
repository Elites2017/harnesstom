<?php

namespace App\Controller;

use App\Entity\Accession;
use App\Entity\Country;
use App\Entity\GenotypingPlatform;
use App\Entity\Marker;
use App\Entity\Study;
use App\Entity\Trial;
use App\Entity\User;
use App\Entity\MappingPopulation;
use App\Entity\GWAS;
use App\Entity\QTLStudy;
use App\Entity\GermplasmStudyImage;
use App\Entity\GWASVariant;
use App\Entity\Metabolite;
use App\Entity\QTLVariant;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $users = $userRepository->findAll();
        if (count($users) === 0) {
            $user = new User();
            $user->setEmail("harnesstom@harnesstom.eu");
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        "123456"
                    )
                );
                $user->setIsActive(true);
                $user->setIsVerified(true);
                $roles [] = "ROLE_ADMIN";
                $user->setRoles($roles);
                $entityManager->persist($user);
                $entityManager->flush();
        }

        // Setup repository of some entity
        $repoMarker = $entityManager->getRepository(Marker::class);
        // Query how many rows are there in the Marker table
        $totalMarker = $repoMarker->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Setup repository of some entity
        $repoGenotypingPlatform = $entityManager->getRepository(GenotypingPlatform::class);
        // Query how many rows are there in the GenotypingPlatform table
        $totalGenotypingPlatform = $repoGenotypingPlatform->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Setup repository of some entity
        $repoStudy = $entityManager->getRepository(Study::class);
        // Query how many rows are there in the Study table
        $totalStudy = $repoStudy->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Setup repository of some entity
        $repoTrial = $entityManager->getRepository(Trial::class);
        // Query how many rows are there in the Trial table
        $totalTrial = $repoTrial->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Setup repository of some entity
        $repoAccession = $entityManager->getRepository(Accession::class);
        // Query how many rows are there in the Accession table
        $totalAccession = $repoAccession->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Setup query to get the most accession per country
        //$qb = $this->getDoctrine()->getManager()->createQueryBuilder("Select c.id from App\Entity\Country c Where c.id=1");
        $accessionTotalRows = $repoAccession->totalRows();
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $accessionPerCountry = $qb->select('country as ctry, count(accession.maintainernumb) as accQty')
                ->from('App\Entity\Country', 'country')
                ->join('App\Entity\Accession', 'accession')
                ->where('country.id = accession.origcty')
                ->groupBy('accession.origcty')
                ->orderBy('count(accession.maintainernumb)', 'DESC')
                ->setFirstResult(0)
                ->setMaxResults(3)
                ->getQuery()
                ->getResult();

        // Setup query to get the most accession per country with country fiels separately
        // use substring function a in the map there a different iso version is used (iso with 2 letters code)
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $accessionPerCountryFields = $qb->select("country.id, substring(country.iso3, 1, length(country.iso3)-1) as iso2, count(accession.maintainernumb) as accQty, 'a' as percentage")
                ->from('App\Entity\Country', 'country')
                ->join('App\Entity\Accession', 'accession')
                ->where('country.id = accession.origcty')
                ->groupBy('accession.origcty')
                ->orderBy('count(accession.maintainernumb)', 'DESC')
                ->getQuery()
                ->getResult();

        // show percentage of trials
        // foreach ($accessionPerCountryFields as $key => $value) {
        //     # code...
        //     $accessionPerCountryFields[$key]['percentage'] = round($value['accQty'] / $accessionTotalRows * 100);
        // }
        // dd($accessionPerCountryFields);
        
        //dd($qb->getDQL());

        // Setup repository of some entity
        $repoMappingPopulation = $entityManager->getRepository(MappingPopulation::class);
        // Query how many rows are there in the MappingPopulation table
        $totalMappingPopulation = $repoMappingPopulation->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();
        
        // Setup repository of some entity
        $repoGWAS = $entityManager->getRepository(GWAS::class);
        // Query how many rows are there in the GWAS table
        $totalGWAS = $repoGWAS->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Setup repository of some entity
        $repoQTLStudy = $entityManager->getRepository(QTLStudy::class);
        // Query how many rows are there in the QTLStudy table
        $totalQTLStudy = $repoQTLStudy->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        
        // Setup query to get the most accession per country
        $repoGermplasmStudyImage = $entityManager->getRepository(GermplasmStudyImage::class);
        $numbRows = $repoGermplasmStudyImage->getTotalRows();
        //$qb = $this->getDoctrine()->getManager()->createQueryBuilder("Select c.id from App\Entity\Country c Where c.id=1");
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $germplasmStudyImages = $qb->select('germpSI')
            ->from('App\Entity\GermplasmStudyImage', 'germpSI')
            ->setFirstResult(rand(0, $numbRows))
            ->setMaxResults(8)
            ->getQuery()
            ->getResult();

        // Setup query to get the most accession per country
        $repoTrial = $entityManager->getRepository(Trial::class);
        $trialRows = $repoTrial->totalRows();
        //$qb = $this->getDoctrine()->getManager()->createQueryBuilder("Select c.id from App\Entity\Country c Where c.id=1");
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $trialAndType = $qb->select('tr as trial, count(tr.trialType) as trtQty')
            ->from('App\Entity\Trial', 'tr')
            ->join('App\Entity\TrialType', 'trt')
            ->where('tr.trialType = trt.id')
            ->groupBy('trt.name')
            ->orderBy('count(tr.trialType)', 'DESC')
            ->getQuery()
            ->getResult();
        
        // show percentage of trials
        foreach ($trialAndType as $key => $value) {
            # code...
            $trialAndType[$key]['trtQty'] = round($value['trtQty'] / $trialRows * 100);
        }

        // Setup repository of some entity
        $repoQTLVariant = $entityManager->getRepository(QTLVariant::class);
        // Query how many rows are there in the QTLVariant table
        $totalQTLVariant = $repoQTLVariant->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Setup repository of some entity
        $repoGWASVariant = $entityManager->getRepository(GWASVariant::class);
        // Query how many rows are there in the GWASVariant table
        $totalGWASVariant = $repoGWASVariant->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Setup repository of some entity
        $repoMetabolite = $entityManager->getRepository(Metabolite::class);
        // Query how many rows are there in the Metabolite table
        $totalMetabolite = $repoMetabolite->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $context = [
            'title' => 'Harnesstom DB',
            "totalMarker" => $totalMarker,
            "totalGenotypingPlatform" => $totalGenotypingPlatform,
            "totalStudy" => $totalStudy,
            "totalTrial" => $totalTrial,
            "totalAccession" => $totalAccession,
            "accessionPerCountry" => $accessionPerCountry,
            "jsonAcc" => json_encode($accessionPerCountryFields),
            "totalMappingPopulation" => $totalMappingPopulation,
            "totalGWAS" => $totalGWAS,
            "totalQTLStudy" => $totalQTLStudy,
            "germplasmStudyImages" => $germplasmStudyImages,
            "trialAndType" => $trialAndType,
            "totalQTLVariant" => $totalQTLVariant,
            "totalGWASVariant" => $totalGWASVariant,
            "totalMetabolite" => $totalMetabolite,

        ];
        return $this->render('app/index.html.twig', $context);
    }
    
    // /**
    //  * @Route("/", name="index")
    //  */
    // public function index(AttributeRepository $attributeRepo): Response
    // {
    //     $attributes =  $attributeRepo->findAll();
    //     $context = [
    //         'title' => 'Attribute List',
    //         'attributes' => $attributes
    //     ];
    //     return $this->render('attribute/index.html.twig', $context);
    // }
}
