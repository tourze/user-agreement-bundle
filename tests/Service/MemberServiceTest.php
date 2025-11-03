<?php

namespace UserAgreementBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use UserAgreementBundle\Service\MemberService;

/**
 * @internal
 */
#[CoversClass(MemberService::class)]
#[RunTestsInSeparateProcesses]
final class MemberServiceTest extends AbstractIntegrationTestCase
{
    private MemberService $memberService;

    protected function onSetUp(): void
    {
        $this->memberService = self::getService(MemberService::class);
    }

    #[Test]
    public function testExtractMemberIdWithGetIdMethod(): void
    {
        $user = $this->createNormalUser('fallback@example.com');

        // User implementation has getId() method that returns integer ID
        $memberId = $this->memberService->extractMemberId($user);

        // Since user has getId() method, it should return the string version of ID
        $this->assertIsNumeric($memberId);

        // Verify user implements UserInterface and has getId method
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue(method_exists($user, 'getId'), 'User must have getId() method');

        // Use reflection to safely access getId() method
        $reflection = new \ReflectionMethod($user, 'getId');
        $userId = $reflection->invoke($user);
        $this->assertEquals((string) $userId, $memberId);
    }

    #[Test]
    public function testExtractMemberIdFallsBackToUserIdentifier(): void
    {
        // Create a mock user without getId method to test fallback
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user@example.com');

        $memberId = $this->memberService->extractMemberId($user);
        $this->assertEquals('user@example.com', $memberId);
    }

    #[Test]
    public function testExtractMemberIdWithNullId(): void
    {
        $user = $this->createNormalUser('testuser');
        // Force ID to null to test edge case
        $reflection = new \ReflectionClass($user);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($user, null);

        $memberId = $this->memberService->extractMemberId($user);
        // Method exists but ID is null, so it returns empty string
        $this->assertEquals('', $memberId);
    }

    #[Test]
    public function testExtractMemberIdWithZeroId(): void
    {
        $user = $this->createNormalUser('testuser');
        // Force ID to 0 to test edge case
        $reflection = new \ReflectionClass($user);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($user, 0);

        $memberId = $this->memberService->extractMemberId($user);
        // Should return '0' when ID is 0
        $this->assertEquals('0', $memberId);
    }
}
