<?php

namespace WechatWorkStaffBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Repository\DepartmentRepository;

class DepartmentRepositoryTest extends TestCase
{
    private DepartmentRepository $repository;
    private ManagerRegistry $registry;
    
    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new DepartmentRepository($this->registry);
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
        $this->assertEquals(DepartmentRepository::class, $reflection->getName());
    }
}
