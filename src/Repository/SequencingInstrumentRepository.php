<?php

namespace App\Repository;

use App\Entity\SequencingInstrument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SequencingInstrument|null find($id, $lockMode = null, $lockVersion = null)
 * @method SequencingInstrument|null findOneBy(array $criteria, array $orderBy = null)
 * @method SequencingInstrument[]    findAll()
 * @method SequencingInstrument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SequencingInstrumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SequencingInstrument::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return SequencingInstrument[] Returns an array of SequencingInstrument objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SequencingInstrument
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
