<?php

declare(strict_types=1);

namespace UserAgreementBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use UserAgreementBundle\Controller\Admin\UserAgreementAgreeLogCrudController;
use UserAgreementBundle\Entity\AgreeLog;

/**
 * @internal
 */
#[CoversClass(UserAgreementAgreeLogCrudController::class)]
#[RunTestsInSeparateProcesses]
class UserAgreementAgreeLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<AgreeLog>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(UserAgreementAgreeLogCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     *
     * 返回INDEX页面的实际字段标题，与 configureFields('index') 方法的配置保持一致。
     * 这些字段包括：ID、协议ID、成员ID、是否同意、同意时间
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '协议ID' => ['协议ID'];
        yield '成员ID' => ['成员ID'];
        yield '是否同意' => ['是否同意'];
        yield '同意时间' => ['同意时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        // UserAgreementAgreeLogCrudController 禁用了 EDIT 操作 (Action::EDIT is disabled)
        // 但框架要求至少提供一个字段，实际测试会被跳过
        yield 'protocolId' => ['protocolId'];
        yield 'memberId' => ['memberId'];
    }

    /**
     * NEW 操作已被禁用，提供dummy数据以满足PHPUnit DataProvider要求
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        // UserAgreementAgreeLogCrudController 禁用了 NEW 操作 (Action::NEW is disabled)
        // 提供dummy数据以满足PHPUnit DataProvider要求，测试会在isActionEnabled检查时提前返回
        yield 'dummy_field' => ['dummy_field'];
    }

    /**
     * 重写基类的编辑页面预填充测试方法，因为当前控制器禁用了 EDIT 操作
     */
    public function testCustomEditPagePrefillsExistingData(): void
    {
        self::markTestSkipped('UserAgreementAgreeLogCrudController 已禁用 EDIT 操作，无法测试编辑页面预填充');
    }
}
