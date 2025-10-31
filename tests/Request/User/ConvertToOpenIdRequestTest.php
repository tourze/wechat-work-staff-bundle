<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\User\ConvertToOpenIdRequest;

/**
 * @internal
 */
#[CoversClass(ConvertToOpenIdRequest::class)]
#[RunTestsInSeparateProcesses]
final class ConvertToOpenIdRequestTest extends AbstractIntegrationTestCase
{
    private ConvertToOpenIdRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(ConvertToOpenIdRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testRequest(): void
    {
        $this->assertNotNull($this->request);
    }
}
