<?php

namespace UserAgreementBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use UserAgreementBundle\DependencyInjection\UserAgreementExtension;

/**
 * @internal
 */
#[CoversClass(UserAgreementExtension::class)]
final class UserAgreementExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private UserAgreementExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new UserAgreementExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    #[Test]
    public function testExtensionInstance(): void
    {
        // 验证扩展是否正确实例化
        $this->assertInstanceOf(UserAgreementExtension::class, $this->extension);
    }
}
