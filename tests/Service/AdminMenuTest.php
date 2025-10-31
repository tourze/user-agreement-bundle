<?php

namespace UserAgreementBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Entity\RevokeRequest;
use UserAgreementBundle\Exception\UnexpectedEntityClassException;
use UserAgreementBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses] final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    /** @var LinkGeneratorInterface&MockObject */
    private LinkGeneratorInterface $linkGenerator;

    protected function onSetUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        // 在容器中设置模拟服务
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
        // AdminMenu 类由容器自动管理，从容器中获取服务
        $adminMenu = self::getContainer()->get(AdminMenu::class);
        self::assertInstanceOf(AdminMenu::class, $adminMenu);
        $this->adminMenu = $adminMenu;
    }

    #[Test]
    public function testImplementsMenuProviderInterface(): void
    {
        $this->assertInstanceOf(
            MenuProviderInterface::class,
            $this->adminMenu
        );
    }

    #[Test]
    public function testInvokeCreatesAllMenuItems(): void
    {
        $rootItem = $this->createMock(ItemInterface::class);
        $customerMgmtItem = $this->createMock(ItemInterface::class);

        // 设置调用顺序：第一次返回null，后续返回创建的项目
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('客户管理')
            ->willReturnOnConsecutiveCalls(null, $customerMgmtItem)
        ;

        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('客户管理')
            ->willReturn($customerMgmtItem)
        ;

        // 配置客户管理菜单添加子菜单
        $customerMgmtItem->expects($this->exactly(3))
            ->method('addChild')
            ->willReturnCallback(function ($name) {
                $item = $this->createMock(ItemInterface::class);
                $item->expects($this->once())
                    ->method('setUri')
                ;

                return $item;
            })
        ;

        // 配置链接生成器
        $this->linkGenerator->expects($this->exactly(3))
            ->method('getCurdListPage')
            ->willReturnCallback(function ($entityClass) {
                return match ($entityClass) {
                    ProtocolEntity::class => '/admin/protocol',
                    AgreeLog::class => '/admin/agree-log',
                    RevokeRequest::class => '/admin/revoke-request',
                    default => throw new UnexpectedEntityClassException($entityClass),
                };
            })
        ;

        ($this->adminMenu)($rootItem);
    }

    #[Test]
    public function testInvokeWithExistingCustomerManagementMenu(): void
    {
        $rootItem = $this->createMock(ItemInterface::class);
        $existingCustomerMgmtItem = $this->createMock(ItemInterface::class);

        // 模拟已经存在"客户管理"子项
        $rootItem->method('getChild')->with('客户管理')->willReturn($existingCustomerMgmtItem);
        $rootItem->expects($this->never())->method('addChild');

        // 配置客户管理菜单添加子菜单
        $existingCustomerMgmtItem->expects($this->exactly(3))
            ->method('addChild')
            ->willReturnCallback(function ($name) {
                $item = $this->createMock(ItemInterface::class);
                $item->expects($this->once())
                    ->method('setUri')
                ;

                return $item;
            })
        ;

        // 配置链接生成器
        $this->linkGenerator->expects($this->exactly(3))
            ->method('getCurdListPage')
            ->willReturnCallback(function ($entityClass) {
                return match ($entityClass) {
                    ProtocolEntity::class => '/admin/protocol',
                    AgreeLog::class => '/admin/agree-log',
                    RevokeRequest::class => '/admin/revoke-request',
                    default => throw new UnexpectedEntityClassException($entityClass),
                };
            })
        ;

        ($this->adminMenu)($rootItem);
    }
}
