<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Integration\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Repository\DepartmentRepository;

class DepartmentRepositoryTest extends TestCase
{
    public function testRepositoryIsServiceEntityRepository(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $repository = new DepartmentRepository($managerRegistry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }

    public function testRepositoryExists(): void
    {
        $this->assertTrue(class_exists(DepartmentRepository::class));
    }
}