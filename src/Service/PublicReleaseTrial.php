<?php

namespace App\Service;

use App\Repository\CrossRepository;
use App\Repository\SampleRepository;
use Symfony\Component\Security\Core\Security;
use App\Repository\TrialRepository;
use App\Repository\SharedWithRepository;
use App\Repository\StudyRepository;

class PublicReleaseTrial
{
    private $trialRepo;
    private $swRepo;
    private $studyRepo;
    private $crossRepo;
    private $sampleRepo;
    private $security;

    function __construct(TrialRepository $trialRepo, SharedWithRepository $swRepo, StudyRepository $studyRepo,
                        CrossRepository $crossRepo, SampleRepository $sampleRepo, Security $security) {
        $this->trialRepo = $trialRepo;
        $this->swRepo = $swRepo;
        $this->studyRepo = $studyRepo;
        $this->crossRepo = $crossRepo;
        $this->sampleRepo = $sampleRepo;
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
        return $query;
    }

    function getVisibleCrosses() {
        $user = $this->security->getUser();
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->crossRepo->createQueryBuilder('cr')
            ->from('App\Entity\Study', 'st')
            ->from('App\Entity\Trial', 'tr')
            ->Where('cr.isActive = 1')
            ->AndWhere('cr.study = st.id')
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
        return $query;
    }

    // sample
    function getVisibleSamples() {
        $user = $this->security->getUser();
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->sampleRepo->createQueryBuilder('spl')
            ->from('App\Entity\Study', 'st')
            ->from('App\Entity\Trial', 'tr')
            ->Where('spl.isActive = 1')
            ->AndWhere('spl.study = st.id')
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
        return $query;
    }
}
