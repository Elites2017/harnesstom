<?php

namespace App\Controller;

use App\Entity\Accession;
use App\Entity\Country;
use App\Entity\GenotypingPlatform;
use App\Entity\Marker;
use App\Entity\Program;
use App\Entity\Trial;
use App\Entity\User;
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
        $repoProgram = $entityManager->getRepository(Program::class);
        // Query how many rows are there in the Program table
        $totalProgram = $repoProgram->createQueryBuilder('tab')
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
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $accessionPerCountry = $qb->select('country.iso3, count(accession.maintainernumb) as accQty')
                ->from('App\Entity\Country', 'country')
                ->join('App\Entity\Accession', 'accession')
                ->where('country.id = accession.origcty')
                ->groupBy('accession.origcty')
                ->orderBy('count(accession.maintainernumb)', 'DESC')
                ->setFirstResult(0)
                ->setMaxResults(3)
                ->getQuery()
                ->getResult();
        
        $repoAccession = $entityManager->getRepository(Accession::class);
        // Query how many rows are there in the Accession table
        $totalAccession = $repoAccession->createQueryBuilder('tab')
            // Filter by some parameter if you want
            // ->where('a.isActive = 1')
            ->select('count(tab.id)')
            ->getQuery()
            ->getSingleScalarResult();

        
        $context = [
            'title' => 'Home Page',
            "totalMarker" => $totalMarker,
            "totalGenotypingPlatform" => $totalGenotypingPlatform,
            "totalProgram" => $totalProgram,
            "totalTrial" => $totalTrial,
            "totalAccession" => $totalAccession,
            "accessionPerCountry" => $accessionPerCountry
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
