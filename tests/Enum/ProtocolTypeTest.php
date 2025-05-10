<?php

namespace UserAgreementBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Enum\ProtocolType;

class ProtocolTypeTest extends TestCase
{
    /**
     * @test
     */
    public function testValues(): void
    {
        // 测试枚举值
        $this->assertEquals('member_register', ProtocolType::MEMBER_REGISTER->value);
        $this->assertEquals('member_usage', ProtocolType::MEMBER_USAGE->value);
        $this->assertEquals('privacy', ProtocolType::PRIVACY->value);
        $this->assertEquals('sale-push', ProtocolType::SALE_PUSH->value);
    }

    /**
     * @test
     */
    public function testGetLabel(): void
    {
        // 测试标签文本
        $this->assertEquals('用户注册协议', ProtocolType::MEMBER_REGISTER->getLabel());
        $this->assertEquals('用户使用协议', ProtocolType::MEMBER_USAGE->getLabel());
        $this->assertEquals('隐私协议', ProtocolType::PRIVACY->getLabel());
        $this->assertEquals('营销推送', ProtocolType::SALE_PUSH->getLabel());
    }

    /**
     * @test
     */
    public function testItemTrait(): void
    {
        // 验证ItemTrait相关功能
        $item = ProtocolType::MEMBER_REGISTER;
        $this->assertEquals('用户注册协议', $item->getLabel());

        // 使用反射计算枚举值的数量
        $enumValues = ProtocolType::cases();
        $this->assertCount(4, $enumValues);
    }

    /**
     * @test
     */
    public function testSelectTrait(): void
    {
        // 验证SelectTrait相关功能
        $item = ProtocolType::MEMBER_REGISTER;
        $this->assertIsArray($item->toArray());
        $this->assertEquals([
            'value' => 'member_register',
            'label' => '用户注册协议'
        ], $item->toArray());
    }
}
