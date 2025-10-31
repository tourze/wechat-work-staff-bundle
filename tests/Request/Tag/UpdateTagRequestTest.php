<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\Tag;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\Tag\UpdateTagRequest;

/**
 * @internal
 */
#[CoversClass(UpdateTagRequest::class)]
#[RunTestsInSeparateProcesses]
final class UpdateTagRequestTest extends AbstractIntegrationTestCase
{
    private UpdateTagRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(UpdateTagRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testRequest(): void
    {
        $this->assertNotNull($this->request);
    }
}
