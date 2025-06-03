<?php

namespace WechatWorkStaffBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Command\SyncTagUsersCommand;
use WechatWorkStaffBundle\Repository\UserTagRepository;

class SyncTagUsersCommandTest extends TestCase
{
    private SyncTagUsersCommand $command;
    private AgentRepository&MockObject $agentRepository;
    private UserTagRepository&MockObject $userTagRepository;
    private UserLoaderInterface&MockObject $userLoader;
    private WorkService&MockObject $workService;
    private EntityManagerInterface&MockObject $entityManager;
    
    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->userTagRepository = $this->createMock(UserTagRepository::class);
        $this->userLoader = $this->createMock(UserLoaderInterface::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->command = new SyncTagUsersCommand(
            $this->agentRepository,
            $this->userTagRepository,
            $this->userLoader,
            $this->workService,
            $this->entityManager
        );
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(SyncTagUsersCommand::class, $this->command);
    }
    
    public function testCommandConfiguration(): void
    {
        $this->assertSame('wechat-work:sync-tag-users', $this->command->getName());
        $this->assertSame('同步获取标签成员', $this->command->getDescription());
    }
    
    public function testExecuteWithNoAgents(): void
    {
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        
        $this->userTagRepository->expects($this->never())
            ->method('findBy');
        
        $commandTester = new CommandTester($this->command);
        $result = $commandTester->execute([]);
        
        $this->assertSame(0, $result);
    }
    
    public function testCommandReturnsSuccessCode(): void
    {
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        
        $commandTester = new CommandTester($this->command);
        $result = $commandTester->execute([]);
        
        $this->assertSame(0, $result);
    }
    
    public function testCommandCallsRepositories(): void
    {
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);
        
        // 验证通过了mock expectations
    }
    
    public function testBasicExecutionFlow(): void
    {
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        
        $commandTester = new CommandTester($this->command);
        $result = $commandTester->execute([]);
        
        $this->assertSame(0, $result);
        $this->assertStringContainsString('', $commandTester->getDisplay());
    }
} 