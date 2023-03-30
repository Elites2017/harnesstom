<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }
    
    public function getAccessionCountries() {
        $query = $this->createQueryBuilder('country')
            ->join('App\Entity\Accession', 'accession')
            ->where('country.isActive = 1')
            ->andWhere('country.id = accession.origcty')
        ;
        return $query->getQuery()->getResult();
    }

    // to show the number of accession by each country
    public function getAccessionsByCountry($biologicalStatuses = null) {
        $query = $this->createQueryBuilder('ctry')
            ->select('ctry as country, count(ctry.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('ctry.isActive = 1')
            ->andWhere('ctry.id = accession.origcty')
            ->groupBy('ctry.id')
            ->orderBy('count(ctry.id)', 'DESC')
        ;
        if ($biologicalStatuses) {
            $query->andWhere('accession.sampstat IN(:selectedBiologicalStatuses)')
            ->setParameter(':selectedBiologicalStatuses', array_values($biologicalStatuses));
        }
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Country[] Returns an array of Country objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Country
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
