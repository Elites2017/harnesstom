<?php

namespace App\Repository;

use App\Entity\Accession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Accession|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accession|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accession[]    findAll()
 * @method Accession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accession::class);
    }

    /**
     * 23/11/2022 3h20 PM
     * Returns all the accessions based on the filters if any (filters) 
     */
    public function getAccessionFilteredOrNot($countries = null, $biologicalStatuses = null) {
        $query = $this->createQueryBuilder('acc')
            ->where('acc.isActive = 1');
        
        if ($countries) {
            $query->andWhere('acc.origcty IN(:selectedCountries)')
            ->setParameter(':selectedCountries', array_values($countries));
        }
        if ($biologicalStatuses) {
            $query->andWhere('acc.sampstat IN(:selectedBiologicalStatuses)')
            ->setParameter(':selectedBiologicalStatuses', array_values($biologicalStatuses));
        }
        ;
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Accession[] Returns an array of Accession objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Accession
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
