<?php

namespace UserAgreementBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use UserAgreementBundle\Entity\AgreeLog;

/**
 * @internal
 */
#[CoversClass(AgreeLog::class)]
final class AgreeLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new AgreeLog();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'protocolId' => ['protocolId', 'test-protocol-123'],
            'memberId' => ['memberId', 'test-member-456'],
            'valid' => ['valid', true],
            'valid_false' => ['valid', false],
        ];
    }

    #[Test]
    public function testDefaultValues(): void
    {
        $newAgreeLog = new AgreeLog();

        // valid默认应为true
        $this->assertTrue($newAgreeLog->isValid());

        // ID默认应为null
        $this->assertNull($newAgreeLog->getId());

        // 其他属性默认应为null
        $this->assertNull($newAgreeLog->getProtocolId());
        $this->assertNull($newAgreeLog->getMemberId());
    }
}
