<?php

namespace UserAgreementBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use UserAgreementBundle\Entity\AgreeLog;

/**
 * @phpstan-extends AbstractCrudController<AgreeLog>
 */
#[AdminCrud(
    routePath: '/user-agreement/agree-log',
    routeName: 'user_agreement_agree_log',
)]
final class UserAgreementAgreeLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AgreeLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('同意日志')
            ->setEntityLabelInPlural('同意日志列表')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['protocolId', 'memberId'])
            ->showEntityActionsInlined()
            ->setPageTitle('index', '协议同意日志')
            ->setPageTitle('detail', '同意日志详情')
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('protocolId', '协议ID')
            ->setMaxLength(255)
            ->setHelp('关联的协议ID')
            ->setDisabled(true)
        ;

        yield TextField::new('memberId', '成员ID')
            ->setMaxLength(255)
            ->setHelp('同意协议的用户ID')
            ->setDisabled(true)
        ;

        yield BooleanField::new('valid', '是否同意')
            ->renderAsSwitch(false)
            ->setHelp('用户是否同意了协议')
            ->setDisabled(true)
        ;

        yield DateTimeField::new('createTime', '同意时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
            ->setSortable(true)
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
            ->hideOnIndex()
            ->setSortable(true)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE, Action::BATCH_DELETE)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('protocolId', '协议ID'))
            ->add(TextFilter::new('memberId', '成员ID'))
            ->add(BooleanFilter::new('valid', '是否同意'))
            ->add(DateTimeFilter::new('createTime', '同意时间'))
        ;
    }
}
