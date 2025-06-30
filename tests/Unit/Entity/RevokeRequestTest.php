<?php

namespace UserAgreementBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use UserAgreementBundle\Entity\RevokeRequest;
use UserAgreementBundle\Enum\RevokeType;

class RevokeRequestTest extends TestCase
{
    private RevokeRequest $revokeRequest;

    protected function setUp(): void
    {
        $this->revokeRequest = new RevokeRequest();
    }

    /**
     * @test
     */
    public function testGettersAndSetters(): void
    {
        // 测试备注
        $remark = '用户申请注销账户';
        $this->revokeRequest->setRemark($remark);
        $this->assertEquals($remark, $this->revokeRequest->getRemark());

        // 测试注销类型
        $type = RevokeType::All;
        $this->revokeRequest->setType($type);
        $this->assertEquals($type, $this->revokeRequest->getType());

        // 测试身份信息
        $identity = '123456789';
        $this->revokeRequest->setIdentity($identity);
        $this->assertEquals($identity, $this->revokeRequest->getIdentity());

        // 测试头像
        $avatar = 'https://example.com/avatar.jpg';
        $this->revokeRequest->setAvatar($avatar);
        $this->assertEquals($avatar, $this->revokeRequest->getAvatar());

        // 测试昵称
        $nickName = '测试用户';
        $this->revokeRequest->setNickName($nickName);
        $this->assertEquals($nickName, $this->revokeRequest->getNickName());

        // 测试锁版本
        $lockVersion = 1;
        $this->revokeRequest->setLockVersion($lockVersion);
        $this->assertEquals($lockVersion, $this->revokeRequest->getLockVersion());

        // 测试创建IP
        $createdFromIp = '192.168.1.1';
        $this->revokeRequest->setCreatedFromIp($createdFromIp);
        $this->assertEquals($createdFromIp, $this->revokeRequest->getCreatedFromIp());

        // 测试更新IP
        $updatedFromIp = '192.168.1.2';
        $this->revokeRequest->setUpdatedFromIp($updatedFromIp);
        $this->assertEquals($updatedFromIp, $this->revokeRequest->getUpdatedFromIp());
    }

    /**
     * @test
     */
    public function testUserRelation(): void
    {
        $user = $this->createMock(UserInterface::class);
        
        $this->revokeRequest->setUser($user);
        $this->assertEquals($user, $this->revokeRequest->getUser());
    }

    /**
     * @test
     */
    public function testToString(): void
    {
        // ID为空时，toString应该返回空字符串
        $this->assertEquals('', $this->revokeRequest->__toString());
    }

    /**
     * @test
     */
    public function testInitialValues(): void
    {
        // 测试初始值都是null
        $this->assertNull($this->revokeRequest->getId());
        $this->assertNull($this->revokeRequest->getUser());
        $this->assertNull($this->revokeRequest->getType());
        $this->assertNull($this->revokeRequest->getIdentity());
        $this->assertNull($this->revokeRequest->getAvatar());
        $this->assertNull($this->revokeRequest->getNickName());
        $this->assertNull($this->revokeRequest->getRemark());
        $this->assertNull($this->revokeRequest->getLockVersion());
        $this->assertNull($this->revokeRequest->getCreatedFromIp());
        $this->assertNull($this->revokeRequest->getUpdatedFromIp());
    }

    /**
     * @test
     */
    public function testWithAllRevokeTypes(): void
    {
        foreach (RevokeType::cases() as $type) {
            $this->revokeRequest->setType($type);
            $this->assertEquals($type, $this->revokeRequest->getType());
        }
    }
}