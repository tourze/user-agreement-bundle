<?php

namespace UserAgreementBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use UserAgreementBundle\Entity\RevokeRequest;
use UserAgreementBundle\Enum\RevokeType;

/**
 * @phpstan-extends AbstractCrudController<RevokeRequest>
 */
#[AdminCrud(
    routePath: '/user-agreement/revoke-request',
    routeName: 'user_agreement_revoke_request',
)]
final class UserAgreementRevokeRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RevokeRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('注销请求')
            ->setEntityLabelInPlural('注销请求列表')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['identity', 'nickName', 'remark'])
            ->showEntityActionsInlined()
            ->setPageTitle('index', '用户注销请求管理')
            ->setPageTitle('detail', '注销请求详情')
            ->setPageTitle('edit', '处理注销请求')
            ->setPageTitle('new', '创建注销请求')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield AssociationField::new('user', '关联用户')
            ->setRequired(false)
            ->setHelp('注销请求关联的用户账号')
        ;

        yield ChoiceField::new('type', '注销类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => RevokeType::class,
                'choice_label' => fn (RevokeType $type) => $type->getLabel(),
            ])
            ->formatValue(fn ($value) => $value?->getLabel())
            ->setRequired(true)
        ;

        yield TextField::new('identity', '身份信息')
            ->setRequired(true)
            ->setMaxLength(255)
            ->setHelp('用户的身份标识信息')
        ;

        yield ImageField::new('avatar', '头像')
            ->setBasePath('/')
            ->setUploadDir('public/uploads/revoke')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false)
            ->hideOnIndex()
        ;

        yield TextField::new('nickName', '昵称')
            ->setMaxLength(255)
            ->setRequired(false)
        ;

        yield TextField::new('remark', '备注')
            ->setMaxLength(100)
            ->setRequired(false)
            ->setHelp('管理员处理备注')
        ;

        yield TextField::new('createdFromIp', '来源IP')
            ->hideOnForm()
            ->hideOnIndex()
        ;

        yield IntegerField::new('lockVersion', '版本号')
            ->hideOnForm()
            ->hideOnIndex()
            ->setHelp('乐观锁版本号')
        ;

        yield DateTimeField::new('createTime', '创建时间')
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
        $approve = Action::new('approve', '同意注销', 'fa fa-check')
            ->linkToCrudAction('approveRevoke')
            ->addCssClass('btn btn-success')
            ->displayIf(fn (RevokeRequest $entity) => null === $entity->getRemark() || '' === $entity->getRemark())
        ;

        $reject = Action::new('reject', '拒绝注销', 'fa fa-times')
            ->linkToCrudAction('rejectRevoke')
            ->addCssClass('btn btn-danger')
            ->displayIf(fn (RevokeRequest $entity) => null === $entity->getRemark() || '' === $entity->getRemark())
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $approve)
            ->add(Crud::PAGE_INDEX, $reject)
            ->add(Crud::PAGE_DETAIL, $approve)
            ->add(Crud::PAGE_DETAIL, $reject)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye');
            })
            ->setPermission($approve, 'ROLE_ADMIN')
            ->setPermission($reject, 'ROLE_ADMIN')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('type', '注销类型')->setChoices([
                '全渠道注销' => RevokeType::All->value,
                '不接收通知' => RevokeType::NO_NOTIFY->value,
                '接收通知' => RevokeType::NOTIFY->value,
            ]))
            ->add(TextFilter::new('identity', '身份信息'))
            ->add(TextFilter::new('nickName', '昵称'))
            ->add(TextFilter::new('remark', '备注'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
