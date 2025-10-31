<?php

namespace UserAgreementBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Enum\ProtocolType;

/**
 * @internal
 */
#[CoversClass(ProtocolEntity::class)]
final class ProtocolEntityTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ProtocolEntity();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'title' => ['title', '用户协议'],
            'version' => ['version', '1.0.0'],
            'content' => ['content', '这是协议内容'],
            'valid' => ['valid', true],
            'valid_false' => ['valid', false],
            'type' => ['type', ProtocolType::MEMBER_REGISTER],
            'required' => ['required', true],
            'required_false' => ['required', false],
            'pdfUrl' => ['pdfUrl', 'https://example.com/terms.pdf'],
            'pdfUrl_null' => ['pdfUrl', null],
            'effectiveTime' => ['effectiveTime', new \DateTimeImmutable('2023-01-02 00:00:00')],
            'effectiveTime_null' => ['effectiveTime', null],
        ];
    }

    #[Test]
    public function testToString(): void
    {
        $protocolEntity = new ProtocolEntity();

        // 未设置ID时返回空字符串（因为__toString方法检查getId()是否为null）
        $this->assertEquals('', $protocolEntity->__toString());

        // 由于ProtocolEntity的ID是通过SnowflakeIdGenerator自动生成的，
        // 我们无法直接设置ID来测试__toString方法的完整功能
        // 这里我们只能测试空ID的情况

        // 测试设置了标题和版本但ID为空的情况
        $protocolEntity->setTitle('用户协议');
        $protocolEntity->setVersion('1.0.0');
        $this->assertEquals('', $protocolEntity->__toString());
    }

    #[Test]
    public function testRetrieveApiArray(): void
    {
        $protocolEntity = new ProtocolEntity();

        // 设置测试数据
        $title = '用户协议';
        $version = '1.0.0';
        $content = '这是协议内容';
        $type = ProtocolType::MEMBER_REGISTER;
        $required = true;
        $pdfUrl = 'https://example.com/terms.pdf';
        $effectiveTime = new \DateTimeImmutable('2023-01-02 00:00:00');

        // 设置实体的属性
        $protocolEntity->setTitle($title);
        $protocolEntity->setVersion($version);
        $protocolEntity->setContent($content);
        $protocolEntity->setType($type);
        $protocolEntity->setValid(true);
        $protocolEntity->setRequired($required);
        $protocolEntity->setPdfUrl($pdfUrl);
        $protocolEntity->setEffectiveTime($effectiveTime);

        $result = $protocolEntity->retrieveApiArray();

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
