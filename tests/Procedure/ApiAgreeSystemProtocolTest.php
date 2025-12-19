<?php

namespace UserAgreementBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
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

        // 创建参数对象，提供必需的协议ID
        $param = new \UserAgreementBundle\Param\ApiAgreeSystemProtocolParam();
        $param->id = 'test-protocol-id';

        $procedure->execute($param);
    }
}
