<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use WechatWorkStaffBundle\Entity\AgentUser;

#[AdminCrud(routePath: '/wechat-work-staff/agent-user', routeName: 'wechat_work_staff_agent_user')]
final class AgentUserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AgentUser::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();
        yield TextField::new('userId', '企业用户ID')
            ->setRequired(true)
            ->setHelp('企业内用户的唯一标识')
        ;
        yield TextField::new('openId', 'OpenID')
            ->setRequired(true)
            ->setHelp('用于支付等业务的开放ID')
        ;
        yield AssociationField::new('agent', '应用');
        yield DateTimeField::new('createTime', '创建时间')->hideOnForm();
        yield DateTimeField::new('updateTime', '更新时间')->hideOnForm();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('userId')
            ->add('openId')
            ->add('agent')
            ->add('createTime')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('应用用户')
            ->setEntityLabelInPlural('应用用户')
            ->setPageTitle('index', '应用用户管理')
            ->setPageTitle('new', '创建应用用户')
            ->setPageTitle('edit', '编辑应用用户')
        ;
    }
}
