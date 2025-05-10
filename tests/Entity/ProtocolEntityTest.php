<?php

namespace UserAgreementBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Enum\ProtocolType;

class ProtocolEntityTest extends TestCase
{
    private ProtocolEntity $protocolEntity;

    protected function setUp(): void
    {
        $this->protocolEntity = new ProtocolEntity();
    }

    /**
     * @test
     */
    public function testGettersAndSetters(): void
    {
        // 测试标题属性
        $title = '用户协议';
        $this->protocolEntity->setTitle($title);
        $this->assertEquals($title, $this->protocolEntity->getTitle());

        // 测试版本属性
        $version = '1.0.0';
        $this->protocolEntity->setVersion($version);
        $this->assertEquals($version, $this->protocolEntity->getVersion());

        // 测试内容属性
        $content = '这是协议内容';
        $this->protocolEntity->setContent($content);
        $this->assertEquals($content, $this->protocolEntity->getContent());

        // 测试有效性属性
        $valid = true;
        $this->protocolEntity->setValid($valid);
        $this->assertEquals($valid, $this->protocolEntity->isValid());

        // 测试类型属性
        $type = ProtocolType::MEMBER_REGISTER;
        $this->protocolEntity->setType($type);
        $this->assertEquals($type, $this->protocolEntity->getType());

        // 测试必需属性
        $required = true;
        $this->protocolEntity->setRequired($required);
        $this->assertEquals($required, $this->protocolEntity->isRequired());

        // 测试PDF URL属性
        $pdfUrl = 'https://example.com/terms.pdf';
        $this->protocolEntity->setPdfUrl($pdfUrl);
        $this->assertEquals($pdfUrl, $this->protocolEntity->getPdfUrl());

        // 测试时间属性
        $now = new \DateTime();
        
        $this->protocolEntity->setCreateTime($now);
        $this->assertEquals($now, $this->protocolEntity->getCreateTime());
        
        $this->protocolEntity->setUpdateTime($now);
        $this->assertEquals($now, $this->protocolEntity->getUpdateTime());
        
        $this->protocolEntity->setEffectiveTime($now);
        $this->assertEquals($now, $this->protocolEntity->getEffectiveTime());
    }

    /**
     * @test
     */
    public function testToString(): void
    {
        // 未设置ID时返回空字符串
        $this->assertEquals('', $this->protocolEntity->__toString());

        // 设置ID、标题和版本后测试toString
        $mockProtocol = $this->getMockBuilder(ProtocolEntity::class)
            ->onlyMethods(['getId', 'getTitle', 'getVersion'])
            ->getMock();
        
        $mockProtocol->method('getId')->willReturn('123456789');
        $mockProtocol->method('getTitle')->willReturn('用户协议');
        $mockProtocol->method('getVersion')->willReturn('1.0.0');
        
        $this->assertEquals('用户协议1.0.0', $mockProtocol->__toString());
    }

    /**
     * @test
     */
    public function testRetrieveApiArray(): void
    {
        // 设置测试数据
        $id = '123456789';
        $title = '用户协议';
        $version = '1.0.0';
        $content = '这是协议内容';
        $type = ProtocolType::MEMBER_REGISTER;
        $required = true;
        $pdfUrl = 'https://example.com/terms.pdf';
        $createTime = new \DateTime('2023-01-01 00:00:00');
        $effectiveTime = new \DateTime('2023-01-02 00:00:00');

        $mockProtocol = $this->getMockBuilder(ProtocolEntity::class)
            ->onlyMethods(['getId', 'getTitle', 'getVersion', 'getContent', 'getType', 'isRequired', 'getPdfUrl', 'getCreateTime', 'getEffectiveTime'])
            ->getMock();
        
        $mockProtocol->method('getId')->willReturn($id);
        $mockProtocol->method('getTitle')->willReturn($title);
        $mockProtocol->method('getVersion')->willReturn($version);
        $mockProtocol->method('getContent')->willReturn($content);
        $mockProtocol->method('getType')->willReturn($type);
        $mockProtocol->method('isRequired')->willReturn($required);
        $mockProtocol->method('getPdfUrl')->willReturn($pdfUrl);
        $mockProtocol->method('getCreateTime')->willReturn($createTime);
        $mockProtocol->method('getEffectiveTime')->willReturn($effectiveTime);

        $expected = [
            'id' => $id,
            'createTime' => '2023-01-01 00:00:00',
            'effectiveTime' => '2023-01-02 00:00:00',
            'type' => $type->value,
            'title' => $title,
            'version' => $version,
            'content' => $content,
            'pdfUrl' => $pdfUrl,
            'required' => $required,
        ];

        $this->assertEquals($expected, $mockProtocol->retrieveApiArray());
    }
} 