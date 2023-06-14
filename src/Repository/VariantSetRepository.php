<?php

namespace App\Repository;

use App\Entity\VariantSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VariantSet|null find($id, $lockMode = null, $lockVersion = null)
 * @method VariantSet|null findOneBy(array $criteria, array $orderBy = null)
 * @method VariantSet[]    findAll()
 * @method VariantSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VariantSetRepository extends ServiceEntityRepository
{
    private $swRepo;

    public function __construct(ManagerRegistry $registry, SharedWithRepository $swRepo)
    {
        $this->swRepo = $swRepo;
        parent::__construct($registry, VariantSet::class);
    }

    public function findReleasedTrialStudySampleVariantSet($user = null)
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('vs')
            ->from('App\Entity\Sample', 'spl')
            ->from('App\Entity\Study', 'st')    
            ->from('App\Entity\Trial', 'tr')
            ->Where('vs.isActive = 1')
            ->andWhere('vs.sample = spl.id')
            ->andWhere('spl.study = st.id')
            ->andWhere('st.trial = tr.id')
            ->andWhere('tr.publicReleaseDate <= :currentDate')
            ->setParameter(':currentDate', $currentDate)
        ;

        if ($user) {
            // check if any trial is shared with this user
            // 0 means no trial is shared with this user
            // in this case show the studies where this user
            // is the owner of future trials used in those studies
            if ($this->swRepo->totalRows($user) == 0) {
                $query->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'vs.sample = spl.id',
                            'spl.study = st.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate'))
                        ->setParameter(':user', $user->getId())
                        ->setParameter(':currentDate', $currentDate);
            }
            
            // if one trial is shared with this user
            // include the shared trial in the list study list
            if ($this->swRepo->totalRows($user) > 0) {
                $query->from('App\Entity\SharedWith', 'sw')
                    ->orWhere(
                        $query->expr()->andX(
                            'vs.sample = spl.id',
                            'spl.study = st.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate',
                            'sw.user = :user',
                            'sw.trial = tr.id',
                            ))
                    ->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'vs.sample = spl.id',
                            'spl.study = st.id',
                            'st.trial = tr.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate'))
                        ->setParameter(':user', $user->getId())
                        ->setParameter(':currentDate', $currentDate);
            }
        }
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return VariantSet[] Returns an array of VariantSet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VariantSet
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
