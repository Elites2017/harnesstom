<?php

namespace App\Security\Voter;

use App\Entity\Germplasm;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class GermplasmVoter extends Voter
{
    public const GERMPLASM_EDIT = 'germplasm_edit';
    public const GERMPLASM_DEL = 'germplasm_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $germplasm): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::GERMPLASM_EDIT, self::GERMPLASM_DEL])
            && $germplasm instanceof \App\Entity\Germplasm;
    }

    protected function voteOnAttribute(string $attribute, $germplasm, TokenInterface $token): bool
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
        if (null === $germplasm->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::GERMPLASM_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($germplasm, $user);
                break;
            case self::GERMPLASM_DEL:
                // logic to determine if the user can GERMPLASM_DEL
                // return true or false
                return $this->canDelete($germplasm, $user);
                break;
        }

        return false;
    }
    // private methods
    private function canEdit(Germplasm $germplasm, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $germplasm->getCreatedBy();
    }

    private function canDelete(Germplasm $germplasm, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $germplasm->getCreatedBy();  
    }
}
