<?php

namespace UserAgreementBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\UserServiceContracts\UserManagerInterface;
use UserAgreementBundle\Entity\RevokeRequest;
use UserAgreementBundle\Enum\RevokeType;

/**
 * 用户注销请求测试数据
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class RevokeRequestFixtures extends Fixture
{
    public const REVOKE_REQUEST_ALL_1 = 'revoke-request-all-1';
    public const REVOKE_REQUEST_NO_NOTIFY = 'revoke-request-no-notify';
    public const REVOKE_REQUEST_NOTIFY = 'revoke-request-notify';
    public const REVOKE_REQUEST_ALL_2 = 'revoke-request-all-2';
    public const REVOKE_REQUEST_NO_AVATAR = 'revoke-request-no-avatar';

    public function __construct(
        private readonly UserManagerInterface $userManager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 通过 UserManager 创建测试用户
        $users = [];
        $userIdentifiers = [
            1 => ['identifier' => 'revoke_user_1', 'nickName' => '张三'],
            2 => ['identifier' => 'revoke_user_2', 'nickName' => '李四'],
            3 => ['identifier' => 'revoke_user_3', 'nickName' => '王五'],
            4 => ['identifier' => 'revoke_user_4', 'nickName' => '赵六'],
            5 => ['identifier' => 'revoke_user_5', 'nickName' => '钱七'],
        ];

        foreach ($userIdentifiers as $index => $data) {
            $user = $this->userManager->createUser(
                userIdentifier: $data['identifier'],
                nickName: $data['nickName'],
            );
            $this->userManager->saveUser($user);
            $users[$index] = $user;
        }

        // 1. 创建全渠道注销请求（已处理）
        $revokeRequest1 = new RevokeRequest();
        $revokeRequest1->setUser($users[1]);
        $revokeRequest1->setType(RevokeType::All);
        $revokeRequest1->setIdentity('13800138001');
        $revokeRequest1->setNickName('张三');
        $revokeRequest1->setAvatar('https://images.unsplash.com/avatars/user1.jpg');
        $revokeRequest1->setRemark('用户要求删除所有个人信息');
        $revokeRequest1->setCreatedFromIp('192.168.1.100');
        $manager->persist($revokeRequest1);

        // 2. 创建保留资料但不接收通知的注销请求
        $revokeRequest2 = new RevokeRequest();
        $revokeRequest2->setUser($users[2]);
        $revokeRequest2->setType(RevokeType::NO_NOTIFY);
        $revokeRequest2->setIdentity('user002@test.example');
        $revokeRequest2->setNickName('李四');
        $revokeRequest2->setAvatar('https://images.unsplash.com/avatars/user2.jpg');
        $revokeRequest2->setRemark('不再使用服务，但保留历史订单');
        $revokeRequest2->setCreatedFromIp('10.0.0.50');
        $manager->persist($revokeRequest2);

        // 3. 创建保留资料且接收通知的注销请求
        $revokeRequest3 = new RevokeRequest();
        $revokeRequest3->setUser($users[3]);
        $revokeRequest3->setType(RevokeType::NOTIFY);
        $revokeRequest3->setIdentity('13900139003');
        $revokeRequest3->setNickName('王五');
        $revokeRequest3->setRemark('暂时不使用，但希望接收重要通知');
        $revokeRequest3->setCreatedFromIp('172.16.0.100');
        $manager->persist($revokeRequest3);

        // 4. 创建另一个全渠道注销请求（未处理）
        $revokeRequest4 = new RevokeRequest();
        $revokeRequest4->setUser($users[4]);
        $revokeRequest4->setType(RevokeType::All);
        $revokeRequest4->setIdentity('user004@test.example');
        $revokeRequest4->setNickName('赵六');
        $revokeRequest4->setAvatar('https://images.unsplash.com/avatars/user4.jpg');
        $revokeRequest4->setRemark('根据GDPR要求删除数据');
        $revokeRequest4->setCreatedFromIp('203.0.113.45');
        $manager->persist($revokeRequest4);

        // 5. 创建无头像的注销请求
        $revokeRequest5 = new RevokeRequest();
        $revokeRequest5->setUser($users[5]);
        $revokeRequest5->setType(RevokeType::NO_NOTIFY);
        $revokeRequest5->setIdentity('15000150005');
        $revokeRequest5->setNickName('钱七');
        // 不设置头像，测试可选字段
        $revokeRequest5->setRemark('账号不再使用');
        $revokeRequest5->setCreatedFromIp('192.168.100.200');
        $manager->persist($revokeRequest5);

        $manager->flush();

        // 设置引用，供其他Fixture使用
        $this->addReference(self::REVOKE_REQUEST_ALL_1, $revokeRequest1);
        $this->addReference(self::REVOKE_REQUEST_NO_NOTIFY, $revokeRequest2);
        $this->addReference(self::REVOKE_REQUEST_NOTIFY, $revokeRequest3);
        $this->addReference(self::REVOKE_REQUEST_ALL_2, $revokeRequest4);
        $this->addReference(self::REVOKE_REQUEST_NO_AVATAR, $revokeRequest5);
    }
}
