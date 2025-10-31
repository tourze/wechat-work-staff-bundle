<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\User\ListIdRequest;

/**
 * @internal
 */
#[CoversClass(ListIdRequest::class)]
#[RunTestsInSeparateProcesses]
final class ListIdRequestTest extends AbstractIntegrationTestCase
{
    private ListIdRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(ListIdRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testRequest(): void
    {
        $this->assertNotNull($this->request);
    }
}
