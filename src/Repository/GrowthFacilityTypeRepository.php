<?php

namespace App\Repository;

use App\Entity\GrowthFacilityType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GrowthFacilityType|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrowthFacilityType|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrowthFacilityType[]    findAll()
 * @method GrowthFacilityType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrowthFacilityTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrowthFacilityType::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return GrowthFacilityType[] Returns an array of GrowthFacilityType objects
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
    public function findOneBySomeField($value): ?GrowthFacilityType
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
