<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatWorkStaffBundle\Command\CheckUserAvatarCommand;
use WechatWorkStaffBundle\Repository\UserRepository;

/**
 * @internal
 */
#[CoversClass(CheckUserAvatarCommand::class)]
#[RunTestsInSeparateProcesses]
final class CheckUserAvatarCommandTest extends AbstractCommandTestCase
{
    private CheckUserAvatarCommand $command;

    private UserRepository $userRepository;

    protected function onSetUp(): void
    {
        // 使用真实的 UserRepository 进行集成测试
        $this->userRepository = self::getService(UserRepository::class);
        $this->command = self::getService(CheckUserAvatarCommand::class);
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
        $this->assertSame('wechat-work:check-user-avatar', $this->command->getName());
        $this->assertSame('检查用户头像并保存', $this->command->getDescription());
    }

    public function testCommandIsInstantiable(): void
    {
        // 简单验证命令可以被实例化
        $this->assertNotNull($this->command);
    }

    public function testCommandExecutionWithEmptyUserList(): void
    {
        // 集成测试：测试命令能够正常执行
        // 由于命令涉及数据库查询和网络请求，这里只测试基本执行
        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute([]);

        // 命令应该成功执行（即使没有符合条件的用户）
        $this->assertSame(0, $exitCode);
    }
}
