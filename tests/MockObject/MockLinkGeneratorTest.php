<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\MockObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\MockObject\MockLinkGenerator;

/**
 * Mock链接生成器测试
 * @internal
 */
#[CoversClass(MockLinkGenerator::class)]
final class MockLinkGeneratorTest extends TestCase
{
    public function testGenerateReturnsCorrectFormat(): void
    {
        $generator = new MockLinkGenerator();

        $result = $generator->generate('test_route', ['param' => 'value']);

        $this->assertEquals('/mock/test_route', $result);
    }

    public function testGenerateFromNameWithObject(): void
    {
        $generator = new MockLinkGenerator();
        $object = new \stdClass();

        $result = $generator->generateFromName($object);

        $this->assertEquals('/mock/' . \stdClass::class, $result);
    }

    public function testGenerateFromNameWithString(): void
    {
        $generator = new MockLinkGenerator();

        $result = $generator->generateFromName('test_name');

        $this->assertEquals('/mock/test_name', $result);
    }

    public function testGetCurdListPage(): void
    {
        $generator = new MockLinkGenerator();

        $result = $generator->getCurdListPage('Test\Entity\User');

        $this->assertEquals('/mock/crud/Test/Entity/User', $result);
    }

    public function testExtractEntityFqcnWithValidCrudUrl(): void
    {
        $generator = new MockLinkGenerator();

        $result = $generator->extractEntityFqcn('/admin/test/entity/crud/Test/Entity/User');

        $this->assertEquals('Test\Entity\User', $result);
    }

    public function testExtractEntityFqcnWithInvalidUrl(): void
    {
        $generator = new MockLinkGenerator();

        $result = $generator->extractEntityFqcn('/some/other/url');

        $this->assertNull($result);
    }

    public function testSetDashboard(): void
    {
        $generator = new MockLinkGenerator();

        $generator->setDashboard('Test\DashboardController');

        $this->assertEquals('Test\DashboardController', $generator->getDefaultDashboard());
    }

    public function testGetDefaultDashboardWhenNotSet(): void
    {
        $generator = new MockLinkGenerator();

        $this->assertNull($generator->getDefaultDashboard());
    }
}
