<?php

namespace UserAgreementBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Entity\AgreeLog;

class AgreeLogTest extends TestCase
{
    private AgreeLog $agreeLog;

    protected function setUp(): void
    {
        $this->agreeLog = new AgreeLog();
    }

    /**
     * @test
     */
    public function testGettersAndSetters(): void
    {
        // 测试协议ID属性
        $protocolId = '123456789';
        $this->agreeLog->setProtocolId($protocolId);
        $this->assertEquals($protocolId, $this->agreeLog->getProtocolId());

        // 测试会员ID属性
        $memberId = '987654321';
        $this->agreeLog->setMemberId($memberId);
        $this->assertEquals($memberId, $this->agreeLog->getMemberId());

        // 测试有效性属性
        $valid = false;
        $this->agreeLog->setValid($valid);
        $this->assertEquals($valid, $this->agreeLog->isValid());

        // 测试时间属性
        $now = new \DateTime();
        
        $this->agreeLog->setCreateTime($now);
        $this->assertEquals($now, $this->agreeLog->getCreateTime());
        
        $this->agreeLog->setUpdateTime($now);
        $this->assertEquals($now, $this->agreeLog->getUpdateTime());
    }

    /**
     * @test
     */
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
        $this->assertNull($newAgreeLog->getCreateTime());
        $this->assertNull($newAgreeLog->getUpdateTime());
    }
} 