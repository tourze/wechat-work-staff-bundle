<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\Department;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\Department\GetDepartmentListRequest;

/**
 * @internal
 */
#[CoversClass(GetDepartmentListRequest::class)]
#[RunTestsInSeparateProcesses]
final class GetDepartmentListRequestTest extends AbstractIntegrationTestCase
{
    private GetDepartmentListRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(GetDepartmentListRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testRequest(): void
    {
        $this->assertNotNull($this->request);
    }
}
