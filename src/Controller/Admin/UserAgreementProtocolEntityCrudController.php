<?php

namespace UserAgreementBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use UserAgreementBundle\Entity\ProtocolEntity;

class UserAgreementProtocolEntityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProtocolEntity::class;
    }
}
