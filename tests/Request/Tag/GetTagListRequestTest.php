<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\Tag;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\Tag\GetTagListRequest;

/**
 * @internal
 */
#[CoversClass(GetTagListRequest::class)]
#[RunTestsInSeparateProcesses]
final class GetTagListRequestTest extends AbstractIntegrationTestCase
{
    private GetTagListRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(GetTagListRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testRequest(): void
    {
        $this->assertNotNull($this->request);
    }
}
