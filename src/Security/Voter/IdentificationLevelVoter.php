<?php

namespace App\Security\Voter;

use App\Entity\IdentificationLevel;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class IdentificationLevelVoter extends Voter
{
    public const EDIT = 'identification_level_edit';
    public const DEL = 'identification_level_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $identificationLevel): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DEL])
            && $identificationLevel instanceof \App\Entity\IdentificationLevel;
    }

    protected function voteOnAttribute(string $attribute, $identificationLevel, TokenInterface $token): bool
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
        if (null === $identificationLevel->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($identificationLevel, $user);
                break;
            case self::DEL:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($identificationLevel, $user);
                break;
        }

        return false;
    }

     // private methods
     private function canEdit(IdentificationLevel $identificationLevel, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $identificationLevel->getCreatedBy();
    }

    private function canDelete(IdentificationLevel $identificationLevel, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $identificationLevel->getCreatedBy();  
    }
}
