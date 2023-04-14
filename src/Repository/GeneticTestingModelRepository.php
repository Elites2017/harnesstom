<?php

namespace App\Repository;

use App\Entity\GeneticTestingModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GeneticTestingModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeneticTestingModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeneticTestingModel[]    findAll()
 * @method GeneticTestingModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeneticTestingModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeneticTestingModel::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return GeneticTestingModel[] Returns an array of GeneticTestingModel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GeneticTestingModel
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
