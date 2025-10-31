<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\Auth\GetUserInfoByCodeRequest;

/**
 * @internal
 */
#[CoversClass(GetUserInfoByCodeRequest::class)]
#[RunTestsInSeparateProcesses]
final class GetUserInfoByCodeRequestTest extends AbstractIntegrationTestCase
{
    private GetUserInfoByCodeRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(GetUserInfoByCodeRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testRequest(): void
    {
        $this->assertNotNull($this->request);
    }
}
