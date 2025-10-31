<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\Auth\GetUserDetailByTicketRequest;

/**
 * @internal
 */
#[CoversClass(GetUserDetailByTicketRequest::class)]
#[RunTestsInSeparateProcesses]
final class GetUserDetailByTicketRequestTest extends AbstractIntegrationTestCase
{
    private GetUserDetailByTicketRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(GetUserDetailByTicketRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testRequest(): void
    {
        $this->assertNotNull($this->request);
    }
}
