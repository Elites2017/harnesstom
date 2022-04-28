<?php

namespace App\Security\Voter;

use App\Entity\Accession;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessionVoter extends Voter
{
    public const ACCESSION_EDIT = 'accession_edit';
    public const ACCESSION_DEL = 'accession_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $accession): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ACCESSION_EDIT, self::ACCESSION_DEL])
            && $accession instanceof \App\Entity\Accession;
    }

    protected function voteOnAttribute(string $attribute, $accession, TokenInterface $token): bool
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
        if (null === $accession->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ACCESSION_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($accession, $user);
                break;
            case self::ACCESSION_DEL:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($accession, $user);
                break;
        }

        return false;
    }

     // private methods
     private function canEdit(Accession $accession, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $accession->getCreatedBy();
    }

    private function canDelete(Accession $accession, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $accession->getCreatedBy();  
    }
}
