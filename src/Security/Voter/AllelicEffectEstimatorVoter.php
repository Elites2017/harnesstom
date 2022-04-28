<?php

namespace App\Security\Voter;

use App\Entity\AllelicEffectEstimator;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AllelicEffectEstimatorVoter extends Voter
{
    public const ALLELIC_EFFECT_ESTIMATOR_EDIT = 'allelic_effect_estimator_edit';
    public const ALLELIC_EFFECT_ESTIMATOR_DEL = 'allelic_effect_estimator_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $allelicEffectEstimator): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ALLELIC_EFFECT_ESTIMATOR_EDIT, self::ALLELIC_EFFECT_ESTIMATOR_DEL])
            && $allelicEffectEstimator instanceof \App\Entity\AllelicEffectEstimator;
    }

    protected function voteOnAttribute(string $attribute, $allelicEffectEstimator, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if the user is admin, grant access
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // if the object doesn't have an owner
        if (null === $allelicEffectEstimator->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ALLELIC_EFFECT_ESTIMATOR_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($allelicEffectEstimator, $user);
                break;
            case self::ALLELIC_EFFECT_ESTIMATOR_DEL:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($allelicEffectEstimator, $user);
                break;
        }

        return false;
    }

     // private methods
     private function canEdit(AllelicEffectEstimator $allelicEffectEstimator, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $allelicEffectEstimator->getCreatedBy();
    }

    private function canDelete(AllelicEffectEstimator $allelicEffectEstimator, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $allelicEffectEstimator->getCreatedBy();  
    }
}
