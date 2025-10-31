<?php

namespace UserAgreementBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use UserAgreementBundle\Enum\ProtocolType;

/**
 * @internal
 */
#[CoversClass(ProtocolType::class)]
final class ProtocolTypeTest extends AbstractEnumTestCase
{
    #[Test]
    #[TestWith([ProtocolType::MEMBER_REGISTER, 'member_register', '用户注册协议'])]
    #[TestWith([ProtocolType::MEMBER_USAGE, 'member_usage', '用户使用协议'])]
    #[TestWith([ProtocolType::PRIVACY, 'privacy', '隐私协议'])]
    #[TestWith([ProtocolType::SALE_PUSH, 'sale-push', '营销推送'])]
    public function testEnumValueAndLabel(ProtocolType $enum, string $expectedValue, string $expectedLabel): void
    {
        $this->assertEquals($expectedValue, $enum->value);
        $this->assertEquals($expectedLabel, $enum->getLabel());
    }

    #[Test]
    public function testToArray(): void
    {
        $item = ProtocolType::MEMBER_REGISTER;
        $this->assertEquals([
            'value' => 'member_register',
            'label' => '用户注册协议',
        ], $item->toArray());
    }

    #[Test]
    public function testGenOptions(): void
    {
        $options = ProtocolType::genOptions();
        $this->assertCount(4, $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
        }
    }
}
