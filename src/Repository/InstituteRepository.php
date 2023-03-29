<?php

namespace App\Repository;

use App\Entity\Institute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Institute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Institute|null findOneBy(array $criteria, array $orderBy = null)
 * @method Institute[]    findAll()
 * @method Institute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstituteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Institute::class);
    }

    // to show the number of accession by maintaining institute
    public function getAccessionsByMaintainingInstitute() {
        $query = $this->createQueryBuilder('inst')
            ->select('inst as institute, count(inst.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.instcode')
            ->groupBy('inst.id')
            ->orderBy('count(inst.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // to show the number of accession by donor institute
    public function getAccessionsByDonorInstitute() {
        $query = $this->createQueryBuilder('inst')
            ->select('inst as institute, count(inst.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.donorcode')
            ->groupBy('inst.id')
            ->orderBy('count(inst.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // to show the number of accession by breding institute
    public function getAccessionsByBreedingInstitute() {
        $query = $this->createQueryBuilder('inst')
            ->select('inst as institute, count(inst.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.bredcode')
            ->groupBy('inst.id')
            ->orderBy('count(inst.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Institute[] Returns an array of Institute objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Institute
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
