<?php

namespace UserAgreementBundle;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use UserAgreementBundle\Entity\ProtocolEntity;

#[MenuProvider]
class AdminMenu
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('客户管理')) {
            $item->addChild('客户管理');
        }
        $item->getChild('客户管理')->addChild('条款管理')->setUri($this->linkGenerator->getCurdListPage(ProtocolEntity::class));
    }
}
