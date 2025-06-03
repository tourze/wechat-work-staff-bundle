<?php

namespace WechatWorkStaffBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkStaffBundle\Repository\UserRepository;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;
    
    protected function setUp(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new UserRepository($registry);
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(UserRepository::class, $this->repository);
    }
    
    public function testImplementsUserLoaderInterface(): void
    {
        $this->assertInstanceOf(UserLoaderInterface::class, $this->repository);
    }
    
    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $this->repository);
    }
    
    public function testRepositoryMethods(): void
    {
        // 测试Repository基本方法存在
        $this->assertTrue(method_exists($this->repository, 'find'));
        $this->assertTrue(method_exists($this->repository, 'findOneBy'));
        $this->assertTrue(method_exists($this->repository, 'findAll'));
        $this->assertTrue(method_exists($this->repository, 'findBy'));
    }
    
    public function testUserLoaderInterfaceMethods(): void
    {
        // 测试UserLoaderInterface方法存在
        $this->assertTrue(method_exists($this->repository, 'loadUserByUserIdAndCorp'));
        $this->assertTrue(method_exists($this->repository, 'createUser'));
    }
    
    public function testEntityClass(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        $this->assertEquals(UserRepository::class, $reflection->getName());
    }
} 