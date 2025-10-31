<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatWorkStaffBundle\Entity\AgentUser;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Entity\UserTag;

/**
 * 企业微信员工管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
final readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        // 创建企业微信管理主菜单
        if (null === $item->getChild('企业微信管理')) {
            $item->addChild('企业微信管理')
                ->setAttribute('icon', 'fas fa-wechat')
            ;
        }

        $wechatMenu = $item->getChild('企业微信管理');
        if (null === $wechatMenu) {
            return;
        }

        // 添加员工管理子菜单
        if (null === $wechatMenu->getChild('员工管理')) {
            $wechatMenu->addChild('员工管理')
                ->setAttribute('icon', 'fas fa-users')
            ;
        }

        $staffMenu = $wechatMenu->getChild('员工管理');
        if (null === $staffMenu) {
            return;
        }

        // 员工列表
        $staffMenu->addChild('员工列表')
            ->setUri($this->linkGenerator->getCurdListPage(User::class))
            ->setAttribute('icon', 'fas fa-user')
        ;

        // 部门管理
        $staffMenu->addChild('部门管理')
            ->setUri($this->linkGenerator->getCurdListPage(Department::class))
            ->setAttribute('icon', 'fas fa-sitemap')
        ;

        // 员工标签
        $staffMenu->addChild('员工标签')
            ->setUri($this->linkGenerator->getCurdListPage(UserTag::class))
            ->setAttribute('icon', 'fas fa-tags')
        ;

        // 应用用户
        $staffMenu->addChild('应用用户')
            ->setUri($this->linkGenerator->getCurdListPage(AgentUser::class))
            ->setAttribute('icon', 'fas fa-id-card')
        ;
    }
}
