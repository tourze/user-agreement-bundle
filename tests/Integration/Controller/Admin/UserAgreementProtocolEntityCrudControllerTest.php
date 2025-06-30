<?php

namespace UserAgreementBundle\Tests\Integration\Controller\Admin;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Controller\Admin\UserAgreementProtocolEntityCrudController;
use UserAgreementBundle\Entity\ProtocolEntity;

class UserAgreementProtocolEntityCrudControllerTest extends TestCase
{
    /**
     * @test
     */
    public function testExtendsAbstractCrudController(): void
    {
        $reflection = new \ReflectionClass(UserAgreementProtocolEntityCrudController::class);
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
            ProtocolEntity::class,
            UserAgreementProtocolEntityCrudController::getEntityFqcn()
        );
    }
}