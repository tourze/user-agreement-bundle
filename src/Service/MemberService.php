<?php

namespace UserAgreementBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;

class MemberService
{
    public function extractMemberId(UserInterface $user): string
    {
        if (method_exists($user, 'getId')) {
            return (string) $user->getId();
        }

        return $user->getUserIdentifier();
    }
}
