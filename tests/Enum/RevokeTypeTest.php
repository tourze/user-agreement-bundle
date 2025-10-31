<?php

namespace UserAgreementBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use UserAgreementBundle\Enum\RevokeType;

/**
 * @internal
 */
#[CoversClass(RevokeType::class)]
final class RevokeTypeTest extends AbstractEnumTestCase
{
    #[Test]
    public function testEnumCases(): void
    {
        $this->assertEquals('1', RevokeType::All->value);
        $this->assertEquals('2', RevokeType::NO_NOTIFY->value);
        $this->assertEquals('3', RevokeType::NOTIFY->value);
    }

    #[Test]
    public function testGetLabel(): void
    {
        $this->assertEquals('全渠道注销', RevokeType::All->getLabel());
        $this->assertEquals('不接收通知', RevokeType::NO_NOTIFY->getLabel());
        $this->assertEquals('接收通知', RevokeType::NOTIFY->getLabel());
    }

    #[Test]
    public function testAllCasesHaveLabels(): void
    {
        foreach (RevokeType::cases() as $case) {
            $label = $case->getLabel();
            $this->assertNotEmpty($label);
        }
    }

    #[Test]
    public function testEnumImplementsInterfaces(): void
    {
        $this->assertInstanceOf(Labelable::class, RevokeType::All);
        $this->assertInstanceOf(Itemable::class, RevokeType::All);
        $this->assertInstanceOf(Selectable::class, RevokeType::All);
    }

    #[Test]
    public function testToArray(): void
    {
        $item = RevokeType::All;
        $this->assertEquals([
            'value' => '1',
            'label' => '全渠道注销',
        ], $item->toArray());
    }

    #[Test]
    public function testGenOptions(): void
    {
        $options = RevokeType::genOptions();
        $this->assertCount(3, $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
        }
    }
}
