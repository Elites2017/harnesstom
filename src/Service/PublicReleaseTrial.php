<?php

namespace App\Service;
use Symfony\Component\Security\Core\Security;
use App\Repository\TrialRepository;


class PublicReleaseTrial
{
    private $trialRepo;
    private $security;

    function __construct(TrialRepository $trialRepo, Security $security) {
        $this->trialRepo = $trialRepo;
        $this->security = $security;
    }

    function getPublicReleaseTrials() {
        $user = $this->security->getUser();
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->trialRepo->createQueryBuilder('tr')
                ->from('App\Entity\User', 'u')
                ->Where('tr.isActive = 1')
                ->andWhere('tr.publicReleaseDate <= :currentDate')
                ->setParameter(':currentDate', $currentDate);

                if ($user) {
                    $query->orWhere(
                        $query->expr()->orX(
                                'tr.createdBy = :userId'))
                            ->setParameter(':userId', $user->getId());
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