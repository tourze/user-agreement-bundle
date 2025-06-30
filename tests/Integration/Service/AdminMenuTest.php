<?php

namespace UserAgreementBundle\Tests\Integration\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Service\AdminMenu;

class AdminMenuTest extends TestCase
{
    private AdminMenu $adminMenu;
    private LinkGeneratorInterface $linkGenerator;

    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }

    /**
     * @test
     */
    public function testImplementsMenuProviderInterface(): void
    {
        $this->assertInstanceOf(
            \Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface::class,
            $this->adminMenu
        );
    }

    /**
     * @test
     */
    public function testInvokeCreatesCustomerManagementMenu(): void
    {
        $rootItem = $this->createMock(ItemInterface::class);
        $customerMgmtItem = $this->createMock(ItemInterface::class);
        $termsItem = $this->createMock(ItemInterface::class);
        
        // 设置调用顺序：第一次返回null，第二次返回创建的项目
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('客户管理')
            ->willReturnOnConsecutiveCalls(null, $customerMgmtItem);
        
        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('客户管理')
            ->willReturn($customerMgmtItem);
        
        $customerMgmtItem->expects($this->once())
            ->method('addChild')
            ->with('条款管理')
            ->willReturn($termsItem);
        
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(ProtocolEntity::class)
            ->willReturn('/admin/protocol');
        
        $termsItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/protocol');
        
        ($this->adminMenu)($rootItem);
    }

    /**
     * @test
     */
    public function testInvokeWithExistingCustomerManagementMenu(): void
    {
        $rootItem = $this->createMock(ItemInterface::class);
        $existingCustomerMgmtItem = $this->createMock(ItemInterface::class);
        $termsItem = $this->createMock(ItemInterface::class);
        
        // 模拟已经存在"客户管理"子项
        $rootItem->method('getChild')->with('客户管理')->willReturn($existingCustomerMgmtItem);
        $rootItem->expects($this->never())->method('addChild');
        
        $existingCustomerMgmtItem->expects($this->once())
            ->method('addChild')
            ->with('条款管理')
            ->willReturn($termsItem);
        
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(ProtocolEntity::class)
            ->willReturn('/admin/protocol');
        
        $termsItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/protocol');
        
        ($this->adminMenu)($rootItem);
    }
}