<?php

namespace App\Repository;

use App\Entity\AnatomicalEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnatomicalEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnatomicalEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnatomicalEntity[]    findAll()
 * @method AnatomicalEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnatomicalEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnatomicalEntity::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return AnatomicalEntity[] Returns an array of AnatomicalEntity objects
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
    public function findOneBySomeField($value): ?AnatomicalEntity
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
