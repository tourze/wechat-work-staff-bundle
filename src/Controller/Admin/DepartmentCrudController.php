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
use WechatWorkStaffBundle\Entity\Department;

#[AdminCrud(routePath: '/wechat-work-staff/department', routeName: 'wechat_work_staff_department')]
final class DepartmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Department::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();
        yield IntegerField::new('remoteId', '远程ID')->hideOnIndex();
        yield TextField::new('name', '部门名称')->setRequired(true);
        yield TextField::new('enName', '英文名称')->hideOnIndex();
        yield TextField::new('sortNumber', '排序值')
            ->setHelp('值越大排序越靠前，范围[0, 2^32]')
            ->hideOnIndex()
        ;
        yield AssociationField::new('parent', '上级部门')
            ->setFormTypeOptions(['choice_label' => 'name'])
            ->hideOnIndex()
        ;
        yield AssociationField::new('corp', '企业')->hideOnIndex();
        yield AssociationField::new('agent', '应用')->hideOnIndex();
        yield AssociationField::new('children', '子部门')
            ->setFormTypeOptions(['by_reference' => false])
            ->onlyOnDetail()
        ;
        yield AssociationField::new('users', '成员')
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
            ->add('enName')
            ->add('parent')
            ->add('corp')
            ->add('agent')
            ->add('createTime')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('部门')
            ->setEntityLabelInPlural('部门')
            ->setPageTitle('index', '部门管理')
            ->setPageTitle('new', '创建部门')
            ->setPageTitle('edit', '编辑部门')
        ;
    }
}
