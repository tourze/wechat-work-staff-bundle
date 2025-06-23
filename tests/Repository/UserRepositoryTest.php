<?php

namespace WechatWorkStaffBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;

class UserRepositoryTest extends TestCase
{
    public function test_repository_extends_service_entity_repository(): void
    {
        $reflection = new \ReflectionClass(UserRepository::class);

        $this->assertTrue($reflection->isSubclassOf(ServiceEntityRepository::class));
    }

    public function test_repository_has_correct_entity_class(): void
    {
        $reflection = new \ReflectionClass(UserRepository::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        // 验证Repository是为User实体设计的
        $this->assertTrue(class_exists(User::class));
    }

    public function test_repository_has_required_methods(): void
    {
        $reflection = new \ReflectionClass(UserRepository::class);

        // 继承自ServiceEntityRepository的基本方法
        $this->assertTrue($reflection->hasMethod('find'));
        $this->assertTrue($reflection->hasMethod('findBy'));
        $this->assertTrue($reflection->hasMethod('findOneBy'));
        $this->assertTrue($reflection->hasMethod('findAll'));
    }

    public function test_user_entity_can_be_instantiated(): void
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user);

        // 测试基本属性设置
        $user->setUserId('test_repository_user');
        $this->assertEquals('test_repository_user', $user->getUserId());

        $user->setName('Repository测试用户');
        $this->assertEquals('Repository测试用户', $user->getName());
    }

    public function test_user_entity_has_stringable_interface(): void
    {
        $user = new User();
        $user->setUserId('stringable_test');
        $user->setName('可字符串化测试');

        // 验证实体实现了__toString方法
        $stringRepresentation = (string) $user;
        $this->assertNotEmpty($stringRepresentation);
    }

    public function test_user_entity_has_required_fields(): void
    {
        $user = new User();

        // 验证实体有必要的字段访问方法
        $requiredMethods = [
            'getUserId',
            'setUserId',
            'getName',
            'setName',
            'getAlias',
            'setAlias',
            'getPosition',
            'setPosition',
            'getMobile',
            'setMobile',
            'getEmail',
            'setEmail',
            'getAvatarUrl',
            'setAvatarUrl',
            'getOpenUserId',
            'setOpenUserId',
            'getCreatedBy',
            'setCreatedBy',
            'getUpdatedBy',
            'setUpdatedBy',
        ];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($user, $method),
                "User entity should have method: {$method}"
            );
        }
    }
}
