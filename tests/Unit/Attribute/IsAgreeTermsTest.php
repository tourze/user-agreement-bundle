<?php

namespace UserAgreementBundle\Tests\Unit\Attribute;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Attribute\IsAgreeTerms;
use UserAgreementBundle\Enum\ProtocolType;

class IsAgreeTermsTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructor(): void
    {
        $type = ProtocolType::MEMBER_REGISTER;
        $attribute = new IsAgreeTerms($type);
        
        $this->assertEquals($type, $attribute->type);
    }

    /**
     * @test
     */
    public function testWithDifferentProtocolTypes(): void
    {
        foreach (ProtocolType::cases() as $protocolType) {
            $attribute = new IsAgreeTerms($protocolType);
            $this->assertEquals($protocolType, $attribute->type);
        }
    }

    /**
     * @test
     */
    public function testAttributeReflection(): void
    {
        $reflectionClass = new \ReflectionClass(IsAgreeTerms::class);
        $attributes = $reflectionClass->getAttributes(\Attribute::class);
        
        $this->assertCount(1, $attributes);
        $attributeInstance = $attributes[0]->newInstance();
        
        $this->assertEquals(\Attribute::TARGET_CLASS, $attributeInstance->flags);
    }
}