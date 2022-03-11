<?php

namespace App\Repository;

use App\Entity\StudyImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StudyImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudyImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudyImage[]    findAll()
 * @method StudyImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudyImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudyImage::class);
    }

    // /**
    //  * @return StudyImage[] Returns an array of StudyImage objects
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
    public function findOneBySomeField($value): ?StudyImage
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
