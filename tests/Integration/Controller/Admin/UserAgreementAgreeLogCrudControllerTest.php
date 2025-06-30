<?php

namespace UserAgreementBundle\Tests\Integration\Controller\Admin;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Controller\Admin\UserAgreementAgreeLogCrudController;
use UserAgreementBundle\Entity\AgreeLog;

class UserAgreementAgreeLogCrudControllerTest extends TestCase
{
    /**
     * @test
     */
    public function testExtendsAbstractCrudController(): void
    {
        $reflection = new \ReflectionClass(UserAgreementAgreeLogCrudController::class);
        $this->assertTrue(
            $reflection->isSubclassOf(\Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController::class)
        );
    }

    /**
     * @test
     */
    public function testGetEntityFqcn(): void
    {
        $this->assertEquals(
            AgreeLog::class,
            UserAgreementAgreeLogCrudController::getEntityFqcn()
        );
    }
}