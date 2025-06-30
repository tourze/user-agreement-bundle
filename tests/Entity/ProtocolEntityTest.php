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
        $now = new \DateTimeImmutable();
        
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
        // 未设置ID时返回空字符串（因为__toString方法检查getId()是否为null）
        $this->assertEquals('', $this->protocolEntity->__toString());

        // 由于ProtocolEntity的ID是通过SnowflakeIdGenerator自动生成的，
        // 我们无法直接设置ID来测试__toString方法的完整功能
        // 这里我们只能测试空ID的情况
        
        // 测试设置了标题和版本但ID为空的情况
        $this->protocolEntity->setTitle('用户协议');
        $this->protocolEntity->setVersion('1.0.0');
        $this->assertEquals('', $this->protocolEntity->__toString());
    }

    /**
     * @test
     */
    public function testRetrieveApiArray(): void
    {
        // 设置测试数据
        $title = '用户协议';
        $version = '1.0.0';
        $content = '这是协议内容';
        $type = ProtocolType::MEMBER_REGISTER;
        $required = true;
        $pdfUrl = 'https://example.com/terms.pdf';
        $effectiveTime = new \DateTimeImmutable('2023-01-02 00:00:00');

        // 设置实体的属性
        $this->protocolEntity->setTitle($title);
        $this->protocolEntity->setVersion($version);
        $this->protocolEntity->setContent($content);
        $this->protocolEntity->setType($type);
        $this->protocolEntity->setRequired($required);
        $this->protocolEntity->setPdfUrl($pdfUrl);
        $this->protocolEntity->setEffectiveTime($effectiveTime);

        $result = $this->protocolEntity->retrieveApiArray();

        // 验证返回的数组结构和值
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('createTime', $result);
        $this->assertArrayHasKey('effectiveTime', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('version', $result);
        $this->assertArrayHasKey('content', $result);
        $this->assertArrayHasKey('pdfUrl', $result);
        $this->assertArrayHasKey('required', $result);

        // 验证具体的值
        $this->assertNull($result['id']); // ID 为空，因为没有通过数据库持久化
        $this->assertNull($result['createTime']); // createTime 为空，因为没有通过 TimestampableAware 设置
        $this->assertEquals('2023-01-02 00:00:00', $result['effectiveTime']);
        $this->assertEquals($type->value, $result['type']);
        $this->assertEquals($title, $result['title']);
        $this->assertEquals($version, $result['version']);
        $this->assertEquals($content, $result['content']);
        $this->assertEquals($pdfUrl, $result['pdfUrl']);
        $this->assertEquals($required, $result['required']);
    }
} 