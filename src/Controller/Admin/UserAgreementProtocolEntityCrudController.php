<?php

namespace UserAgreementBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Enum\ProtocolType;

/**
 * @phpstan-extends AbstractCrudController<ProtocolEntity>
 */
#[AdminCrud(
    routePath: '/user-agreement/protocol',
    routeName: 'user_agreement_protocol',
)]
final class UserAgreementProtocolEntityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProtocolEntity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('协议')
            ->setEntityLabelInPlural('协议列表')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['title', 'version', 'content'])
            ->showEntityActionsInlined()
            ->setPageTitle('index', '协议管理')
            ->setPageTitle('new', '创建新协议')
            ->setPageTitle('edit', '编辑协议')
            ->setPageTitle('detail', '协议详情')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield BooleanField::new('valid', '是否有效')
            ->setHelp('设置协议是否当前有效')
        ;

        yield ChoiceField::new('type', '协议类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => ProtocolType::class,
                'choice_label' => fn (ProtocolType $type) => $type->getLabel(),
            ])
            ->formatValue(fn ($value) => $value?->getLabel())
        ;

        yield TextField::new('title', '协议名称')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('输入协议的显示名称')
        ;

        yield TextField::new('version', '版本号')
            ->setRequired(true)
            ->setMaxLength(60)
            ->setHelp('例如：v1.0.0')
        ;

        yield TextareaField::new('content', '协议内容')
            ->setMaxLength(65535)
            ->hideOnIndex()
            ->setHelp('协议的详细内容文本')
        ;

        yield UrlField::new('pdfUrl', 'PDF链接')
            ->hideOnIndex()
            ->setHelp('协议PDF文件的URL地址')
        ;

        yield BooleanField::new('required', '是否必需')
            ->setHelp('用户是否必须同意此协议')
        ;

        yield DateTimeField::new('effectiveTime', '生效时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('协议开始生效的时间')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
            ->setSortable(true)
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
            ->setSortable(true)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewPdf = Action::new('viewPdf', '查看PDF', 'fa fa-file-pdf')
            ->linkToUrl(function (ProtocolEntity $entity) {
                return $entity->getPdfUrl() ?? '#';
            })
            ->displayIf(fn (ProtocolEntity $entity) => null !== $entity->getPdfUrl() && '' !== $entity->getPdfUrl())
            ->setHtmlAttributes(['target' => '_blank'])
            ->addCssClass('btn btn-info')
        ;

        $duplicateVersion = Action::new('duplicateVersion', '创建新版本', 'fa fa-copy')
            ->linkToCrudAction('duplicateVersion')
            ->addCssClass('btn btn-secondary')
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $viewPdf)
            ->add(Crud::PAGE_EDIT, $duplicateVersion)
            ->add(Crud::PAGE_DETAIL, $viewPdf)
            ->add(Crud::PAGE_DETAIL, $duplicateVersion)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye');
            })
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('valid', '是否有效'))
            ->add(ChoiceFilter::new('type', '协议类型')->setChoices([
                '用户注册协议' => ProtocolType::MEMBER_REGISTER->value,
                '用户使用协议' => ProtocolType::MEMBER_USAGE->value,
                '隐私协议' => ProtocolType::PRIVACY->value,
                '营销推送' => ProtocolType::SALE_PUSH->value,
            ]))
            ->add(BooleanFilter::new('required', '是否必需'))
            ->add(TextFilter::new('title', '协议名称'))
            ->add(TextFilter::new('version', '版本号'))
            ->add(DateTimeFilter::new('effectiveTime', '生效时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
