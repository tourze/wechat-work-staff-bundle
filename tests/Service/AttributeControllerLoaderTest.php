<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Service\AttributeControllerLoader;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 无需特殊设置
    }

    public function testService(): void
    {
        $service = self::getService(AttributeControllerLoader::class);

        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }

    public function testAutoload(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $result = $service->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);
        $this->assertGreaterThan(0, $result->count());
    }

    public function testSupports(): void
    {
        $service = self::getService(AttributeControllerLoader::class);

        $this->assertFalse($service->supports('test-resource'));
        $this->assertFalse($service->supports('test-resource', 'type'));
        $this->assertFalse($service->supports(null));
    }

    public function testLoad(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $result = $service->load('test-resource');

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testLoadWithType(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $result = $service->load('test-resource', 'test-type');

        $this->assertInstanceOf(RouteCollection::class, $result);
    }
}
