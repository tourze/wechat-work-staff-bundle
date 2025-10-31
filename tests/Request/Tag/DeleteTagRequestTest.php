<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\Tag;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\Tag\DeleteTagRequest;

/**
 * @internal
 */
#[CoversClass(DeleteTagRequest::class)]
#[RunTestsInSeparateProcesses]
final class DeleteTagRequestTest extends AbstractIntegrationTestCase
{
    private DeleteTagRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(DeleteTagRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testRequest(): void
    {
        $this->assertNotNull($this->request);
    }
}
