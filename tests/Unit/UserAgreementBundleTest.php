<?php

namespace UserAgreementBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\UserAgreementBundle;

class UserAgreementBundleTest extends TestCase
{
    private UserAgreementBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new UserAgreementBundle();
    }

    /**
     * @test
     */
    public function testExtendsBundle(): void
    {
        $this->assertInstanceOf(
            \Symfony\Component\HttpKernel\Bundle\Bundle::class,
            $this->bundle
        );
    }

    /**
     * @test
     */
    public function testImplementsBundleDependencyInterface(): void
    {
        $this->assertInstanceOf(
            \Tourze\BundleDependency\BundleDependencyInterface::class,
            $this->bundle
        );
    }

    /**
     * @test
     */
    public function testGetBundleDependencies(): void
    {
        $dependencies = UserAgreementBundle::getBundleDependencies();
        
        $this->assertArrayHasKey(
            \Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class,
            $dependencies
        );
        $this->assertEquals(
            ['all' => true],
            $dependencies[\Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class]
        );
    }

    /**
     * @test
     */
    public function testBundleName(): void
    {
        $this->assertEquals('UserAgreementBundle', $this->bundle->getName());
    }
}