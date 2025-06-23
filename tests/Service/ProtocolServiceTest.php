<?php

namespace UserAgreementBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Enum\ProtocolType;
use UserAgreementBundle\Repository\AgreeLogRepository;
use UserAgreementBundle\Repository\ProtocolEntityRepository;
use UserAgreementBundle\Service\ProtocolService;

/**
 * 用于测试的模拟用户类
 */
class MockUser implements UserInterface
{
    private string $id;
    private string $identifier;

    public function __construct(string $id = '123456789')
    {
        $this->id = $id;
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
        // 无需实现
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }
}

class ProtocolServiceTest extends TestCase
{
    private ProtocolService $protocolService;
    private MockObject $protocolEntityRepository;
    private MockObject $agreeLogRepository;
    private MockObject $logger;
    private MockObject $entityManager;

    protected function setUp(): void
    {
        $this->protocolEntityRepository = $this->createMock(ProtocolEntityRepository::class);
        $this->agreeLogRepository = $this->createMock(AgreeLogRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->protocolService = new ProtocolService(
            $this->protocolEntityRepository,
            $this->agreeLogRepository,
            $this->logger,
            $this->entityManager
        );
    }
    
    /**
     * 创建模拟协议
     */
    private function createMockProtocol(string $protocolId = '987654321'): MockObject
    {
        $mockProtocol = $this->getMockBuilder(ProtocolEntity::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();
            
        $mockProtocol->method('getId')->willReturn($protocolId);
        
        return $mockProtocol;
    }

    /**
     * @test
     */
    public function testCheckAgree_withValidAgreement(): void
    {
        // 创建模拟对象
        $userId = '123456789';
        $protocolId = '987654321';
        $mockUser = new MockUser($userId);
        $mockProtocol = $this->createMockProtocol($protocolId);

        // 创建模拟同意日志
        $mockAgreeLog = $this->createMock(AgreeLog::class);
        $mockAgreeLog->method('isValid')->willReturn(true);

        // 设置仓库行为
        $this->protocolEntityRepository
            ->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ])
            ->willReturn($mockProtocol);

        $this->agreeLogRepository
            ->method('findOneBy')
            ->with([
                'protocolId' => $protocolId,
                'memberId' => $userId,
            ])
            ->willReturn($mockAgreeLog);

        // 执行测试
        $result = $this->protocolService->checkAgree($mockUser, ProtocolType::MEMBER_REGISTER);

        // 断言
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function testCheckAgree_withInvalidAgreement(): void
    {
        // 创建模拟对象
        $userId = '123456789';
        $protocolId = '987654321';
        $mockUser = new MockUser($userId);
        $mockProtocol = $this->createMockProtocol($protocolId);

        // 创建模拟同意日志 - 但设置为无效
        $mockAgreeLog = $this->createMock(AgreeLog::class);
        $mockAgreeLog->method('isValid')->willReturn(false);

        // 设置仓库行为
        $this->protocolEntityRepository
            ->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ])
            ->willReturn($mockProtocol);

        $this->agreeLogRepository
            ->method('findOneBy')
            ->with([
                'protocolId' => $protocolId,
                'memberId' => $userId,
            ])
            ->willReturn($mockAgreeLog);

        // 执行测试
        $result = $this->protocolService->checkAgree($mockUser, ProtocolType::MEMBER_REGISTER);

