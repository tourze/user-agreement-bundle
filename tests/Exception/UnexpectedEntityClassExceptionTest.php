<?php

namespace UserAgreementBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use UserAgreementBundle\Exception\UnexpectedEntityClassException;

/**
 * @internal
 */
#[CoversClass(UnexpectedEntityClassException::class)]
final class UnexpectedEntityClassExceptionTest extends AbstractExceptionTestCase
{
    #[Test]
    public function testExceptionMessage(): void
    {
        $entityClass = 'SomeEntity';
        $exception = new UnexpectedEntityClassException($entityClass);

        $this->assertEquals("Unexpected entity class: {$entityClass}", $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    #[Test]
    public function testExceptionWithPrevious(): void
    {
        $entityClass = 'AnotherEntity';
        $previous = new \Exception('Previous exception');
        $exception = new UnexpectedEntityClassException($entityClass, $previous);

        $this->assertEquals("Unexpected entity class: {$entityClass}", $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Test]
    public function testExtendsInvalidArgumentException(): void
    {
        $exception = new UnexpectedEntityClassException('Test');

        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }
}
