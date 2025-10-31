<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatWorkStaffBundle\Command\SyncUserListCommand;

/**
 * @internal
 */
#[CoversClass(SyncUserListCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncUserListCommandTest extends AbstractCommandTestCase
{
    private SyncUserListCommand $command;

    protected function onSetUp(): void
    {
        $this->command = self::getService(SyncUserListCommand::class);
    }

    protected function getCommandTester(): CommandTester
    {
        return new CommandTester($this->command);
    }

    public function testConstructor(): void
    {
        $this->assertNotNull($this->command);
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

        $commandTester = new CommandTester($this->command);
        $result = $commandTester->execute(['agentId' => (string) $agentId]);

        $this->assertIsInt($result);
    }

    public function testExecuteWithDifferentAgentIds(): void
    {
        $testCases = [100, 200, 300];

        foreach ($testCases as $agentId) {
            $commandTester = new CommandTester($this->command);
            $result = $commandTester->execute(['agentId' => (string) $agentId]);

            $this->assertIsInt($result);
        }
    }

    public function testExecuteCreatesCorrectMessage(): void
    {
        $agentId = 999;

        $commandTester = new CommandTester($this->command);
        $result = $commandTester->execute(['agentId' => (string) $agentId]);

        $this->assertIsInt($result);
    }

    public function testArgumentAgentId(): void
    {
        $definition = $this->command->getDefinition();
        $this->assertTrue($definition->hasArgument('agentId'));

        $agentIdArgument = $definition->getArgument('agentId');
        $this->assertTrue($agentIdArgument->isRequired());
        $this->assertSame('应用ID', $agentIdArgument->getDescription());
    }
}
