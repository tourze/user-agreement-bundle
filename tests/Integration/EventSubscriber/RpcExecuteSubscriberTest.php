<?php

namespace UserAgreementBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use UserAgreementBundle\EventSubscriber\RpcExecuteSubscriber;
use UserAgreementBundle\Service\ProtocolService;

class RpcExecuteSubscriberTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructor(): void
    {
        $security = $this->createMock(Security::class);
        $protocolService = $this->createMock(ProtocolService::class);
        
        $subscriber = new RpcExecuteSubscriber($security, $protocolService);
        
        $this->assertInstanceOf(RpcExecuteSubscriber::class, $subscriber);
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