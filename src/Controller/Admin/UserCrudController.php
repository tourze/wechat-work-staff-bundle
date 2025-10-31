<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use WechatWorkStaffBundle\Entity\User;

#[AdminCrud(routePath: '/wechat-work-staff/user', routeName: 'wechat_work_staff_user')]
final class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();
        yield TextField::new('userId', '用户ID')->setHelp('企业内必须唯一，最多64个字符');
        yield TextField::new('name', '姓名')->setRequired(true);
        yield TextField::new('alias', '别名')->hideOnIndex();
        yield TextField::new('position', '职务')->hideOnIndex();
        yield TextField::new('mobile', '手机号码')->setHelp('企业内必须唯一');
        yield EmailField::new('email', '邮箱地址')->hideOnIndex();
        yield TextField::new('openUserId', '全局用户ID')->hideOnIndex()->hideOnForm();
        yield UrlField::new('avatarUrl', '头像地址')->hideOnIndex()->hideOnForm();
        yield AssociationField::new('corp', '企业')->hideOnIndex();
        yield AssociationField::new('agent', '应用')->hideOnIndex();
        yield AssociationField::new('departments', '部门')
            ->setFormTypeOptions(['by_reference' => false])
            ->hideOnIndex()
        ;
        yield AssociationField::new('tags', '标签')
            ->setFormTypeOptions(['by_reference' => false])
            ->hideOnIndex()
        ;
        yield DateTimeField::new('createTime', '创建时间')->hideOnForm();
        yield DateTimeField::new('updateTime', '更新时间')->hideOnForm();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('userId')
            ->add('name')
            ->add('position')
            ->add('mobile')
            ->add('email')
            ->add('corp')
            ->add('agent')
            ->add('departments')
            ->add('tags')
            ->add('createTime')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('用户')
            ->setEntityLabelInPlural('用户')
            ->setPageTitle('index', '用户管理')
            ->setPageTitle('new', '创建用户')
            ->setPageTitle('edit', '编辑用户')
        ;
    }
}
