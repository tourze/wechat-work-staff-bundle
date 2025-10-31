<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Command\SyncUserTagsCommand;

/**
 * @internal
 */
#[CoversClass(SyncUserTagsCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncUserTagsCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 不需要额外的设置，使用服务容器中的实际服务
    }

    protected function getCommandTester(): CommandTester
    {
        // 从服务容器获取命令实例
        $command = self::getContainer()->get(SyncUserTagsCommand::class);
        $this->assertInstanceOf(SyncUserTagsCommand::class, $command);

        return new CommandTester($command);
    }

    public function testCommandNameConstant(): void
    {
        // 测试常量是否正确定义
        $this->assertSame('wechat-work:sync-user-tags', SyncUserTagsCommand::NAME);
    }

    public function testCommandCanBeExecutedInTestEnvironment(): void
    {
        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([]);

        // 由于有 Mock 的服务，命令应该成功执行
        $this->assertSame(Command::SUCCESS, $exitCode);
    }
}
