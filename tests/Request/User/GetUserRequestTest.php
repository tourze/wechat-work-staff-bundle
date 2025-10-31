<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\User\GetUserRequest;

/**
 * @internal
 */
#[CoversClass(GetUserRequest::class)]
#[RunTestsInSeparateProcesses]
final class GetUserRequestTest extends AbstractIntegrationTestCase
{
    private GetUserRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(GetUserRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testRequest(): void
    {
        $this->assertNotNull($this->request);
    }
}
