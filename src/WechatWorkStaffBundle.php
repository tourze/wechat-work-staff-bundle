<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineResolveTargetEntityBundle\DependencyInjection\Compiler\ResolveTargetEntityPass;
use Tourze\JsonRPCSecurityBundle\JsonRPCSecurityBundle;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use Tourze\WechatWorkContracts\DepartmentInterface;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkBundle\WechatWorkBundle;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Entity\User;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class WechatWorkStaffBundle extends Bundle implements BundleDependencyInterface
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new ResolveTargetEntityPass(UserInterface::class, User::class),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            1000,
        );

        $container->addCompilerPass(
            new ResolveTargetEntityPass(DepartmentInterface::class, Department::class),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            1000,
        );
    }

    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            RoutingAutoLoaderBundle::class => ['all' => true],
            WechatWorkBundle::class => ['all' => true],
            JsonRPCSecurityBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
