<?php

namespace UserAgreementBundle\Tests\Attribute;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Attribute\IsAgreeTerms;
use UserAgreementBundle\Enum\ProtocolType;

/**
 * @internal
 */
#[CoversClass(IsAgreeTerms::class)]
final class IsAgreeTermsTest extends TestCase
{
    #[Test]
    public function testConstructor(): void
    {
        $type = ProtocolType::MEMBER_REGISTER;
        $attribute = new IsAgreeTerms($type);

        $this->assertEquals($type, $attribute->type);
    }

    #[Test]
    public function testWithDifferentProtocolTypes(): void
    {
        foreach (ProtocolType::cases() as $protocolType) {
            $attribute = new IsAgreeTerms($protocolType);
            $this->assertEquals($protocolType, $attribute->type);
        }
    }

    #[Test]
    public function testAttributeReflection(): void
    {
        $reflectionClass = new \ReflectionClass(IsAgreeTerms::class);
        $attributes = $reflectionClass->getAttributes(\Attribute::class);

        $this->assertCount(1, $attributes);
        $attributeInstance = $attributes[0]->newInstance();

        $this->assertEquals(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD, $attributeInstance->flags);
    }
}
