<?php

namespace UserAgreementBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;
use Tourze\UserEventBundle\Event\UserInteractionEvent;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Event\AgreeProtocolEvent;

/**
 * @internal
 */
#[CoversClass(AgreeProtocolEvent::class)]
final class AgreeProtocolEventTest extends AbstractEventTestCase
{
    private AgreeProtocolEvent $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = new AgreeProtocolEvent();
    }

    #[Test]
    public function testProtocolProperty(): void
    {
        // 使用真实的 ProtocolEntity 对象而不是 Mock
        $protocol = new ProtocolEntity();
        $protocol->setTitle('Test Protocol');
        $protocol->setVersion('1.0');

        $this->event->setProtocol($protocol);
        $this->assertEquals($protocol, $this->event->getProtocol());
    }

    #[Test]
    public function testAgreeLogProperty(): void
    {
        // 使用真实的 AgreeLog 对象而不是 Mock
        $agreeLog = new AgreeLog();
        $agreeLog->setProtocolId('protocol-123');
        $agreeLog->setMemberId('member-456');

        $this->event->setAgreeLog($agreeLog);
        $this->assertEquals($agreeLog, $this->event->getAgreeLog());
    }

    #[Test]
    public function testExtendsUserInteractionEvent(): void
    {
        $this->assertInstanceOf(
            UserInteractionEvent::class,
            $this->event
        );
    }

    #[Test]
    public function testSetAndGetBothProperties(): void
    {
        // 使用真实的实体对象而不是 Mock
        $protocol = new ProtocolEntity();
        $protocol->setTitle('Test Protocol');
        $protocol->setVersion('1.0');

        $agreeLog = new AgreeLog();
        $agreeLog->setProtocolId('protocol-123');
        $agreeLog->setMemberId('member-456');

        $this->event->setProtocol($protocol);
        $this->event->setAgreeLog($agreeLog);

        $this->assertEquals($protocol, $this->event->getProtocol());
        $this->assertEquals($agreeLog, $this->event->getAgreeLog());
    }
}
