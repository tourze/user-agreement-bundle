<?php

declare(strict_types=1);

namespace UserAgreementBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Param\ApiGetSystemProtocolContentParam;

/**
 * @internal
 */
#[CoversClass(ApiGetSystemProtocolContentParam::class)]
final class ApiGetSystemProtocolContentParamTest extends TestCase
{
    #[Test]
    public function testImplementsRpcParamInterface(): void
    {
        $param = new ApiGetSystemProtocolContentParam();
        $this->assertInstanceOf(
            \Tourze\JsonRPC\Core\Contracts\RpcParamInterface::class,
            $param
        );
    }

    #[Test]
    public function testTypePropertyExists(): void
    {
        $param = new ApiGetSystemProtocolContentParam();

        // 测试属性存在
        $this->assertObjectHasProperty('type', $param);

        // 测试属性类型为string
        $reflection = new \ReflectionClass($param);
        $typeProperty = $reflection->getProperty('type');
        $type = $typeProperty->getType();
  $this->assertInstanceOf(\ReflectionNamedType::class, $type);
  $this->assertEquals('string', $type->getName());
        $this->assertFalse($typeProperty->getType()->allowsNull());
    }

    #[Test]
    #[TestWith(['privacy'])]
    #[TestWith(['member_register'])]
    #[TestWith(['member_usage'])]
    #[TestWith(['sale-push'])]
    public function testTypePropertyAssignment(string $testValue): void
    {
        $param = new ApiGetSystemProtocolContentParam();
        $param->type = $testValue;

        $this->assertEquals($testValue, $param->type);
        $this->assertIsString($param->type);
    }

    #[Test]
    public function testMethodParamAttribute(): void
    {
        $reflection = new \ReflectionClass(ApiGetSystemProtocolContentParam::class);
        $typeProperty = $reflection->getProperty('type');

        // 检查 MethodParam 属性
        $attributes = $typeProperty->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
        $this->assertCount(1, $attributes);

        $attribute = $attributes[0]->newInstance();
        $this->assertEquals('协议类型', $attribute->description);
    }

    #[Test]
    public function testAssertNotBlankAttribute(): void
    {
        $reflection = new \ReflectionClass(ApiGetSystemProtocolContentParam::class);
        $typeProperty = $reflection->getProperty('type');

        // 检查 Assert\NotBlank 属性
        $attributes = $typeProperty->getAttributes(\Symfony\Component\Validator\Constraints\NotBlank::class);
        $this->assertCount(1, $attributes);

        $attribute = $attributes[0]->newInstance();
        $this->assertEquals('协议类型不能为空', $attribute->message);
    }

    #[Test]
    public function testCanInstantiate(): void
    {
        $param = new ApiGetSystemProtocolContentParam();
        $this->assertInstanceOf(ApiGetSystemProtocolContentParam::class, $param);
    }

    #[Test]
    public function testCanBeUsedWithKnownProtocolTypes(): void
    {
        // 这个测试验证 Param 可以与实际的协议类型值配合使用
        $param = new ApiGetSystemProtocolContentParam();

        // 使用已知的协议类型
        $validTypes = ['member_register', 'member_usage', 'privacy', 'sale-push'];

        foreach ($validTypes as $type) {
            $param->type = $type;
            $this->assertEquals($type, $param->type);
            $this->assertIsString($param->type);
        }
    }
}