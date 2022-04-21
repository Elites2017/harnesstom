<?php

namespace App\Security\Voter;

use App\Entity\Study;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class StudyVoter extends Voter
{
    public const STUDY_EDIT = 'study_edit';
    public const STUDY_DEL = 'study_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $study): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::STUDY_EDIT, self::STUDY_DEL])
            && $study instanceof \App\Entity\Study;
    }

    protected function voteOnAttribute(string $attribute, $study, TokenInterface $token): bool
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
        if (null === $study->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::STUDY_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($study, $user);
                break;
            case self::STUDY_DEL:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($study, $user);
                break;
        }

        return false;
    }

     // private methods
     private function canEdit(Study $study, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $study->getCreatedBy();
    }

    private function canDelete(Study $study, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $study->getCreatedBy();  
    }
}
