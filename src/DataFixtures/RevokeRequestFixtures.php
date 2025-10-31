<?php

namespace UserAgreementBundle\DataFixtures;

use BizUserBundle\DataFixtures\BizUserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineResolveTargetEntityBundle\Service\ResolveTargetEntityService;
use Tourze\UserServiceContracts\UserServiceConstants;
use UserAgreementBundle\Entity\RevokeRequest;
use UserAgreementBundle\Enum\RevokeType;

/**
 * 用户注销请求测试数据
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class RevokeRequestFixtures extends Fixture implements DependentFixtureInterface
{
    public const REVOKE_REQUEST_ALL_1 = 'revoke-request-all-1';
    public const REVOKE_REQUEST_NO_NOTIFY = 'revoke-request-no-notify';
    public const REVOKE_REQUEST_NOTIFY = 'revoke-request-notify';
    public const REVOKE_REQUEST_ALL_2 = 'revoke-request-all-2';
    public const REVOKE_REQUEST_NO_AVATAR = 'revoke-request-no-avatar';

    public function __construct(
        private readonly ResolveTargetEntityService $resolveTargetEntityService,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 1. 创建全渠道注销请求（已处理）
        $revokeRequest1 = new RevokeRequest();
        $user1 = $this->createMockUser(UserServiceConstants::NORMAL_USER_REFERENCE_PREFIX . 1);
        if ($user1 instanceof UserInterface) {
            $revokeRequest1->setUser($user1);
        }
        $revokeRequest1->setType(RevokeType::All);
        $revokeRequest1->setIdentity('13800138001');
        $revokeRequest1->setNickName('张三');
        $revokeRequest1->setAvatar('https://images.unsplash.com/avatars/user1.jpg');
        $revokeRequest1->setRemark('用户要求删除所有个人信息');
        $revokeRequest1->setCreatedFromIp('192.168.1.100');
        $manager->persist($revokeRequest1);

        // 2. 创建保留资料但不接收通知的注销请求
        $revokeRequest2 = new RevokeRequest();
        $user2 = $this->createMockUser(UserServiceConstants::NORMAL_USER_REFERENCE_PREFIX . 2);
        if ($user2 instanceof UserInterface) {
            $revokeRequest2->setUser($user2);
        }
        $revokeRequest2->setType(RevokeType::NO_NOTIFY);
        $revokeRequest2->setIdentity('user002@test.example');
        $revokeRequest2->setNickName('李四');
        $revokeRequest2->setAvatar('https://images.unsplash.com/avatars/user2.jpg');
        $revokeRequest2->setRemark('不再使用服务，但保留历史订单');
        $revokeRequest2->setCreatedFromIp('10.0.0.50');
        $manager->persist($revokeRequest2);

        // 3. 创建保留资料且接收通知的注销请求
        $revokeRequest3 = new RevokeRequest();
        $user3 = $this->createMockUser(UserServiceConstants::NORMAL_USER_REFERENCE_PREFIX . 3);
        if ($user3 instanceof UserInterface) {
            $revokeRequest3->setUser($user3);
        }
        $revokeRequest3->setType(RevokeType::NOTIFY);
        $revokeRequest3->setIdentity('13900139003');
        $revokeRequest3->setNickName('王五');
        $revokeRequest3->setRemark('暂时不使用，但希望接收重要通知');
        $revokeRequest3->setCreatedFromIp('172.16.0.100');
        $manager->persist($revokeRequest3);

        // 4. 创建另一个全渠道注销请求（未处理）
        $revokeRequest4 = new RevokeRequest();
        $user4 = $this->createMockUser(UserServiceConstants::NORMAL_USER_REFERENCE_PREFIX . 4);
        if ($user4 instanceof UserInterface) {
            $revokeRequest4->setUser($user4);
        }
        $revokeRequest4->setType(RevokeType::All);
        $revokeRequest4->setIdentity('user004@test.example');
        $revokeRequest4->setNickName('赵六');
        $revokeRequest4->setAvatar('https://images.unsplash.com/avatars/user4.jpg');
        $revokeRequest4->setRemark('根据GDPR要求删除数据');
        $revokeRequest4->setCreatedFromIp('203.0.113.45');
        $manager->persist($revokeRequest4);

        // 5. 创建无头像的注销请求
        $revokeRequest5 = new RevokeRequest();
        $user5 = $this->createMockUser(UserServiceConstants::NORMAL_USER_REFERENCE_PREFIX . 5);
        if ($user5 instanceof UserInterface) {
            $revokeRequest5->setUser($user5);
        }
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

    /**
     * 创建模拟用户
     */
    private function createMockUser(string $reference = 'user-1'): object
    {
        $userClass = $this->resolveTargetEntityService->findEntityClass(UserInterface::class);

        // @phpstan-ignore-next-line
        return $this->getReference($reference, $userClass);
    }

    public function getDependencies(): array
    {
        return [
            BizUserFixtures::class,
        ];
    }
}
