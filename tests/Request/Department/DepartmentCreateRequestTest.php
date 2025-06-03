<?php

namespace WechatWorkStaffBundle\Tests\Request\Department;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Department\DepartmentCreateRequest;

class DepartmentCreateRequestTest extends TestCase
{
    private DepartmentCreateRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new DepartmentCreateRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(DepartmentCreateRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testIdGetterAndSetter(): void
    {
        $id = 100;
        
        $this->request->setId($id);
        $this->assertSame($id, $this->request->getId());
    }
    
    public function testNameGetterAndSetter(): void
    {
        $name = '技术部';
        
        $this->request->setName($name);
        $this->assertSame($name, $this->request->getName());
    }
    
    public function testEnNameGetterAndSetter(): void
    {
        $enName = 'Tech Department';
        
        $this->request->setEnName($enName);
        $this->assertSame($enName, $this->request->getEnName());
    }
    
    public function testEnNameWithNullValue(): void
    {
        $this->request->setEnName(null);
        $this->assertNull($this->request->getEnName());
    }
    
    public function testParentIdGetterAndSetter(): void
    {
        $parentId = 1;
        
        $this->request->setParentId($parentId);
        $this->assertSame($parentId, $this->request->getParentId());
    }
    
    public function testOrderGetterAndSetter(): void
    {
        $order = 100;
        
        $this->request->setOrder($order);
        $this->assertSame($order, $this->request->getOrder());
    }
    
    public function testOrderWithNullValue(): void
    {
        $this->request->setOrder(null);
        $this->assertNull($this->request->getOrder());
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/department/create', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('POST', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptionsWithRequiredFields(): void
    {
        $name = '技术部';
        $parentId = 1;
        
        $this->request->setName($name);
        $this->request->setParentId($parentId);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('name', $options['json']);
        $this->assertArrayHasKey('parentid', $options['json']);
        $this->assertSame($name, $options['json']['name']);
        $this->assertSame($parentId, $options['json']['parentid']);
    }
    
    public function testGetRequestOptionsWithAllFields(): void
    {
        $id = 100;
        $name = '技术部';
        $enName = 'Tech Department';
        $parentId = 1;
        $order = 500;
        
        $this->request->setId($id);
        $this->request->setName($name);
        $this->request->setEnName($enName);
        $this->request->setParentId($parentId);
        $this->request->setOrder($order);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('name', $options['json']);
        $this->assertArrayHasKey('name_en', $options['json']);
        $this->assertArrayHasKey('parentid', $options['json']);
        $this->assertArrayHasKey('order', $options['json']);
        $this->assertArrayHasKey('id', $options['json']);
        
        $this->assertSame($name, $options['json']['name']);
        $this->assertSame($enName, $options['json']['name_en']);
        $this->assertSame($parentId, $options['json']['parentid']);
        $this->assertSame($order, $options['json']['order']);
        $this->assertSame($id, $options['json']['id']);
    }
    
    public function testGetRequestOptionsWithOptionalFieldsNull(): void
    {
        $name = '技术部';
        $parentId = 1;
        
        $this->request->setName($name);
        $this->request->setParentId($parentId);
        $this->request->setEnName(null);
        $this->request->setOrder(null);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayNotHasKey('name_en', $options['json']);
        $this->assertArrayNotHasKey('order', $options['json']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 