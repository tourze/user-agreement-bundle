<?php

namespace UserAgreementBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Exception\TermsNeedAgreeException;

class TermsNeedAgreeExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testExceptionConstruct(): void
    {
        $exception = new TermsNeedAgreeException();
        
        $this->assertInstanceOf(TermsNeedAgreeException::class, $exception);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Exception\ApiException::class, $exception);
        $this->assertEquals(-988, $exception->getCode());
    }

    /**
     * @test
     */
    public function testExceptionWithMessage(): void
    {
        $message = 'Terms need to be agreed';
        $exception = new TermsNeedAgreeException($message);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(-988, $exception->getCode());
    }

    /**
     * @test
     */
    public function testExceptionWithData(): void
    {
        $message = 'Terms need to be agreed';
        $data = ['protocol_type' => 'MEMBER_REGISTER'];
        $exception = new TermsNeedAgreeException($message, $data);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(-988, $exception->getCode());
    }

    /**
     * @test
     */
    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new TermsNeedAgreeException('Terms error', [], $previous);
        
        $this->assertEquals($previous, $exception->getPrevious());
        $this->assertEquals(-988, $exception->getCode());
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function testExceptionCodeIsFixed(): void
    {
        // 测试不同的构造参数，code都应该是-988
        $exception1 = new TermsNeedAgreeException();
        $exception2 = new TermsNeedAgreeException('test');
        $exception3 = new TermsNeedAgreeException('test', ['data']);
        
        $this->assertEquals(-988, $exception1->getCode());
        $this->assertEquals(-988, $exception2->getCode());
        $this->assertEquals(-988, $exception3->getCode());
    }
}