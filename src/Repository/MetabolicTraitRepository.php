<?php

namespace App\Repository;

use App\Entity\MetabolicTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MetabolicTrait|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetabolicTrait|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetabolicTrait[]    findAll()
 * @method MetabolicTrait[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetabolicTraitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MetabolicTrait::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return MetabolicTrait[] Returns an array of MetabolicTrait objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MetabolicTrait
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
