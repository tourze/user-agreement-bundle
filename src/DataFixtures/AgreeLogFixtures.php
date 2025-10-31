<?php

namespace UserAgreementBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Entity\ProtocolEntity;

/**
 * 用户协议同意日志测试数据
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class AgreeLogFixtures extends Fixture implements DependentFixtureInterface
{
    public const AGREE_LOG_USER_001_REGISTER = 'agree-log-user-001-register';
    public const AGREE_LOG_USER_001_USAGE = 'agree-log-user-001-usage';
    public const AGREE_LOG_USER_001_PRIVACY = 'agree-log-user-001-privacy';
    public const AGREE_LOG_USER_001_SALE_PUSH = 'agree-log-user-001-sale-push';
    public const AGREE_LOG_USER_002_REGISTER = 'agree-log-user-002-register';
    public const AGREE_LOG_USER_002_USAGE = 'agree-log-user-002-usage';

    public function load(ObjectManager $manager): void
    {
        // 获取协议引用
        $registerProtocol = $this->getReference(ProtocolEntityFixtures::PROTOCOL_REGISTER_REFERENCE, ProtocolEntity::class);
        $usageProtocol = $this->getReference(ProtocolEntityFixtures::PROTOCOL_USAGE_REFERENCE, ProtocolEntity::class);
        $privacyProtocol = $this->getReference(ProtocolEntityFixtures::PROTOCOL_PRIVACY_REFERENCE, ProtocolEntity::class);
        $salePushProtocol = $this->getReference(ProtocolEntityFixtures::PROTOCOL_SALE_PUSH_REFERENCE, ProtocolEntity::class);

        // 用户ID列表（模拟不同用户）
        $userIds = [
            'user-001',
            'user-002',
            'user-003',
            'user-004',
            'user-005',
        ];

        // 1. 用户001 - 同意所有协议（典型用户）
        $agreeLog1 = new AgreeLog();
        $agreeLog1->setMemberId($userIds[0]);
        $agreeLog1->setProtocolId((string) $registerProtocol->getId());
        $agreeLog1->setValid(true);
        $manager->persist($agreeLog1);

        $agreeLog2 = new AgreeLog();
        $agreeLog2->setMemberId($userIds[0]);
        $agreeLog2->setProtocolId((string) $usageProtocol->getId());
        $agreeLog2->setValid(true);
        $manager->persist($agreeLog2);

        $agreeLog3 = new AgreeLog();
        $agreeLog3->setMemberId($userIds[0]);
        $agreeLog3->setProtocolId((string) $privacyProtocol->getId());
        $agreeLog3->setValid(true);
        $manager->persist($agreeLog3);

        $agreeLog4 = new AgreeLog();
        $agreeLog4->setMemberId($userIds[0]);
        $agreeLog4->setProtocolId((string) $salePushProtocol->getId());
        $agreeLog4->setValid(true);
        $manager->persist($agreeLog4);

        // 2. 用户002 - 只同意必要协议，拒绝营销推送
        $agreeLog5 = new AgreeLog();
        $agreeLog5->setMemberId($userIds[1]);
        $agreeLog5->setProtocolId((string) $registerProtocol->getId());
        $agreeLog5->setValid(true);
        $manager->persist($agreeLog5);

        $agreeLog6 = new AgreeLog();
        $agreeLog6->setMemberId($userIds[1]);
        $agreeLog6->setProtocolId((string) $usageProtocol->getId());
        $agreeLog6->setValid(true);
        $manager->persist($agreeLog6);

        $agreeLog7 = new AgreeLog();
        $agreeLog7->setMemberId($userIds[1]);
        $agreeLog7->setProtocolId((string) $privacyProtocol->getId());
        $agreeLog7->setValid(true);
        $manager->persist($agreeLog7);

        $agreeLog8 = new AgreeLog();
        $agreeLog8->setMemberId($userIds[1]);
        $agreeLog8->setProtocolId((string) $salePushProtocol->getId());
        $agreeLog8->setValid(false); // 拒绝营销推送
        $manager->persist($agreeLog8);

        // 3. 用户003 - 只同意注册协议（最小权限用户）
        $agreeLog9 = new AgreeLog();
        $agreeLog9->setMemberId($userIds[2]);
        $agreeLog9->setProtocolId((string) $registerProtocol->getId());
        $agreeLog9->setValid(true);
        $manager->persist($agreeLog9);

        // 4. 用户004 - 撤回了部分协议的同意
        $agreeLog10 = new AgreeLog();
        $agreeLog10->setMemberId($userIds[3]);
        $agreeLog10->setProtocolId((string) $registerProtocol->getId());
        $agreeLog10->setValid(true);
        $manager->persist($agreeLog10);

        $agreeLog11 = new AgreeLog();
        $agreeLog11->setMemberId($userIds[3]);
        $agreeLog11->setProtocolId((string) $usageProtocol->getId());
        $agreeLog11->setValid(false); // 撤回同意
        $manager->persist($agreeLog11);

        $agreeLog12 = new AgreeLog();
        $agreeLog12->setMemberId($userIds[3]);
        $agreeLog12->setProtocolId((string) $privacyProtocol->getId());
        $agreeLog12->setValid(true);
        $manager->persist($agreeLog12);

        // 5. 用户005 - 新用户，还未同意任何协议
        // 不创建任何记录，模拟新注册用户

        $manager->flush();

        // 设置引用，供其他Fixture使用
        $this->addReference(self::AGREE_LOG_USER_001_REGISTER, $agreeLog1);
        $this->addReference(self::AGREE_LOG_USER_001_USAGE, $agreeLog2);
        $this->addReference(self::AGREE_LOG_USER_001_PRIVACY, $agreeLog3);
        $this->addReference(self::AGREE_LOG_USER_001_SALE_PUSH, $agreeLog4);
        $this->addReference(self::AGREE_LOG_USER_002_REGISTER, $agreeLog5);
        $this->addReference(self::AGREE_LOG_USER_002_USAGE, $agreeLog8);
    }

    public function getDependencies(): array
    {
        return [
            ProtocolEntityFixtures::class,
        ];
    }
}
