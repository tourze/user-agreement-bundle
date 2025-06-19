<?php

namespace UserAgreementBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use UserAgreementBundle\Entity\ProtocolEntity;

class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if ($item->getChild('客户管理') === null) {
            $item->addChild('客户管理');
        }
        $item->getChild('客户管理')->addChild('条款管理')->setUri($this->linkGenerator->getCurdListPage(ProtocolEntity::class));
    }
}
