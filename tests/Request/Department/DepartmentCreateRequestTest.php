<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\Department;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\Department\DepartmentCreateRequest;

/**
 * @internal
 */
#[CoversClass(DepartmentCreateRequest::class)]
#[RunTestsInSeparateProcesses]
final class DepartmentCreateRequestTest extends AbstractIntegrationTestCase
{
    private DepartmentCreateRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(DepartmentCreateRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/department/create', $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertEquals('POST', $this->request->getRequestMethod());
    }

    public function testSetAndGetId(): void
    {
        $id = 123;
        $this->request->setId($id);

        $this->assertEquals($id, $this->request->getId());
    }

    public function testSetAndGetName(): void
    {
        $name = '测试部门';
        $this->request->setName($name);

        $this->assertEquals($name, $this->request->getName());
    }

    public function testSetAndGetEnName(): void
    {
        $enName = 'Test Department';
        $this->request->setEnName($enName);

        $this->assertEquals($enName, $this->request->getEnName());
    }

    public function testSetAndGetParentId(): void
    {
        $parentId = 1;
        $this->request->setParentId($parentId);

        $this->assertEquals($parentId, $this->request->getParentId());
    }

    public function testSetAndGetOrder(): void
    {
        $order = 100;
        $this->request->setOrder($order);

        $this->assertEquals($order, $this->request->getOrder());
    }

    public function testGetRequestOptionsBasic(): void
    {
        $this->request->setName('测试部门');
        $this->request->setParentId(1);

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('name', $options['json']);
        $this->assertArrayHasKey('parentid', $options['json']);
        $this->assertEquals('测试部门', $options['json']['name']);
        $this->assertEquals(1, $options['json']['parentid']);
    }

    public function testGetRequestOptionsWithAllFields(): void
    {
        $this->request->setId(123);
        $this->request->setName('测试部门');
        $this->request->setEnName('Test Department');
        $this->request->setParentId(1);
        $this->request->setOrder(100);

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertEquals(123, $options['json']['id']);
        $this->assertEquals('测试部门', $options['json']['name']);
        $this->assertEquals('Test Department', $options['json']['name_en']);
        $this->assertEquals(1, $options['json']['parentid']);
        $this->assertEquals(100, $options['json']['order']);
    }
}