        // 断言
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function testCheckAgree_withNoAgreement(): void
    {
        // 创建模拟对象
        $userId = '123456789';
        $protocolId = '987654321';
        $mockUser = new MockUser($userId);
        $mockProtocol = $this->createMockProtocol($protocolId);

        // 设置仓库行为
        $this->protocolEntityRepository
            ->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ])
            ->willReturn($mockProtocol);

        $this->agreeLogRepository
            ->method('findOneBy')
            ->with([
                'protocolId' => $protocolId,
                'memberId' => $userId,
            ])
            ->willReturn(null); // 没有找到协议记录

        // 执行测试
        $result = $this->protocolService->checkAgree($mockUser, ProtocolType::MEMBER_REGISTER);

        // 断言
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function testCheckAgree_withNoProtocol(): void
    {
        // 创建模拟用户
        $mockUser = new MockUser();

        // 设置仓库行为
        $this->protocolEntityRepository
            ->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ])
            ->willReturn(null); // 没有找到协议

        // 执行测试
        $result = $this->protocolService->checkAgree($mockUser, ProtocolType::MEMBER_REGISTER);

        // 断言
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function testAutoAgree_createsNewLogWhenNotExists(): void
    {
        // 创建模拟对象
        $userId = '123456789';
        $protocolId = '987654321';
        $mockUser = new MockUser($userId);
        $mockProtocol = $this->createMockProtocol($protocolId);

        // 设置仓库行为
        $this->protocolEntityRepository
            ->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ], ['id' => 'DESC'])
            ->willReturn($mockProtocol);

        $this->agreeLogRepository
            ->method('findOneBy')
            ->with([
                'protocolId' => $protocolId,
                'memberId' => $userId,
            ])
            ->willReturn(null); // 不存在同意日志

        // 验证EntityManager行为
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($entity) use ($userId, $protocolId) {
                return $entity instanceof AgreeLog
                    && $entity->getMemberId() === $userId
                    && $entity->getProtocolId() === $protocolId
                    && $entity->isValid() === true;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // 执行测试
        $this->protocolService->autoAgree($mockUser, ProtocolType::MEMBER_REGISTER);
    }

    /**
     * @test
     */
    public function testAutoAgree_updatesExistingLog(): void
    {
        // 创建模拟对象
        $userId = '123456789';
        $protocolId = '987654321';
        $mockUser = new MockUser($userId);
        $mockProtocol = $this->createMockProtocol($protocolId);

        // 创建现有同意日志
        $mockAgreeLog = $this->getMockBuilder(AgreeLog::class)
            ->onlyMethods(['isValid', 'setValid'])
            ->getMock();
        $mockAgreeLog->method('isValid')->willReturn(false); // 当前是不同意状态
        $mockAgreeLog->expects($this->once())
            ->method('setValid')
            ->with(true); // 应该被设置为同意状态

        // 设置仓库行为
        $this->protocolEntityRepository
            ->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ], ['id' => 'DESC'])
            ->willReturn($mockProtocol);

        $this->agreeLogRepository
            ->method('findOneBy')
            ->with([
                'protocolId' => $protocolId,
                'memberId' => $userId,
            ])
            ->willReturn($mockAgreeLog);

        // 验证EntityManager行为
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($mockAgreeLog);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // 执行测试
        $this->protocolService->autoAgree($mockUser, ProtocolType::MEMBER_REGISTER);
    }

    /**
     * @test
     */
    public function testAutoAgree_doesNotUpdateWhenStateIsUnchanged(): void
    {
        // 创建模拟对象
        $userId = '123456789';
        $protocolId = '987654321';
        $mockUser = new MockUser($userId);
        $mockProtocol = $this->createMockProtocol($protocolId);

        // 创建现有同意日志，且状态已经是同意
        $mockAgreeLog = $this->getMockBuilder(AgreeLog::class)
            ->onlyMethods(['isValid', 'setValid'])
            ->getMock();
        $mockAgreeLog->method('isValid')->willReturn(true); // 已同意状态
        $mockAgreeLog->expects($this->never())->method('setValid'); // 不应该调用setValid

        // 设置仓库行为
        $this->protocolEntityRepository
            ->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ], ['id' => 'DESC'])
            ->willReturn($mockProtocol);

        $this->agreeLogRepository
            ->method('findOneBy')
            ->with([
                'protocolId' => $protocolId,
                'memberId' => $userId,
            ])
            ->willReturn($mockAgreeLog);

        // EntityManager不应该被调用
        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        // 执行测试
        $this->protocolService->autoAgree($mockUser, ProtocolType::MEMBER_REGISTER);
    }

    /**
     * @test
     */
    public function testAutoAgree_handlesNoProtocolFound(): void
    {
        // 创建模拟用户
        $mockUser = new MockUser();

        // 设置仓库行为，找不到协议
        $this->protocolEntityRepository
            ->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ], ['id' => 'DESC'])
            ->willReturn(null);

        // 应该记录错误日志
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with(
                '找不到指定的协议',
                ['type' => ProtocolType::MEMBER_REGISTER]
            );

        // EntityManager不应该被调用
        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        // 执行测试
        $this->protocolService->autoAgree($mockUser, ProtocolType::MEMBER_REGISTER);
    }

    /**
     * @test
     */
    public function testAutoAgree_withDisagreeFlag(): void
    {
        // 创建模拟对象
        $userId = '123456789';
        $protocolId = '987654321';
        $mockUser = new MockUser($userId);
        $mockProtocol = $this->createMockProtocol($protocolId);

        // 设置仓库行为
        $this->protocolEntityRepository
            ->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ], ['id' => 'DESC'])
            ->willReturn($mockProtocol);

        $this->agreeLogRepository
            ->method('findOneBy')
            ->with([
                'protocolId' => $protocolId,
                'memberId' => $userId,
            ])
            ->willReturn(null); // 不存在同意日志

        // 验证EntityManager行为
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($entity) use ($userId, $protocolId) {
                return $entity instanceof AgreeLog
                    && $entity->getMemberId() === $userId
                    && $entity->getProtocolId() === $protocolId
                    && $entity->isValid() === false; // 此处传入false表示不同意
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // 执行测试
        $this->protocolService->autoAgree($mockUser, ProtocolType::MEMBER_REGISTER, false);
    }
} 