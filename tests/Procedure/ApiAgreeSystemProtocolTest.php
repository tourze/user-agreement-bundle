<?php

namespace UserAgreementBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use UserAgreementBundle\Procedure\ApiAgreeSystemProtocol;

/**
 * @internal
 */
#[CoversClass(ApiAgreeSystemProtocol::class)]
#[RunTestsInSeparateProcesses]
final class ApiAgreeSystemProtocolTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 使用真实数据库，不使用 Mock
    }

    #[Test]
    public function testExtendsLockableProcedure(): void
    {
        $procedure = self::getService(ApiAgreeSystemProtocol::class);
        $this->assertInstanceOf(LockableProcedure::class, $procedure);
    }

    #[Test]
    public function testExecuteRequiresAuthentication(): void
    {
        $procedure = self::getService(ApiAgreeSystemProtocol::class);

        // 测试未认证的情况，应该抛出ApiException
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('用户未登录');

        $procedure->execute();
    }
}
