<?php

namespace App\Security\Voter;

use App\Entity\AttributeTraitValue;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AttributeTraitValueVoter extends Voter
{
    public const ATTRIBUTE_TRAIT_VALUE_EDIT = 'attribute_trait_value_edit';
    public const ATTRIBUTE_TRAIT_VALUE_DEL = 'attribute_trait_value_delete';
    // to get the user role
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $attributeTraitValue): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ATTRIBUTE_TRAIT_VALUE_EDIT, self::ATTRIBUTE_TRAIT_VALUE_DEL])
            && $attributeTraitValue instanceof \App\Entity\AttributeTraitValue;
    }

    protected function voteOnAttribute(string $attribute, $attributeTraitValue, TokenInterface $token): bool
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
        if (null === $attributeTraitValue->getCreatedBy()) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ATTRIBUTE_TRAIT_VALUE_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($attributeTraitValue, $user);
                break;
            case self::ATTRIBUTE_TRAIT_VALUE_DEL:
                // logic to determine if the user can attributeTraitValue_DEL
                // return true or false
                return $this->canDelete($attributeTraitValue, $user);
                break;
        }

        return false;
    }
    // private methods
    private function canEdit(AttributeTraitValue $attributeTraitValue, User $user){
        // if the connected user is the owner of the object, they can modify it
        return $user === $attributeTraitValue->getCreatedBy();
    }

    private function canDelete(AttributeTraitValue $attributeTraitValue, User $user){
        // if the connected user is the owner of the object, they can delete it
        return $user === $attributeTraitValue->getCreatedBy();  
    }
}
