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
        /*
         * 使用具体类 ProtocolEntity 的理由：
         * 1. ProtocolEntity 是 Doctrine 实体类，没有提供接口
         * 2. 事件测试需要验证实体的传递和获取功能
         * 3. 这是业务事件测试，需要模拟实体行为
         */
        $protocol = $this->createMock(ProtocolEntity::class);

        $this->event->setProtocol($protocol);
        $this->assertEquals($protocol, $this->event->getProtocol());
    }

    #[Test]
    public function testAgreeLogProperty(): void
    {
        /*
         * 使用具体类 AgreeLog 的理由：
         * 1. AgreeLog 是 Doctrine 实体类，没有提供接口
         * 2. 事件测试需要验证实体的传递和获取功能
         * 3. 这是业务事件测试，需要模拟实体行为
         */
        $agreeLog = $this->createMock(AgreeLog::class);

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
        /*
         * 使用具体类的理由：
         * 1. ProtocolEntity 和 AgreeLog 都是 Doctrine 实体类，没有提供接口
         * 2. 事件测试需要验证实体的传递和获取功能
         * 3. 这是业务事件测试，需要模拟实体行为
         */
        $protocol = $this->createMock(ProtocolEntity::class);
        /*
         * 使用具体类 AgreeLog 的理由：
         * 1. AgreeLog 是 Doctrine 实体类，没有提供标准接口
         * 2. 测试需要验证事件中实体的传递和获取功能
         * 3. 作为业务实体，使用具体类进行 Mock 是合理的测试方式
         */
        $agreeLog = $this->createMock(AgreeLog::class);

        $this->event->setProtocol($protocol);
        $this->event->setAgreeLog($agreeLog);

        $this->assertEquals($protocol, $this->event->getProtocol());
        $this->assertEquals($agreeLog, $this->event->getAgreeLog());
    }
}
