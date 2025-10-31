<?php

namespace UserAgreementBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Enum\ProtocolType;
use UserAgreementBundle\Service\ProtocolService;

/**
 * @internal
 */
#[CoversClass(ProtocolService::class)]
#[RunTestsInSeparateProcesses]
final class ProtocolServiceTest extends AbstractIntegrationTestCase
{
    private ProtocolService $protocolService;

    protected function onSetUp(): void
    {
        $protocolService = self::getContainer()->get(ProtocolService::class);
        self::assertInstanceOf(ProtocolService::class, $protocolService);
        $this->protocolService = $protocolService;
    }

    #[Test]
    public function testServiceIsAvailable(): void
    {
        $this->assertInstanceOf(ProtocolService::class, $this->protocolService);
    }

    #[Test]
    public function testCheckAgreeWithNoProtocol(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password');

        $result = $this->protocolService->checkAgree($user, ProtocolType::MEMBER_REGISTER);

        $this->assertFalse($result);
    }

    #[Test]
    public function testAutoAgreeWithNoProtocol(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password');

        // 创建一个协议实体以测试upsert功能
        $protocolEntity = new ProtocolEntity();
        $protocolEntity->setType(ProtocolType::MEMBER_REGISTER);
        $protocolEntity->setTitle('Test Protocol');
        $protocolEntity->setVersion('1.0.0');
        $protocolEntity->setContent('Test protocol content');
        $protocolEntity->setValid(true);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($protocolEntity);
        $entityManager->flush();

        // 现在测试autoAgree方法，应该成功执行upsert不抛出SQLite ON CONFLICT异常
        $this->protocolService->autoAgree($user, ProtocolType::MEMBER_REGISTER);

        // 验证用户已经同意该协议
        $result = $this->protocolService->checkAgree($user, ProtocolType::MEMBER_REGISTER);
        $this->assertTrue($result, 'User should have agreed to the protocol after autoAgree');
    }
}
