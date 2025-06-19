<?php

namespace WechatWorkStaffBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\DoctrineResolveTargetEntityBundle\DependencyInjection\Compiler\ResolveTargetEntityPass;
use Tourze\WechatWorkContracts\DepartmentInterface;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Entity\User;

class WechatWorkStaffBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
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
}
