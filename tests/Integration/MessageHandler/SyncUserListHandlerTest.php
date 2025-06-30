<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Integration\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\MessageHandler\SyncUserListHandler;
use WechatWorkStaffBundle\Repository\DepartmentRepository;
use WechatWorkStaffBundle\Service\BizUserService;

class SyncUserListHandlerTest extends TestCase
{
    public function testMessageHandler(): void
    {
        $propertyAccessor = $this->createMock(PropertyAccessor::class);
        $agentRepository = $this->createMock(AgentRepository::class);
        $userLoader = $this->createMock(UserLoaderInterface::class);
        $departmentRepository = $this->createMock(DepartmentRepository::class);
        $bizUserService = $this->createMock(BizUserService::class);
        $workService = $this->createMock(WorkService::class);
        $logger = $this->createMock(LoggerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $handler = new SyncUserListHandler(
            $propertyAccessor,
            $agentRepository,
            $userLoader,
            $departmentRepository,
            $bizUserService,
            $workService,
            $logger,
            $entityManager
        );
        
        $this->assertInstanceOf(SyncUserListHandler::class, $handler);
    }
}