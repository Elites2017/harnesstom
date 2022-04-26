<?php

namespace App\Security\Voter;

use App\Entity\AnatomicalEntity;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AnatomicalEntityVoter extends Voter
{
    public const ANATOMICAL_ENTITY_EDIT = 'anatomical_entity_edit';
    public const ANATOMICAL_ENTITY_DEL = 'anatomical_entity_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $anatomicalEntity): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ANATOMICAL_ENTITY_EDIT, self::ANATOMICAL_ENTITY_DEL])
            && $anatomicalEntity instanceof \App\Entity\AnatomicalEntity;
    }

    protected function voteOnAttribute(string $attribute, $anatomicalEntity, TokenInterface $token): bool
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
        if (null === $anatomicalEntity->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ANATOMICAL_ENTITY_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($anatomicalEntity, $user);
                break;
            case self::ANATOMICAL_ENTITY_DEL:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($anatomicalEntity, $user);
                break;
        }

        return false;
    }

     // private methods
     private function canEdit(AnatomicalEntity $anatomicalEntity, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $anatomicalEntity->getCreatedBy();
    }

    private function canDelete(AnatomicalEntity $anatomicalEntity, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $anatomicalEntity->getCreatedBy();  
    }
}
