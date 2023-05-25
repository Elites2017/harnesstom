<?php

namespace App\Repository;

use App\Entity\Trial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trial|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trial|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trial[]    findAll()
 * @method Trial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrialRepository extends ServiceEntityRepository
{
    private $swRepo;
    public function __construct(ManagerRegistry $registry, SharedWithRepository $swRepo)
    {
        $this->swRepo = $swRepo;
        parent::__construct($registry, Trial::class);
    }

    public function totalRows() {
        return $this->createQueryBuilder('tab')
            ->select('count(tab.id)')
            ->where('tab.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findReleasedTrials($user = null)
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('tr')
            ->Where('tr.isActive = 1')
            ->andWhere('tr.publicReleaseDate <= :currentDate')
            ->setParameter(':currentDate', $currentDate)
        ;

        if ($user) {
            if ($this->swRepo->totalRows($user) === 0) {
                $query->orWhere(
                        $query->expr()->orX(
                            'tr.createdBy = :user'))
                        ->setParameter(':user', $user->getId());
            }
            
            if ($this->swRepo->totalRows($user) > 0) {
                $query->from('App\Entity\SharedWith', 'sw')
                    ->orWhere(
                        $query->expr()->orX(
                            'tr.createdBy = :user',
                            'sw.user = :user'))
                        ->setParameter(':user', $user->getId());
            }
        }

        return $query->getQuery()->getResult();
    }

    public function isAccessible($user = null, $trial)
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('tr')
            ->Where('tr.isActive = 1')
            ->andWhere('tr.id = :trial')
            ->setParameter(':trial', $trial->getId())
        ;

        $foundTrial = $query->getQuery()->getResult();
        if($foundTrial[0]) {
            $foundTrial = $foundTrial[0];
            if ($foundTrial->getPublicReleaseDate() <= $currentDate) {
                return true;
            } else if ($foundTrial->getCreatedBy() === $user) {
                return true;
            } else if (count($this->swRepo->findBy(["trial" => $trial, "user" => $user])) === 1) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // /**
    //  * @return Trial[] Returns an array of Trial objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Trial
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
