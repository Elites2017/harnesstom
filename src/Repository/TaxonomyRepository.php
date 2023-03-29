<?php

namespace App\Repository;

use App\Entity\Taxonomy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Taxonomy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taxonomy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taxonomy[]    findAll()
 * @method Taxonomy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxonomyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taxonomy::class);
    }

    // to show the number of accession by each taxonomy
    public function getAccessionsByTaxonomy() {
        $query = $this->createQueryBuilder('tax')
            ->select('tax as taxonomy, count(tax.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('tax.isActive = 1')
            ->andWhere('tax.id = accession.taxon')
            ->groupBy('tax.id')
            ->orderBy('count(tax.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // to show the number of accession by species
    // public function getAccessionsBySpecies() {
    //     $query = $this->createQueryBuilder('tax')
    //         ->select('tax.species as species, tax.id as id, count(tax.id) as accQty')
    //         ->join('App\Entity\Accession', 'accession')
    //         ->where('tax.isActive = 1')
    //         ->andWhere('tax.id = accession.taxon')
    //         ->groupBy('tax.id')
    //         ->orderBy('count(tax.id)', 'DESC')
    //     ;
    //     return $query->getQuery()->getResult();
    // }

    // /**
    //  * @return Taxonomy[] Returns an array of Taxonomy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Taxonomy
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
