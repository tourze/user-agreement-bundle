<?php

declare(strict_types=1);

namespace UserAgreementBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Param\ApiAgreeSystemProtocolParam;

/**
 * @internal
 */
#[CoversClass(ApiAgreeSystemProtocolParam::class)]
final class ApiAgreeSystemProtocolParamTest extends TestCase
{
    #[Test]
    public function testImplementsRpcParamInterface(): void
    {
        $param = new ApiAgreeSystemProtocolParam();
        $this->assertInstanceOf(
            \Tourze\JsonRPC\Core\Contracts\RpcParamInterface::class,
            $param
        );
    }

    #[Test]
    public function testIdPropertyExists(): void
    {
        $param = new ApiAgreeSystemProtocolParam();

        // 测试属性存在
        $this->assertObjectHasProperty('id', $param);

        // 测试属性类型为string
        $reflection = new \ReflectionClass($param);
        $idProperty = $reflection->getProperty('id');
        $type = $idProperty->getType();
  $this->assertInstanceOf(\ReflectionNamedType::class, $type);
  $this->assertEquals('string', $type->getName());
        $this->assertFalse($idProperty->getType()->allowsNull());
    }

    #[Test]
    #[TestWith(['protocol-123'])]
    #[TestWith(['abc-def-ghi'])]
    #[TestWith(['123456'])]
    public function testIdPropertyAssignment(string $testValue): void
    {
        $param = new ApiAgreeSystemProtocolParam();
        $param->id = $testValue;

        $this->assertEquals($testValue, $param->id);
        $this->assertIsString($param->id);
    }

    #[Test]
    public function testMethodParamAttribute(): void
    {
        $reflection = new \ReflectionClass(ApiAgreeSystemProtocolParam::class);
        $idProperty = $reflection->getProperty('id');

        // 检查 MethodParam 属性
        $attributes = $idProperty->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
        $this->assertCount(1, $attributes);

        $attribute = $attributes[0]->newInstance();
        $this->assertEquals('协议ID', $attribute->description);
    }

    #[Test]
    public function testAssertNotBlankAttribute(): void
    {
        $reflection = new \ReflectionClass(ApiAgreeSystemProtocolParam::class);
        $idProperty = $reflection->getProperty('id');

        // 检查 Assert\NotBlank 属性
        $attributes = $idProperty->getAttributes(\Symfony\Component\Validator\Constraints\NotBlank::class);
        $this->assertCount(1, $attributes);

        $attribute = $attributes[0]->newInstance();
        $this->assertEquals('协议ID不能为空', $attribute->message);
    }

    #[Test]
    public function testCanInstantiate(): void
    {
        $param = new ApiAgreeSystemProtocolParam();
        $this->assertInstanceOf(ApiAgreeSystemProtocolParam::class, $param);
    }

    }