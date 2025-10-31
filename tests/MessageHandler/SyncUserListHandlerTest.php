<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\MessageHandler;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\MessageHandler\SyncUserListHandler;

/**
 * @internal
 */
#[CoversClass(SyncUserListHandler::class)]
#[RunTestsInSeparateProcesses]
final class SyncUserListHandlerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 无需特殊设置
    }

    public function testMessageHandlerCanBeCreated(): void
    {
        // 获取容器中的处理器实例
        $handler = self::getService(SyncUserListHandler::class);

        $this->assertInstanceOf(SyncUserListHandler::class, $handler);
    }

    public function testMessageHandlerIsProperlyConfigured(): void
    {
        // 验证处理器可以在容器中正确配置
        self::assertTrue(self::getContainer()->has(SyncUserListHandler::class));

        $handler = self::getService(SyncUserListHandler::class);
        self::assertInstanceOf(SyncUserListHandler::class, $handler);
    }
}
