<?php

namespace App\Service;
use Symfony\Component\Security\Core\Security;
use App\Repository\TrialRepository;
use App\Repository\SharedWithRepository;
use App\Repository\StudyRepository;

class PublicReleaseTrial
{
    private $trialRepo;
    private $swRepo;
    private $studyRepo;
    private $security;

    function __construct(TrialRepository $trialRepo, SharedWithRepository $swRepo, StudyRepository $studyRepo, Security $security) {
        $this->trialRepo = $trialRepo;
        $this->swRepo = $swRepo;
        $this->studyRepo = $studyRepo;
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

    // the list of study available in the create forms
    // e.g cross create / update
    function getVisibleStudies() {
        $user = $this->security->getUser();
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->studyRepo->createQueryBuilder('st')
            ->from('App\Entity\Trial', 'tr')
            ->Where('st.isActive = 1')
            ->AndWhere('st.trial = tr.id')
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
        //dd($query->getDQL());
        return $query;
    }
}