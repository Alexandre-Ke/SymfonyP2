<?php

namespace App\Security\Voter;

use App\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProductVoter extends Voter
{
    public const ADD = 'PRODUCT_ADD';
    public const EDIT = 'PRODUCT_EDIT';
    public const DELETE = 'PRODUCT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::ADD, self::EDIT, self::DELETE])
            && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur n'est pas connecté, on refuse l'accès
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Vérification des permissions en fonction de l'action
        switch ($attribute) {
            case self::ADD:
                return $this->canADD($user);

            case self::EDIT:
                return $this->canEdit($user);

            case self::DELETE:
                return $this->canDelete($user);
        }

        return false;
    }

    private function canADD(UserInterface $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    private function canEdit(UserInterface $user): bool
    {
        // Seuls les administrateurs peuvent modifier un produit
        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    private function canDelete(UserInterface $user): bool
    {
        // Seuls les administrateurs peuvent supprimer un produit
        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
