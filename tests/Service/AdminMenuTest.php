<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatWorkStaffBundle\Service\AdminMenu;

/**
 * AdminMenu服务测试
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AdminMenu tests
    }

    public function testServiceImplementsMenuProviderInterface(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);
        $this->assertInstanceOf(MenuProviderInterface::class, $adminMenu);
    }

    public function testServiceClassShouldBeFinal(): void
    {
        $reflection = new \ReflectionClass(AdminMenu::class);
        $this->assertTrue($reflection->isFinal());
    }

    public function testInvokeAddsMenuItems(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);
        self::assertInstanceOf(AdminMenu::class, $adminMenu);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证菜单结构
        $wechatMenu = $rootItem->getChild('企业微信管理');
        self::assertNotNull($wechatMenu, '企业微信管理菜单应该存在');

        $staffMenu = $wechatMenu->getChild('员工管理');
        self::assertNotNull($staffMenu, '员工管理菜单应该存在');

        // 验证子菜单
        self::assertNotNull($staffMenu->getChild('员工列表'), '员工列表菜单应该存在');
        self::assertNotNull($staffMenu->getChild('部门管理'), '部门管理菜单应该存在');
        self::assertNotNull($staffMenu->getChild('员工标签'), '员工标签菜单应该存在');
        self::assertNotNull($staffMenu->getChild('应用用户'), '应用用户菜单应该存在');
    }

    public function testMenuItemsHaveIcons(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);
        self::assertInstanceOf(AdminMenu::class, $adminMenu);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        $wechatMenu = $rootItem->getChild('企业微信管理');
        self::assertNotNull($wechatMenu, '企业微信管理菜单应该存在');

        $staffMenu = $wechatMenu->getChild('员工管理');
        self::assertNotNull($staffMenu, '员工管理菜单应该存在');

        // 检查图标属性
        self::assertSame('fas fa-wechat', $wechatMenu->getAttribute('icon'));
        self::assertSame('fas fa-users', $staffMenu->getAttribute('icon'));

        $userListMenu = $staffMenu->getChild('员工列表');
        self::assertNotNull($userListMenu, '员工列表菜单应该存在');
        self::assertSame('fas fa-user', $userListMenu->getAttribute('icon'));

        $departmentMenu = $staffMenu->getChild('部门管理');
        self::assertNotNull($departmentMenu, '部门管理菜单应该存在');
        self::assertSame('fas fa-sitemap', $departmentMenu->getAttribute('icon'));

        $tagMenu = $staffMenu->getChild('员工标签');
        self::assertNotNull($tagMenu, '员工标签菜单应该存在');
        self::assertSame('fas fa-tags', $tagMenu->getAttribute('icon'));

        $appUserMenu = $staffMenu->getChild('应用用户');
        self::assertNotNull($appUserMenu, '应用用户菜单应该存在');
        self::assertSame('fas fa-id-card', $appUserMenu->getAttribute('icon'));
    }
}
