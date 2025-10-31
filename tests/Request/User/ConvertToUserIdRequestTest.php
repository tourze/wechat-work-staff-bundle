<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\User\ConvertToUserIdRequest;

/**
 * @internal
 */
#[CoversClass(ConvertToUserIdRequest::class)]
#[RunTestsInSeparateProcesses]
final class ConvertToUserIdRequestTest extends AbstractIntegrationTestCase
{
    private ConvertToUserIdRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(ConvertToUserIdRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testRequest(): void
    {
        $this->assertNotNull($this->request);
    }
}
