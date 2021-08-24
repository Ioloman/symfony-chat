<?php

namespace App\Security\Voter;

use App\Entity\Chatroom;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChatroomVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['CHAT_AUTH'])
            && $subject instanceof Chatroom;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var Chatroom $subject */

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        switch ($attribute) {
            case 'CHAT_AUTH':
                if ($subject->getUsers()->contains($user)) {
                    return true;
                }
                return false;
            case 'POST_VIEW':
                return false;
        }

        return false;
    }
}
