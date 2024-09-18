<?php

namespace App\Repository;

use App\Entity\QTLEpistasisEffect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QTLEpistasisEffect|null find($id, $lockMode = null, $lockVersion = null)
 * @method QTLEpistasisEffect|null findOneBy(array $criteria, array $orderBy = null)
 * @method QTLEpistasisEffect[]    findAll()
 * @method QTLEpistasisEffect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QTLEpistasisEffectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QTLEpistasisEffect::class);
    }

    // to download publicly release trial associated data
    public function getPublicReleasedData()
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('qtlEpi')
            ->from('App\Entity\QTLVariant', 'qtlV1')
            ->from('App\Entity\QTLVariant', 'qtlV2')
            ->from('App\Entity\QTLStudy', 'qtlS')
            ->from('App\Entity\Study', 'st')    
            ->from('App\Entity\Trial', 'tr')
            ->where('qtlEpi.qtlVariant1 = qtlV1.id')
            ->andWhere('qtlEpi.qtlVariant2 = qtlV2.id')
            ->andWhere('qtlV1.qtlStudy = qtlS.id')
            ->andWhere('st MEMBER OF qtlS.studyList')
            ->andWhere('st.trial = tr.id')
            ->andWhere('tr.publicReleaseDate <= :currentDate')
            ->setParameter(':currentDate', $currentDate)
        ;
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return QTLEpistasisEffect[] Returns an array of QTLEpistasisEffect objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QTLEpistasisEffect
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
