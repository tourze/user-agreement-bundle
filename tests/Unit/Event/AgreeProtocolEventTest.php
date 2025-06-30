<?php

namespace UserAgreementBundle\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Event\AgreeProtocolEvent;

class AgreeProtocolEventTest extends TestCase
{
    private AgreeProtocolEvent $event;

    protected function setUp(): void
    {
        $this->event = new AgreeProtocolEvent();
    }

    /**
     * @test
     */
    public function testProtocolProperty(): void
    {
        $protocol = $this->createMock(ProtocolEntity::class);
        
        $this->event->setProtocol($protocol);
        $this->assertEquals($protocol, $this->event->getProtocol());
    }

    /**
     * @test
     */
    public function testAgreeLogProperty(): void
    {
        $agreeLog = $this->createMock(AgreeLog::class);
        
        $this->event->setAgreeLog($agreeLog);
        $this->assertEquals($agreeLog, $this->event->getAgreeLog());
    }

    /**
     * @test
     */
    public function testExtendsUserInteractionEvent(): void
    {
        $this->assertInstanceOf(
            \Tourze\UserEventBundle\Event\UserInteractionEvent::class,
            $this->event
        );
    }

    /**
     * @test
     */
    public function testSetAndGetBothProperties(): void
    {
        $protocol = $this->createMock(ProtocolEntity::class);
        $agreeLog = $this->createMock(AgreeLog::class);
        
        $this->event->setProtocol($protocol);
        $this->event->setAgreeLog($agreeLog);
        
        $this->assertEquals($protocol, $this->event->getProtocol());
        $this->assertEquals($agreeLog, $this->event->getAgreeLog());
    }
}