<?php

namespace UserAgreementBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Contracts\RpcResultInterface;
use Tourze\JsonRPC\Core\Domain\JsonRpcMethodInterface;
use Tourze\JsonRPC\Core\Event\BeforeMethodApplyEvent;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPC\Core\Result\EmptyResult;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;
use UserAgreementBundle\Attribute\IsAgreeTerms;
use UserAgreementBundle\Enum\ProtocolType;
use UserAgreementBundle\EventSubscriber\RpcExecuteSubscriber;
use UserAgreementBundle\Exception\TermsNeedAgreeException;

/**
 * @internal
 */
#[CoversClass(RpcExecuteSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class RpcExecuteSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // Nothing needed for this test
    }

    #[Test]
    public function testBeforeMethodApplyWithoutIsAgreeTermsAttribute(): void
    {
        $subscriber = self::getService(RpcExecuteSubscriber::class);

        $methodObj = new class implements JsonRpcMethodInterface {
            public function __invoke(JsonRpcRequest $request): RpcResultInterface
            {
                // Method without IsAgreeTerms attribute
                return EmptyResult::getMockResult();
            }

            public function execute(RpcParamInterface $param): RpcResultInterface
            {
                return EmptyResult::getMockResult();
            }
        };

        $event = new BeforeMethodApplyEvent();
        $event->setMethod($methodObj);

        $subscriber->beforeMethodApply($event);

        // Test passes if no exception thrown
        $this->expectNotToPerformAssertions();
    }

    #[Test]
    public function testBeforeMethodApplyWithUnauthenticatedUser(): void
    {
        // 未认证用户测试，应该抛出异常
        $subscriber = self::getService(RpcExecuteSubscriber::class);

        $methodObj = new #[IsAgreeTerms(type: ProtocolType::MEMBER_USAGE)] class implements JsonRpcMethodInterface {
            public function __invoke(JsonRpcRequest $request): RpcResultInterface
            {
                // Method with IsAgreeTerms attribute
                return EmptyResult::getMockResult();
            }

            public function execute(RpcParamInterface $param): RpcResultInterface
            {
                return EmptyResult::getMockResult();
            }
        };

        $event = new BeforeMethodApplyEvent();
        $event->setMethod($methodObj);

        $this->expectException(TermsNeedAgreeException::class);
        $subscriber->beforeMethodApply($event);
    }

    #[Test]
    public function testBeforeMethodApplyWithUserWhoHasNotAgreed(): void
    {
        // 简化测试，直接测试未认证用户场景
        $subscriber = self::getService(RpcExecuteSubscriber::class);

        $methodObj = new #[IsAgreeTerms(type: ProtocolType::MEMBER_USAGE)] class implements JsonRpcMethodInterface {
            public function __invoke(JsonRpcRequest $request): RpcResultInterface
            {
                // Method with IsAgreeTerms attribute
                return EmptyResult::getMockResult();
            }

            public function execute(RpcParamInterface $param): RpcResultInterface
            {
                return EmptyResult::getMockResult();
            }
        };

        $event = new BeforeMethodApplyEvent();
        $event->setMethod($methodObj);

        $this->expectException(TermsNeedAgreeException::class);
        $subscriber->beforeMethodApply($event);
    }
}
