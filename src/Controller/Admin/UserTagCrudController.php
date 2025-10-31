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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use WechatWorkStaffBundle\Entity\UserTag;

#[AdminCrud(routePath: '/wechat-work-staff/user-tag', routeName: 'wechat_work_staff_user_tag')]
final class UserTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserTag::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();
        yield TextField::new('name', '标签名称')
            ->setRequired(true)
            ->setHelp('成员标签的名称')
        ;
        yield IntegerField::new('tagId', '企微标签ID')
            ->setHelp('企业微信系统中的标签ID')
            ->hideOnIndex()
        ;
        yield AssociationField::new('corp', '企业')->hideOnIndex();
        yield AssociationField::new('agent', '应用')->hideOnIndex();
        yield AssociationField::new('users', '关联成员')
            ->setFormTypeOptions(['by_reference' => false])
            ->onlyOnDetail()
        ;
        yield DateTimeField::new('createTime', '创建时间')->hideOnForm();
        yield DateTimeField::new('updateTime', '更新时间')->hideOnForm();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('tagId')
            ->add('corp')
            ->add('agent')
            ->add('createTime')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('用户标签')
            ->setEntityLabelInPlural('用户标签')
            ->setPageTitle('index', '用户标签管理')
            ->setPageTitle('new', '创建用户标签')
            ->setPageTitle('edit', '编辑用户标签')
        ;
    }
}
