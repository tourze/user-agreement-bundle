<?php

namespace UserAgreementBundle\Tests\Integration\Controller\Admin;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Controller\Admin\UserAgreementRevokeRequestCrudController;
use UserAgreementBundle\Entity\RevokeRequest;

class UserAgreementRevokeRequestCrudControllerTest extends TestCase
{
    /**
     * @test
     */
    public function testExtendsAbstractCrudController(): void
    {
        $reflection = new \ReflectionClass(UserAgreementRevokeRequestCrudController::class);
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
            RevokeRequest::class,
            UserAgreementRevokeRequestCrudController::getEntityFqcn()
        );
    }
}