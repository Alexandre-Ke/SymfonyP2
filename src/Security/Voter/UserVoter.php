<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserVoter extends Voter
{
    public const EDIT = 'edit';
    public const EDIT_ROLE = 'editRole';
    public const VIEW = 'view';
    public const DELETE = 'delete';
    public const CREATE = 'create';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::EDIT_ROLE, self::VIEW, self::DELETE, self::CREATE])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Restrictions spécifiques aux utilisateurs
        switch ($attribute) {
            case self::VIEW:
                return $user === $subject; // Un utilisateur peut voir son propre profil
            case self::EDIT:
                return $user === $subject; // Un utilisateur peut modifier son propre profil
            case self::EDIT_ROLE:
                return false;
            case self::DELETE:
                return false; // Un utilisateur ne peut pas se supprimer
            case self::CREATE:
                return false; // Seul un admin peut créer un utilisateur
        }

        return false;
    }
}
