<?php

namespace App\Security\Voter;

use App\Entity\Person;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class PersonVoter extends Voter
{
    public const EDIT = 'person_edit';
    public const DEL = 'person_del';

    // to get the user roles as suggested by symfony
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $person): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DEL])
            && $person instanceof \App\Entity\Person;
    }

    protected function voteOnAttribute(string $attribute, $person, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // check if user is admin
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($person, $user);
                break;
            case self::DEL:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($person, $user);
                break;
        }

        return false;
    }

    // private methods
    private function canEdit(Person $person, User $user){
        // if the connected user is the created of the person object, they can modify it
        return $user === $person->getCreatedBy();
    }

    private function canDelete(Person $person, User $user){
        // if the connected user is the created of the person object, they can modify it
        return $user === $person->getCreatedBy();  
    }
}
