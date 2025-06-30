<?php

namespace UserAgreementBundle\Tests\Unit\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use UserAgreementBundle\EventSubscriber\RpcExecuteSubscriber;
use UserAgreementBundle\Service\ProtocolService;

class RpcExecuteSubscriberTest extends TestCase
{
    private RpcExecuteSubscriber $subscriber;
    private Security $security;
    private ProtocolService $protocolService;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->protocolService = $this->createMock(ProtocolService::class);
        
        $this->subscriber = new RpcExecuteSubscriber(
            $this->security,
            $this->protocolService
        );
    }

    /**
     * @test
     */
    public function testConstructorInitializesCorrectly(): void
    {
        $this->assertInstanceOf(RpcExecuteSubscriber::class, $this->subscriber);
    }

    /**
     * @test
     */
    public function testHasBeforeMethodApplyMethod(): void
    {
        $reflection = new \ReflectionClass(RpcExecuteSubscriber::class);
        $this->assertTrue(
            $reflection->hasMethod('beforeMethodApply')
        );
    }
}