<?php

namespace UserAgreementBundle\Tests\Service;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 用于测试的模拟用户类
 */
class MockUser implements UserInterface
{
    /**
     * @var non-empty-string
     */
    private string $identifier;

    public function __construct(private readonly string $id = '123456789')
    {
        $this->identifier = 'user_' . $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }
}
