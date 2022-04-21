<?php

namespace App\Security\Voter;

use App\Entity\SharedWith;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SharedWithVoter extends Voter
{
    public const SHARED_WITH_EDIT = 'shared_with_edit';
    public const SHARED_WITH_DEL = 'shared_with_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $sharedWith): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::SHARED_WITH_EDIT, self::SHARED_WITH_DEL])
            && $sharedWith instanceof \App\Entity\SharedWith;
    }

    protected function voteOnAttribute(string $attribute, $sharedWith, TokenInterface $token): bool
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
        if (null === $sharedWith->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::SHARED_WITH_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($sharedWith, $user);
                break;
            case self::SHARED_WITH_DEL:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($sharedWith, $user);
                break;
        }

        return false;
    }

     // private methods
     private function canEdit(SharedWith $sharedWith, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $sharedWith->getCreatedBy();
    }

    private function canDelete(SharedWith $sharedWith, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $sharedWith->getCreatedBy();  
    }
}