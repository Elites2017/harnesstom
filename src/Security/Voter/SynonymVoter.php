<?php

namespace App\Security\Voter;

use App\Entity\Synonym;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SynonymVoter extends Voter
{
    public const SYNONYM_EDIT = 'synonym_edit';
    public const SYNONYM_DEL = 'synonym_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $synonym): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::SYNONYM_EDIT, self::SYNONYM_DEL])
            && $synonym instanceof \App\Entity\Synonym;
    }

    protected function voteOnAttribute(string $attribute, $synonym, TokenInterface $token): bool
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
        if (null === $synonym->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::SYNONYM_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($synonym, $user);
                break;
            case self::SYNONYM_DEL:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($synonym, $user);
                break;
        }

        return false;
    }

     // private methods
     private function canEdit(Synonym $synonym, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $synonym->getCreatedBy();
    }

    private function canDelete(Synonym $synonym, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $synonym->getCreatedBy();  
    }
}