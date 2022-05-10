<?php

namespace App\Security\Voter;

use App\Entity\QTLStudy;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class QTLStudyVoter extends Voter
{
    public const EDIT = 'qtl_study_edit';
    public const DEL = 'qtl_study_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $qtlStudy): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DEL])
            && $qtlStudy instanceof \App\Entity\QTLStudy;
    }

    protected function voteOnAttribute(string $attribute, $qtlStudy, TokenInterface $token): bool
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
        if (null === $qtlStudy->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($qtlStudy, $user);
                break;
            case self::DEL:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($qtlStudy, $user);
                break;
        }

        return false;
    }

     // private methods
     private function canEdit(QTLStudy $qtlStudy, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $qtlStudy->getCreatedBy();
    }

    private function canDelete(QTLStudy $qtlStudy, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $qtlStudy->getCreatedBy();  
    }
}
