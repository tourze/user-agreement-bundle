<?php

namespace UserAgreementBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use UserAgreementBundle\Entity\RevokeRequest;
use UserAgreementBundle\Enum\RevokeType;

/**
 * @internal
 */
#[CoversClass(RevokeRequest::class)]
final class RevokeRequestTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new RevokeRequest();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'remark' => ['remark', '用户申请注销账户'],
            'remark_null' => ['remark', null],
            'type' => ['type', RevokeType::All],
            'type_null' => ['type', null],
            'identity' => ['identity', '123456789'],
            'avatar' => ['avatar', 'https://example.com/avatar.jpg'],
            'avatar_null' => ['avatar', null],
            'nickName' => ['nickName', '测试用户'],
            'nickName_null' => ['nickName', null],
            'lockVersion' => ['lockVersion', 1],
            'lockVersion_null' => ['lockVersion', null],
            'createdFromIp' => ['createdFromIp', '192.168.1.1'],
            'createdFromIp_null' => ['createdFromIp', null],
        ];
    }

    #[Test]
    public function testUserRelation(): void
    {
        $revokeRequest = new RevokeRequest();
        $user = $this->createMock(UserInterface::class);

        $revokeRequest->setUser($user);
        $this->assertEquals($user, $revokeRequest->getUser());
    }

    #[Test]
    public function testToString(): void
    {
        $revokeRequest = new RevokeRequest();

        // ID为空时，toString应该返回空字符串
        $this->assertEquals('', $revokeRequest->__toString());
    }

    #[Test]
    public function testInitialValues(): void
    {
        $revokeRequest = new RevokeRequest();

        // 测试初始值都是null
        $this->assertNull($revokeRequest->getId());
        $this->assertNull($revokeRequest->getUser());
        $this->assertNull($revokeRequest->getType());
        $this->assertNull($revokeRequest->getIdentity());
        $this->assertNull($revokeRequest->getAvatar());
        $this->assertNull($revokeRequest->getNickName());
        $this->assertNull($revokeRequest->getRemark());
        $this->assertNull($revokeRequest->getLockVersion());
        $this->assertNull($revokeRequest->getCreatedFromIp());
    }

    #[Test]
    public function testWithAllRevokeTypes(): void
    {
        $revokeRequest = new RevokeRequest();

        foreach (RevokeType::cases() as $type) {
            $revokeRequest->setType($type);
            $this->assertEquals($type, $revokeRequest->getType());
        }
    }
}
