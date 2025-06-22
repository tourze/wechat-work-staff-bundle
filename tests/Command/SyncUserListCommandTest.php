<?php

namespace WechatWorkStaffBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use WechatWorkStaffBundle\Command\SyncUserListCommand;
use WechatWorkStaffBundle\Message\SyncUserListMessage;
use WechatWorkStaffBundle\MessageHandler\SyncUserListHandler;

class SyncUserListCommandTest extends TestCase
{
    private SyncUserListCommand $command;
    private SyncUserListHandler&MockObject $handler;
    
    protected function setUp(): void
    {
        $this->handler = $this->createMock(SyncUserListHandler::class);
        $this->command = new SyncUserListCommand($this->handler);
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(SyncUserListCommand::class, $this->command);
    }
    
    public function testCommandConfiguration(): void
    {
        $this->assertSame('wechat-work:sync-user-list', $this->command->getName());
        $this->assertSame('同步指定企业的用户列表', $this->command->getDescription());
    }
    
    public function testCommandHasRequiredArgument(): void
    {
        $definition = $this->command->getDefinition();
        $this->assertTrue($definition->hasArgument('agentId'));
        
        $agentIdArgument = $definition->getArgument('agentId');
        $this->assertTrue($agentIdArgument->isRequired());
        $this->assertSame('应用ID', $agentIdArgument->getDescription());
    }
    
    public function testExecuteWithValidAgentId(): void
    {
        $agentId = 123;
        
        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (SyncUserListMessage $message) use ($agentId) {
                return $message->getAgentId() === $agentId;
            }));
        
        $commandTester = new CommandTester($this->command);
        $result = $commandTester->execute(['agentId' => (string)$agentId]);
        
        $this->assertSame(0, $result);
    }
    
    public function testExecuteWithDifferentAgentIds(): void
    {
        $testCases = [100, 200, 300];
        
        foreach ($testCases as $agentId) {
            $handler = $this->createMock(SyncUserListHandler::class);
            $command = new SyncUserListCommand($handler);
            
            $handler->expects($this->once())
                ->method('__invoke')
                ->with($this->callback(function (SyncUserListMessage $message) use ($agentId) {
                    return $message->getAgentId() === $agentId;
                }));
            
            $commandTester = new CommandTester($command);
            $result = $commandTester->execute(['agentId' => (string)$agentId]);
            
            $this->assertSame(0, $result);
        }
    }
    
    public function testExecuteCreatesCorrectMessage(): void
    {
        $agentId = 999;
        $capturedMessage = null;
        
        $this->handler->expects($this->once())
            ->method('__invoke')
            ->willReturnCallback(function (SyncUserListMessage $message) use (&$capturedMessage) {
                $capturedMessage = $message;
            });
        
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(['agentId' => (string)$agentId]);
        
        $this->assertNotNull($capturedMessage);
        $this->assertInstanceOf(SyncUserListMessage::class, $capturedMessage);
        $this->assertSame($agentId, $capturedMessage->getAgentId());
    }
}
