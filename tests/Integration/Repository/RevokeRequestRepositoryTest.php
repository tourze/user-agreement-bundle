<?php

namespace UserAgreementBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Repository\RevokeRequestRepository;

class RevokeRequestRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function testExtendsServiceEntityRepository(): void
    {
        $reflection = new \ReflectionClass(RevokeRequestRepository::class);
        $this->assertTrue(
            $reflection->isSubclassOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class)
        );
    }

    /**
     * @test
     */
    public function testRepositoryClassName(): void
    {
        $this->assertEquals(
            'UserAgreementBundle\Repository\RevokeRequestRepository',
            RevokeRequestRepository::class
        );
    }
}