<?php

namespace UserAgreementBundle\Controller\Admin;

use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;
use UserAgreementBundle\Entity\RevokeRequest;

class UserAgreementRevokeRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RevokeRequest::class;
    }
}
