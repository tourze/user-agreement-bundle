<?php

namespace UserAgreementBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use UserAgreementBundle\Entity\AgreeLog;

class UserAgreementAgreeLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AgreeLog::class;
    }
}
