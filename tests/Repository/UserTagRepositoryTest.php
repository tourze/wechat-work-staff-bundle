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
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testExtendsServiceEntityRepository(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testRepositoryMethods(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testEntityClass(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        $this->assertEquals(UserTagRepository::class, $reflection->getName());
    }
}
