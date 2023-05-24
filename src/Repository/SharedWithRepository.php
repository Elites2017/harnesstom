<?php

namespace App\Repository;

use App\Entity\SharedWith;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SharedWith|null find($id, $lockMode = null, $lockVersion = null)
 * @method SharedWith|null findOneBy(array $criteria, array $orderBy = null)
 * @method SharedWith[]    findAll()
 * @method SharedWith[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SharedWithRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SharedWith::class);
    }

    public function totalRows($user = null) {
        $query = $this->createQueryBuilder('sw')
            ->select('count(sw.id)')
        ;

        if ($user) {
            $query->where('sw.user = :userId')
            ->setParameter(':userId', $user->getId());
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    // /**
    //  * @return SharedWith[] Returns an array of SharedWith objects
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
    public function findOneBySomeField($value): ?SharedWith
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
