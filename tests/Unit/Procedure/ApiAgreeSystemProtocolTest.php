<?php

namespace UserAgreementBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineUpsertBundle\Service\UpsertManager;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Event\AgreeProtocolEvent;
use UserAgreementBundle\Procedure\ApiAgreeSystemProtocol;
use UserAgreementBundle\Repository\AgreeLogRepository;
use UserAgreementBundle\Repository\ProtocolEntityRepository;

class ApiAgreeSystemProtocolTest extends TestCase
{
    private ApiAgreeSystemProtocol $procedure;
    private ProtocolEntityRepository $protocolEntityRepository;
    private AgreeLogRepository $agreeLogRepository;
    private UpsertManager $upsertManager;
    private Security $security;
    private EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        $this->protocolEntityRepository = $this->createMock(ProtocolEntityRepository::class);
        $this->agreeLogRepository = $this->createMock(AgreeLogRepository::class);
        $this->upsertManager = $this->createMock(UpsertManager::class);
        $this->security = $this->createMock(Security::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        
        $this->procedure = new ApiAgreeSystemProtocol(
            $this->protocolEntityRepository,
            $this->agreeLogRepository,
            $this->upsertManager,
            $this->security,
            $this->eventDispatcher
        );
    }

    /**
     * @test
     */
    public function testExecuteWithNonExistentProtocol(): void
    {
        $this->procedure->id = 'non-existent-id';
        
        $user = $this->createMock(UserInterface::class);
        $this->security->method('getUser')->willReturn($user);
        
        $this->protocolEntityRepository->method('findOneBy')
            ->with([
                'id' => 'non-existent-id',
                'valid' => true,
            ])
            ->willReturn(null);
        
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到协议');
        
        $this->procedure->execute();
    }

    /**
     * @test
     */
    public function testExecuteWithExistingAgreeLog(): void
    {
        $this->procedure->id = 'protocol-id';
        
        $user = new class implements UserInterface {
            public function getRoles(): array { return []; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'user@test.com'; }
            public function getId(): string { return 'user-id'; }
        };
        $this->security->method('getUser')->willReturn($user);
        
        $protocol = $this->createMock(ProtocolEntity::class);
        $protocol->method('getId')->willReturn('protocol-id');
        
        $this->protocolEntityRepository->method('findOneBy')
            ->with([
                'id' => 'protocol-id',
                'valid' => true,
            ])
            ->willReturn($protocol);
        
        $existingLog = $this->createMock(AgreeLog::class);
        $this->agreeLogRepository->method('findOneBy')
            ->with([
                'memberId' => 'user-id',
                'protocolId' => 'protocol-id',
            ])
            ->willReturn($existingLog);
        
        // 应该不会创建新的日志或触发事件
        $this->upsertManager->expects($this->never())->method('upsert');
        $this->eventDispatcher->expects($this->never())->method('dispatch');
        
        $result = $this->procedure->execute();
        
        $this->assertEquals(['__message' => '已同意'], $result);
    }

    /**
     * @test
     */
    public function testExecuteWithNewAgreeLog(): void
    {
        $this->procedure->id = 'protocol-id';
        
        $user = new class implements UserInterface {
            public function getRoles(): array { return []; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'user@example.com'; }
            public function getId(): string { return 'user-id'; }
        };
        $this->security->method('getUser')->willReturn($user);
        
        $protocol = $this->createMock(ProtocolEntity::class);
        $protocol->method('getId')->willReturn('protocol-id');
        $protocol->method('__toString')->willReturn('用户协议v1.0');
        
        $this->protocolEntityRepository->method('findOneBy')
            ->with([
                'id' => 'protocol-id',
                'valid' => true,
            ])
            ->willReturn($protocol);
        
        $this->agreeLogRepository->method('findOneBy')
            ->with([
                'memberId' => 'user-id',
                'protocolId' => 'protocol-id',
            ])
            ->willReturn(null);
        
        $newLog = $this->createMock(AgreeLog::class);
        $this->upsertManager->method('upsert')
            ->willReturn($newLog);
        
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(AgreeProtocolEvent::class));
        
        $result = $this->procedure->execute();
        
        $this->assertEquals(['__message' => '已同意'], $result);
    }

    /**
     * @test
     */
    public function testExtendsLockableProcedure(): void
    {
        $this->assertInstanceOf(
            \Tourze\JsonRPCLockBundle\Procedure\LockableProcedure::class,
            $this->procedure
        );
    }
}