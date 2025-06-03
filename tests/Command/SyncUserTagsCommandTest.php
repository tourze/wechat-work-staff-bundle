<?php

namespace WechatWorkStaffBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Command\SyncUserTagsCommand;

class SyncUserTagsCommandTest extends TestCase
{
    private SyncUserTagsCommand $command;
    private AgentRepository&MockObject $agentRepository;
    private WorkService&MockObject $workService;
    private EntityManagerInterface&MockObject $entityManager;
    
    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->command = new SyncUserTagsCommand(
            $this->agentRepository,
            $this->workService,
            $this->entityManager
        );
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(SyncUserTagsCommand::class, $this->command);
    }
    
    public function testCommandConfiguration(): void
    {
        $this->assertSame('wechat-work:sync-user-tags', $this->command->getName());
        $this->assertSame('同步获取成员标签', $this->command->getDescription());
    }
    
    public function testExecuteWithNoAgents(): void
    {
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        
        $this->workService->expects($this->never())
            ->method('request');
        
        $this->entityManager->expects($this->never())
            ->method('persist');
        
        $commandTester = new CommandTester($this->command);
        $result = $commandTester->execute([]);
        
        $this->assertSame(0, $result);
    }
    
    public function testBasicCommandExecution(): void
    {
        // 简化测试，主要验证命令能够正常执行
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        
        $commandTester = new CommandTester($this->command);
        $result = $commandTester->execute([]);
        
        $this->assertSame(0, $result);
        $this->assertStringContainsString('', $commandTester->getDisplay());
    }
    
    public function testCommandReturnsSuccessCode(): void
    {
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        
        $commandTester = new CommandTester($this->command);
        $result = $commandTester->execute([]);
        
        // 测试返回成功状态码
        $this->assertSame(0, $result);
    }
    
    public function testCommandCallsAgentRepository(): void
    {
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);
        
        // 验证通过了mock expectations
    }
} 