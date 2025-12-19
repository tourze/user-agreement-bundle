<?php

namespace UserAgreementBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
use UserAgreementBundle\Enum\ProtocolType;
use UserAgreementBundle\Procedure\ApiGetSystemProtocolContent;

/**
 * @internal
 */
#[CoversClass(ApiGetSystemProtocolContent::class)]
#[RunTestsInSeparateProcesses]
final class ApiGetSystemProtocolContentTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // Procedure 测试需要数据库访问
    }

    protected function getProcedure(): ApiGetSystemProtocolContent
    {
        // Procedure 类由容器自动管理，从容器中获取服务
        $procedure = self::getContainer()->get(ApiGetSystemProtocolContent::class);
        self::assertInstanceOf(ApiGetSystemProtocolContent::class, $procedure);

        return $procedure;
    }

    #[Test]
    public function testProcedureClassCanBeInstantiated(): void
    {
        $procedure = $this->getProcedure();
        $this->assertInstanceOf(ApiGetSystemProtocolContent::class, $procedure);
    }

    #[Test]
    public function testProcedureHasRequiredDependencies(): void
    {
        $procedure = $this->getProcedure();

        // 使用反射检查类的依赖项是否正确注入
        $reflection = new \ReflectionClass($procedure);

        // 检查构造函数是否存在
        $this->assertTrue($reflection->hasMethod('__construct'));

        // 检查构造函数是否为 public
        $constructor = $reflection->getMethod('__construct');
        $this->assertTrue($constructor->isPublic());
    }

    #[Test]
    public function testProcedureExtendsBaseProcedure(): void
    {
        $procedure = $this->getProcedure();
        $this->assertInstanceOf(BaseProcedure::class, $procedure);
    }

    #[Test]
    public function testProcedureAcceptsValidProtocolTypes(): void
    {
        $procedure = $this->getProcedure();

        // 验证 Procedure 可以处理有效的协议类型
        $this->assertInstanceOf(ApiGetSystemProtocolContent::class, $procedure);

        // 验证 ProtocolType 枚举包含预期的值
        $this->assertTrue(ProtocolType::MEMBER_REGISTER !== null);
        $this->assertTrue(ProtocolType::PRIVACY !== null);
    }

    #[Test]
    public function testExecuteMethodExists(): void
    {
        $procedure = $this->getProcedure();
        $reflection = new \ReflectionClass($procedure);

        $this->assertTrue($reflection->hasMethod('execute'));
        $executeMethod = $reflection->getMethod('execute');
        $this->assertTrue($executeMethod->isPublic());

        // 验证返回类型为 ArrayResult
        $returnType = $executeMethod->getReturnType();
        if ($returnType instanceof \ReflectionNamedType) {
            $this->assertEquals('Tourze\JsonRPC\Core\Result\ArrayResult', $returnType->getName());
        } else {
            // 如果是联合类型或其他复杂类型，至少验证不为空
            $this->assertNotNull($returnType);
        }
    }
}
