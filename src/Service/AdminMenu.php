<?php

namespace UserAgreementBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Entity\RevokeRequest;

#[When(env: 'prod')]
#[When(env: 'dev')]
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private ?LinkGeneratorInterface $linkGenerator = null)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        // 在测试环境或 linkGenerator 不可用时跳过菜单添加
        if (null === $this->linkGenerator) {
            return;
        }

        if (null === $item->getChild('客户管理')) {
            $item->addChild('客户管理');
        }

        $customerMenu = $item->getChild('客户管理');
        if (null === $customerMenu) {
            return;
        }

        $customerMenu->addChild('条款管理')->setUri($this->linkGenerator->getCurdListPage(ProtocolEntity::class));
        $customerMenu->addChild('同意日志')->setUri($this->linkGenerator->getCurdListPage(AgreeLog::class));
        $customerMenu->addChild('注销请求')->setUri($this->linkGenerator->getCurdListPage(RevokeRequest::class));
    }
}
