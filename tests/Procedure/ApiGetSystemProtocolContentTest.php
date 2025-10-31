<?php

namespace UserAgreementBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
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
    public function testProcedureHasRequiredProperties(): void
    {
        $procedure = $this->getProcedure();

        // 使用反射检查类的属性
        $reflection = new \ReflectionClass($procedure);

        // 检查是否存在 type 属性
        $this->assertTrue($reflection->hasProperty('type'));

        // 检查属性是否为 public
        $typeProperty = $reflection->getProperty('type');
        $this->assertTrue($typeProperty->isPublic());
    }

    #[Test]
    public function testProcedureExtendsBaseProcedure(): void
    {
        $procedure = $this->getProcedure();
        $this->assertInstanceOf(BaseProcedure::class, $procedure);
    }

    #[Test]
    public function testProcedureTypePropertyAcceptsValidValues(): void
    {
        $procedure = $this->getProcedure();

        // 测试设置有效的协议类型
        $procedure->type = ProtocolType::MEMBER_REGISTER->value;
        $this->assertEquals(ProtocolType::MEMBER_REGISTER->value, $procedure->type);

        $procedure->type = ProtocolType::PRIVACY->value;
        $this->assertEquals(ProtocolType::PRIVACY->value, $procedure->type);
    }

    #[Test]
    public function testExecuteMethodExists(): void
    {
        $procedure = $this->getProcedure();
        $reflection = new \ReflectionClass($procedure);

        $this->assertTrue($reflection->hasMethod('execute'));
        $executeMethod = $reflection->getMethod('execute');
        $this->assertTrue($executeMethod->isPublic());
        $returnType = $executeMethod->getReturnType();
        if ($returnType instanceof \ReflectionNamedType) {
            $this->assertEquals('array', $returnType->getName());
        } else {
            // 如果是联合类型或其他复杂类型，至少验证不为空
            $this->assertNotNull($returnType);
        }
    }
}
