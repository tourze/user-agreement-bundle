<?php

namespace UserAgreementBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Enum\RevokeType;

class RevokeTypeTest extends TestCase
{
    /**
     * @test
     */
    public function testEnumCases(): void
    {
        $this->assertEquals('1', RevokeType::All->value);
        $this->assertEquals('2', RevokeType::NO_NOTIFY->value);
        $this->assertEquals('3', RevokeType::NOTIFY->value);
    }

    /**
     * @test
     */
    public function testGetLabel(): void
    {
        $this->assertEquals('全渠道注销', RevokeType::All->getLabel());
        $this->assertEquals('不接收通知', RevokeType::NO_NOTIFY->getLabel());
        $this->assertEquals('接收通知', RevokeType::NOTIFY->getLabel());
    }

    /**
     * @test
     */
    public function testAllCasesHaveLabels(): void
    {
        foreach (RevokeType::cases() as $case) {
            $label = $case->getLabel();
            $this->assertNotEmpty($label);
        }
    }

    /**
     * @test
     */
    public function testEnumImplementsInterfaces(): void
    {
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, RevokeType::All);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, RevokeType::All);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, RevokeType::All);
    }

    /**
     * @test  
     */
    public function testTraitAvailability(): void
    {
        // 测试枚举是否使用了期望的 trait
        $reflection = new \ReflectionClass(RevokeType::class);
        $traits = $reflection->getTraitNames();
        
        $this->assertContains('Tourze\EnumExtra\ItemTrait', $traits);
        $this->assertContains('Tourze\EnumExtra\SelectTrait', $traits);
    }
}