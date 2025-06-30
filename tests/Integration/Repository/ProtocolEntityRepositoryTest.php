<?php

namespace UserAgreementBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Repository\ProtocolEntityRepository;

class ProtocolEntityRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function testExtendsServiceEntityRepository(): void
    {
        $reflection = new \ReflectionClass(ProtocolEntityRepository::class);
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
            'UserAgreementBundle\Repository\ProtocolEntityRepository',
            ProtocolEntityRepository::class
        );
    }
}