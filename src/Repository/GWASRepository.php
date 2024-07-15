<?php

namespace App\Repository;

use App\Entity\GWAS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GWAS|null find($id, $lockMode = null, $lockVersion = null)
 * @method GWAS|null findOneBy(array $criteria, array $orderBy = null)
 * @method GWAS[]    findAll()
 * @method GWAS[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GWASRepository extends ServiceEntityRepository
{
    private $swRepo;

    public function __construct(ManagerRegistry $registry, SharedWithRepository $swRepo)
    {
        $this->swRepo = $swRepo;
        parent::__construct($registry, GWAS::class);
    }

    // to download publicly release trial associated data
    public function getPublicReleasedData()
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('gwas')
            ->from('App\Entity\Study', 'st')    
            ->from('App\Entity\Trial', 'tr')
            ->where('st MEMBER OF gwas.studyList')
            ->andWhere('st.trial = tr.id')
            ->andWhere('tr.publicReleaseDate <= :currentDate')
            ->setParameter(':currentDate', $currentDate)
        ;
        return $query->getQuery()->getResult();
    }

    public function findReleasedTrialStudyGWAS($user = null)
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('gwas')
            ->from('App\Entity\Study', 'st')    
            ->from('App\Entity\Trial', 'tr')
            ->where('gwas.isActive = 1')
            ->andWhere('gwas.studyList = st.id')
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
                            'gwas.studyList = st.id',
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
                            'gwas.studyList = st.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate',
                            'sw.user = :user',
                            'sw.trial = tr.id',
                            ))
                    ->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'gwas.studyList = st.id',
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
    //  * @return GWAS[] Returns an array of GWAS objects
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
    public function findOneBySomeField($value): ?GWAS
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
