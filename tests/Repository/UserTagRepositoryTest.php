<?php

namespace WechatWorkStaffBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Repository\UserTagRepository;

class UserTagRepositoryTest extends TestCase
{
    private UserTagRepository $repository;
    
    protected function setUp(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new UserTagRepository($registry);
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(UserTagRepository::class, $this->repository);
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
    
    public function testEntityClass(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        $this->assertEquals(UserTagRepository::class, $reflection->getName());
    }
} 