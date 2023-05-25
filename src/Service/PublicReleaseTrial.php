<?php

namespace App\Service;
use Symfony\Component\Security\Core\Security;
use App\Repository\TrialRepository;
use App\Repository\SharedWithRepository;


class PublicReleaseTrial
{
    private $trialRepo;
    private $swRepo;
    private $security;

    function __construct(TrialRepository $trialRepo, SharedWithRepository $swRepo, Security $security) {
        $this->trialRepo = $trialRepo;
        $this->swRepo = $swRepo;
        $this->security = $security;
    }

    function getPublicReleaseTrials() {
        $user = $this->security->getUser();
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->trialRepo->createQueryBuilder('tr')
            ->Where('tr.isActive = 1')
            ->andWhere('tr.publicReleaseDate <= :currentDate')
            ->setParameter(':currentDate', $currentDate)
        ;

        if ($user) {
            if ($this->swRepo->totalRows($user) == 0) {
                $query->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'tr.publicReleaseDate >= :currentDate'))
                        ->setParameter(':user', $user->getId())
                        ->setParameter(':currentDate', $currentDate);
            }
            if ($this->swRepo->totalRows($user) > 0) {
                $query->from('App\Entity\SharedWith', 'sw')
                    ->orWhere(
                        $query->expr()->andX(
                            'tr.publicReleaseDate >= :currentDate',
                            'sw.user = :user',
                            'sw.trial = tr.id',
                            ))
                    ->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'tr.publicReleaseDate >= :currentDate'))
                        ->setParameter(':user', $user->getId())
                        ->setParameter(':currentDate', $currentDate);
            }
        }
        return $query;
    }

    function getOwnedTrials() {
        $user = $this->security->getUser();
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->trialRepo->createQueryBuilder('tr')
                ->from('App\Entity\User', 'u')
                ->Where('tr.isActive = 1')
                ->andWhere('tr.publicReleaseDate > :currentDate')
                ->andWhere('tr.createdBy = :userId')
                ->setParameter(':currentDate', $currentDate)
                ->setParameter(':userId', $user->getId());
            
        return $query;
    }
}