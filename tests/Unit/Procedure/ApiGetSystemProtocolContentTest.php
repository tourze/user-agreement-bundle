<?php

namespace UserAgreementBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Enum\ProtocolType;
use UserAgreementBundle\Procedure\ApiGetSystemProtocolContent;
use UserAgreementBundle\Repository\AgreeLogRepository;
use UserAgreementBundle\Repository\ProtocolEntityRepository;

class ApiGetSystemProtocolContentTest extends TestCase
{
    private ApiGetSystemProtocolContent $procedure;
    private Security $security;
    private ProtocolEntityRepository $protocolEntityRepository;
    private AgreeLogRepository $agreeLogRepository;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->protocolEntityRepository = $this->createMock(ProtocolEntityRepository::class);
        $this->agreeLogRepository = $this->createMock(AgreeLogRepository::class);
        
        $this->procedure = new ApiGetSystemProtocolContent(
            $this->security,
            $this->protocolEntityRepository,
            $this->agreeLogRepository
        );
    }

    /**
     * @test
     */
    public function testExecuteWithoutUser(): void
    {
        $this->procedure->type = 'member_register';
        
        $this->security->method('getUser')->willReturn(null);
        
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('未登录用户不需要同意协议');
        
        $this->procedure->execute();
    }

    /**
     * @test
     */
    public function testExecuteWithInvalidProtocolType(): void
    {
        $this->procedure->type = 'INVALID_TYPE';
        
        $user = $this->createMock(UserInterface::class);
        $this->security->method('getUser')->willReturn($user);
        
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到指定协议类型');
        
        $this->procedure->execute();
    }

    /**
     * @test
     */
    public function testExecuteWithNoProtocolFound(): void
    {
        $this->procedure->type = 'member_register';
        
        $user = $this->createMock(UserInterface::class);
        $this->security->method('getUser')->willReturn($user);
        
        $this->protocolEntityRepository->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ], ['id' => 'DESC'])
            ->willReturn(null);
        
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到最新协议[1]');
        
        $this->procedure->execute();
    }

    /**
     * @test
     */
    public function testExecuteWithProtocolNotAgreed(): void
    {
        $this->procedure->type = 'member_register';
        
        $user = new class implements UserInterface {
            public function getRoles(): array { return []; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'user@test.com'; }
            public function getId(): string { return 'user-id'; }
        };
        $this->security->method('getUser')->willReturn($user);
        
        $protocol = $this->createMock(ProtocolEntity::class);
        $protocol->method('getId')->willReturn('protocol-id');
        $protocol->method('retrieveApiArray')->willReturn([
            'id' => 'protocol-id',
            'title' => '用户协议',
            'content' => '协议内容',
        ]);
        
        $this->protocolEntityRepository->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ], ['id' => 'DESC'])
            ->willReturn($protocol);
        
        $this->agreeLogRepository->method('findOneBy')
            ->with([
                'protocolId' => 'protocol-id',
                'memberId' => 'user-id',
            ])
            ->willReturn(null);
        
        $result = $this->procedure->execute();
        
        $this->assertEquals([
            'id' => 'protocol-id',
            'title' => '用户协议',
            'content' => '协议内容',
            'has_agree' => false,
        ], $result);
    }

    /**
     * @test
     */
    public function testExecuteWithProtocolAgreed(): void
    {
        $this->procedure->type = 'member_register';
        
        $user = new class implements UserInterface {
            public function getRoles(): array { return []; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'user@test.com'; }
            public function getId(): string { return 'user-id'; }
        };
        $this->security->method('getUser')->willReturn($user);
        
        $protocol = $this->createMock(ProtocolEntity::class);
        $protocol->method('getId')->willReturn('protocol-id');
        $protocol->method('retrieveApiArray')->willReturn([
            'id' => 'protocol-id',
            'title' => '用户协议',
            'content' => '协议内容',
        ]);
        
        $this->protocolEntityRepository->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ], ['id' => 'DESC'])
            ->willReturn($protocol);
        
        $agreeLog = new class {
            public function getId(): string { return 'log-id'; }
        };
        
        $this->agreeLogRepository->method('findOneBy')
            ->with([
                'protocolId' => 'protocol-id',
                'memberId' => 'user-id',
            ])
            ->willReturn($agreeLog);
        
        $result = $this->procedure->execute();
        
        $this->assertEquals([
            'id' => 'protocol-id',
            'title' => '用户协议',
            'content' => '协议内容',
            'has_agree' => true,
        ], $result);
    }

    /**
     * @test
     */
    public function testExecuteWithUserWithoutGetIdMethod(): void
    {
        $this->procedure->type = 'member_register';
        
        // 创建一个没有 getId 方法的用户模拟
        $user = new class implements UserInterface {
            public function getRoles(): array { return []; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'user@test.com'; }
        };
        $this->security->method('getUser')->willReturn($user);
        
        $protocol = $this->createMock(ProtocolEntity::class);
        $protocol->method('getId')->willReturn('protocol-id');
        $protocol->method('retrieveApiArray')->willReturn([
            'id' => 'protocol-id',
            'title' => '用户协议',
            'content' => '协议内容',
        ]);
        
        $this->protocolEntityRepository->method('findOneBy')
            ->with([
                'type' => ProtocolType::MEMBER_REGISTER,
                'valid' => true,
            ], ['id' => 'DESC'])
            ->willReturn($protocol);
        
        $this->agreeLogRepository->method('findOneBy')
            ->with([
                'protocolId' => 'protocol-id',
                'memberId' => '',
            ])
            ->willReturn(null);
        
        $result = $this->procedure->execute();
        
        $this->assertEquals([
            'id' => 'protocol-id',
            'title' => '用户协议',
            'content' => '协议内容',
            'has_agree' => false,
        ], $result);
    }
}