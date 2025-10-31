<?php

namespace UserAgreementBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use UserAgreementBundle\Exception\TermsNeedAgreeException;

/**
 * @internal
 */
#[CoversClass(TermsNeedAgreeException::class)]
final class TermsNeedAgreeExceptionTest extends AbstractExceptionTestCase
{
    #[Test]
    public function testExceptionConstruct(): void
    {
        $exception = new TermsNeedAgreeException();

        $this->assertInstanceOf(TermsNeedAgreeException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals(-988, $exception->getCode());
        $this->assertEquals([], $exception->getData());
    }

    #[Test]
    public function testExceptionWithMessage(): void
    {
        $message = 'Terms need to be agreed';
        $exception = new TermsNeedAgreeException($message);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(-988, $exception->getCode());
    }

    #[Test]
    public function testExceptionWithData(): void
    {
        $message = 'Terms need to be agreed';
        $data = ['protocol_type' => 'MEMBER_REGISTER'];
        $exception = new TermsNeedAgreeException($message, $data);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(-988, $exception->getCode());
        $this->assertEquals($data, $exception->getData());
    }

    #[Test]
    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new TermsNeedAgreeException('Terms error', [], $previous);

        $this->assertEquals($previous, $exception->getPrevious());
        $this->assertEquals(-988, $exception->getCode());
    }

    #[Test]
    public function testExceptionWithAllParameters(): void
    {
        $message = 'Terms need to be agreed';
        $data = ['protocol_type' => 'MEMBER_REGISTER'];
        $previous = new \Exception('Previous exception');

        $exception = new TermsNeedAgreeException($message, $data, $previous);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(-988, $exception->getCode());
        $this->assertEquals($previous, $exception->getPrevious());
    }

    #[Test]
    public function testExceptionCodeIsFixed(): void
    {
        // 测试不同的构造参数，code都应该是-988
        $exception1 = new TermsNeedAgreeException();
        $exception2 = new TermsNeedAgreeException('test');
        $exception3 = new TermsNeedAgreeException('test', ['key' => 'data']);

        $this->assertEquals(-988, $exception1->getCode());
        $this->assertEquals(-988, $exception2->getCode());
        $this->assertEquals(-988, $exception3->getCode());
    }
}
