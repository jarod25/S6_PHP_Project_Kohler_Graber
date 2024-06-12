<?php

namespace App\Security\Voter;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class EventVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Event $event */
        $event = $subject;

        return match ($attribute) {
            self::EDIT => $this->canEdit($event, $user),
            self::DELETE => $this->canDelete($event, $user),
            default => false,
        };

    }

    private function canEdit(Event $event, User $user): bool
    {
        return $event->getOwner() === $user;
    }

    private function canDelete(Event $event, User $user): bool
    {
        return $event->getOwner() === $user;
    }
}
