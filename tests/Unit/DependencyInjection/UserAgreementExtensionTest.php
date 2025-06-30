<?php

namespace UserAgreementBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use UserAgreementBundle\DependencyInjection\UserAgreementExtension;

class UserAgreementExtensionTest extends TestCase
{
    private UserAgreementExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new UserAgreementExtension();
        $this->container = new ContainerBuilder();
    }

    /**
     * @test
     */
    public function testLoadConfiguration(): void
    {
        $configs = [];
        
        // 不应该抛出异常
        $this->extension->load($configs, $this->container);
        
        // 验证扩展是否正确实例化
        $this->assertInstanceOf(UserAgreementExtension::class, $this->extension);
    }

    /**
     * @test
     */
    public function testExtendsSymfonyExtension(): void
    {
        $this->assertInstanceOf(
            \Symfony\Component\DependencyInjection\Extension\Extension::class,
            $this->extension
        );
    }

    /**
     * @test
     */
    public function testLoadWithEmptyConfig(): void
    {
        $this->extension->load([], $this->container);
        
        // 验证容器正常工作（没有抛出异常）
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    /**
     * @test
     */
    public function testLoadWithMultipleConfigs(): void
    {
        $configs = [
            [],
            [],
        ];
        
        $this->extension->load($configs, $this->container);
        
        // 验证扩展可以处理多个配置数组
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }
}