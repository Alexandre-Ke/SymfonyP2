<?php

namespace App\Security\Voter;

use App\Entity\Client;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientVoter extends Voter
{
    public const VIEW = 'CLIENT_VIEW';
    public const EDIT = 'CLIENT_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT]) && $subject instanceof Client;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
                return in_array('ROLE_ADMIN', $user->getRoles(), true) || in_array('ROLE_MANAGER', $user->getRoles(), true);
        }

        return false;
    }
}
