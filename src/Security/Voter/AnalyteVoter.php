<?php

namespace App\Security\Voter;

use App\Entity\Analyte;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AnalyteVoter extends Voter
{
    public const ANALYTE_EDIT = 'analyte_edit';
    public const ANALYTE_DEL = 'analyte_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $analyte): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ANALYTE_EDIT, self::ANALYTE_DEL])
            && $analyte instanceof \App\Entity\Analyte;
    }

    protected function voteOnAttribute(string $attribute, $analyte, TokenInterface $token): bool
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
        if (null === $analyte->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ANALYTE_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($analyte, $user);
                break;
            case self::ANALYTE_DEL:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($analyte, $user);
                break;
        }

        return false;
    }

     // private methods
     private function canEdit(Analyte $analyte, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $analyte->getCreatedBy();
    }

    private function canDelete(Analyte $analyte, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $analyte->getCreatedBy();  
    }
}