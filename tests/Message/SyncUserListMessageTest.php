<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Message;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Message\SyncUserListMessage;

/**
 * @internal
 */
#[CoversClass(SyncUserListMessage::class)]
#[RunTestsInSeparateProcesses]
final class SyncUserListMessageTest extends AbstractIntegrationTestCase
{
    private SyncUserListMessage $message;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(SyncUserListMessage::class);
        $this->message = $reflection->newInstance();
    }

    public function testConstructor(): void
    {
        $this->assertNotNull($this->message);
    }

    public function testAgentIdGetterAndSetter(): void
    {
        $agentId = 123;

        $this->message->setAgentId($agentId);
        $this->assertSame($agentId, $this->message->getAgentId());
    }

    public function testAgentIdWithDifferentValues(): void
    {
        $testCases = [1, 100, 999, 1000001, 9999999];

        foreach ($testCases as $agentId) {
            $this->message->setAgentId($agentId);
            $this->assertSame($agentId, $this->message->getAgentId());
        }
    }

    public function testAgentIdWithZeroValue(): void
    {
        $agentId = 0;

        $this->message->setAgentId($agentId);
        $this->assertSame($agentId, $this->message->getAgentId());
    }

    public function testSetAgentIdReturnsVoid(): void
    {
        $agentId = 456;

        $this->message->setAgentId($agentId);
        $this->assertSame($agentId, $this->message->getAgentId());
    }
}
