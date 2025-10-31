<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\UserServiceContracts\UserManagerInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;
use WechatWorkStaffBundle\Service\BizUserService;

/**
 * @internal
 */
#[CoversClass(BizUserService::class)]
#[RunTestsInSeparateProcesses]
final class BizUserServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 此测试类不需要特定的设置逻辑
    }

    public function testServiceHasRequiredDependencies(): void
    {
        // 验证构造函数参数类型
        $reflection = new \ReflectionClass(BizUserService::class);
        $constructor = $reflection->getConstructor();
        if (null === $constructor) {
            self::fail('BizUserService constructor should not be null');
        }

        $parameters = $constructor->getParameters();

        $this->assertCount(2, $parameters);
        $this->assertEquals('userManager', $parameters[0]->getName());
        $this->assertEquals('userRepository', $parameters[1]->getName());

        $type0 = $parameters[0]->getType();
        if ($type0 instanceof \ReflectionNamedType) {
            $this->assertEquals(UserManagerInterface::class, $type0->getName());
        }

        $type1 = $parameters[1]->getType();
        if ($type1 instanceof \ReflectionNamedType) {
            $this->assertEquals(UserRepository::class, $type1->getName());
        }
    }

    public function testConstructorCreatesServiceInstance(): void
    {
        $service = self::getService(BizUserService::class);
        $this->assertInstanceOf(BizUserService::class, $service);
    }

    public function testTransformFromWorkUserWithValidUser(): void
    {
        $service = self::getService(BizUserService::class);

        $workUser = new User();
        $workUser->setUserId('test_123');
        $workUser->setName('测试用户');

        // 由于需要真实的UserManager实现，这里主要测试类型兼容性
        $reflection = new \ReflectionMethod($service, 'transformFromWorkUser');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('user', $parameters[0]->getName());

        $type = $parameters[0]->getType();
        if ($type instanceof \ReflectionNamedType) {
            $this->assertEquals(User::class, $type->getName());
        }
    }

    public function testTransformFromExternalUserInterfaceCompatibility(): void
    {
        $service = self::getService(BizUserService::class);

        $reflection = new \ReflectionMethod($service, 'transformFromExternalUser');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('user', $parameters[0]->getName());

        $type = $parameters[0]->getType();
        if ($type instanceof \ReflectionNamedType) {
            $this->assertEquals(ExternalContactInterface::class, $type->getName());
        }
    }

    public function testTransformToWorkUserMethodExists(): void
    {
        $service = self::getService(BizUserService::class);

        $reflection = new \ReflectionMethod($service, 'transformToWorkUser');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('bizUser', $parameters[0]->getName());
    }

    public function testUserEntityHasRequiredMethods(): void
    {
        $user = new User();

        // 测试基本的setter/getter功能
        $user->setUserId('test_user_123');
        $this->assertEquals('test_user_123', $user->getUserId());

        $user->setName('测试用户名');
        $this->assertEquals('测试用户名', $user->getName());

        $user->setAvatarUrl('https://example.com/avatar.jpg');
        $this->assertEquals('https://example.com/avatar.jpg', $user->getAvatarUrl());
    }
}
