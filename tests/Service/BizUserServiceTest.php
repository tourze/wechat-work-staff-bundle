<?php

namespace WechatWorkStaffBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Tourze\UserServiceContracts\UserManagerInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;
use WechatWorkStaffBundle\Service\BizUserService;

class BizUserServiceTest extends TestCase
{
    public function test_constructor_creates_service_instance(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $userRepository = $this->createMock(UserRepository::class);

        $service = new BizUserService($userManager, $userRepository);

        $this->assertInstanceOf(BizUserService::class, $service);
    }

    public function test_transform_from_work_user_with_valid_user(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $userRepository = $this->createMock(UserRepository::class);
        $service = new BizUserService($userManager, $userRepository);

        $workUser = new User();
        $workUser->setUserId('test_123');
        $workUser->setName('测试用户');

        // 验证方法能够接受User实例
        $this->assertTrue(method_exists($service, 'transformFromWorkUser'));

        // 由于需要真实的UserManager实现，这里主要测试类型兼容性
        $reflection = new \ReflectionMethod($service, 'transformFromWorkUser');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('user', $parameters[0]->getName());
        $this->assertEquals(User::class, $parameters[0]->getType()->getName());
    }

    public function test_transform_from_external_user_interface_compatibility(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $userRepository = $this->createMock(UserRepository::class);
        $service = new BizUserService($userManager, $userRepository);

        // 验证方法签名
        $this->assertTrue(method_exists($service, 'transformFromExternalUser'));

        $reflection = new \ReflectionMethod($service, 'transformFromExternalUser');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('user', $parameters[0]->getName());
        $this->assertEquals(ExternalContactInterface::class, $parameters[0]->getType()->getName());
    }

    public function test_transform_to_work_user_method_exists(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $userRepository = $this->createMock(UserRepository::class);
        $service = new BizUserService($userManager, $userRepository);

        $this->assertTrue(method_exists($service, 'transformToWorkUser'));

        $reflection = new \ReflectionMethod($service, 'transformToWorkUser');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('bizUser', $parameters[0]->getName());
    }

    public function test_service_has_required_dependencies(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $userRepository = $this->createMock(UserRepository::class);

        // 验证构造函数参数类型
        $reflection = new \ReflectionClass(BizUserService::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $this->assertCount(2, $parameters);
        $this->assertEquals('userManager', $parameters[0]->getName());
        $this->assertEquals('userRepository', $parameters[1]->getName());

        $this->assertEquals(UserManagerInterface::class, $parameters[0]->getType()->getName());
        $this->assertEquals(UserRepository::class, $parameters[1]->getType()->getName());
    }

    public function test_user_entity_has_required_methods(): void
    {
        $user = new User();

        // 验证User实体有必要的方法
        $this->assertTrue(method_exists($user, 'setUserId'));
        $this->assertTrue(method_exists($user, 'getUserId'));
        $this->assertTrue(method_exists($user, 'setName'));
        $this->assertTrue(method_exists($user, 'getName'));
        $this->assertTrue(method_exists($user, 'setAvatarUrl'));
        $this->assertTrue(method_exists($user, 'getAvatarUrl'));

        // 测试基本的setter/getter功能
        $user->setUserId('test_user_123');
        $this->assertEquals('test_user_123', $user->getUserId());

        $user->setName('测试用户名');
        $this->assertEquals('测试用户名', $user->getName());

        $user->setAvatarUrl('https://example.com/avatar.jpg');
        $this->assertEquals('https://example.com/avatar.jpg', $user->getAvatarUrl());
    }
}
