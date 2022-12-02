<?php

namespace App\Repository;

use App\Entity\GermplasmStudyImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GermplasmStudyImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method GermplasmStudyImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method GermplasmStudyImage[]    findAll()
 * @method GermplasmStudyImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GermplasmStudyImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GermplasmStudyImage::class);
    }

    public function getTotalRows() {
        return $this->createQueryBuilder('tab')
            ->select('count(tab.id)')
            ->where('tab.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return GermplasmStudyImage[] Returns an array of GermplasmStudyImage objects
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
    public function findOneBySomeField($value): ?GermplasmStudyImage
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
