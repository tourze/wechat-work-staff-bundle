<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\GetUserSimpleListRequest;

class GetUserSimpleListRequestTest extends TestCase
{
    private GetUserSimpleListRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetUserSimpleListRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(GetUserSimpleListRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testDepartmentIdGetterAndSetter(): void
    {
        $departmentId = 100;
        
        $this->request->setDepartmentId($departmentId);
        $this->assertSame($departmentId, $this->request->getDepartmentId());
    }
    
    public function testDepartmentIdWithDifferentValues(): void
    {
        $testCases = [1, 5, 100, 999, 9999];
        
        foreach ($testCases as $departmentId) {
            $this->request->setDepartmentId($departmentId);
            $this->assertSame($departmentId, $this->request->getDepartmentId());
        }
    }
    
    public function testFetchChildGetterAndSetter(): void
    {
        $fetchChild = true;
        
        $this->request->setFetchChild($fetchChild);
        $this->assertSame($fetchChild, $this->request->getFetchChild());
    }
    
    public function testFetchChildWithDifferentValues(): void
    {
        $testCases = [true, false];
        
        foreach ($testCases as $fetchChild) {
            $this->request->setFetchChild($fetchChild);
            $this->assertSame($fetchChild, $this->request->getFetchChild());
        }
    }
    
    public function testFetchChildWithNullValue(): void
    {
        $this->request->setFetchChild(null);
        $this->assertNull($this->request->getFetchChild());
    }
    
    public function testStatusGetterAndSetter(): void
    {
        $status = '1';
        
        $this->request->setStatus($status);
        $this->assertSame($status, $this->request->getStatus());
    }
    
    public function testStatusWithDifferentValues(): void
    {
        $testCases = ['0', '1', '2', '4', '3']; // 可叠加状态
        
        foreach ($testCases as $status) {
            $this->request->setStatus($status);
            $this->assertSame($status, $this->request->getStatus());
        }
    }
    
    public function testStatusWithNullValue(): void
    {
        $this->request->setStatus(null);
        $this->assertNull($this->request->getStatus());
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/user/simplelist', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('GET', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptionsWithRequiredFieldOnly(): void
    {
        $departmentId = 100;
        $this->request->setDepartmentId($departmentId);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('department_id', $options['query']);
        $this->assertSame($departmentId, $options['query']['department_id']);
        $this->assertArrayNotHasKey('fetch_child', $options['query']);
        $this->assertArrayNotHasKey('status', $options['query']);
    }
    
    public function testGetRequestOptionsWithAllFields(): void
    {
        $departmentId = 200;
        $fetchChild = true;
        $status = '1';
        
        $this->request->setDepartmentId($departmentId);
        $this->request->setFetchChild($fetchChild);
        $this->request->setStatus($status);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('department_id', $options['query']);
        $this->assertArrayHasKey('fetch_child', $options['query']);
        $this->assertArrayHasKey('status', $options['query']);
        
        $this->assertSame($departmentId, $options['query']['department_id']);
        $this->assertSame(1, $options['query']['fetch_child']); // true转为1
        $this->assertSame($status, $options['query']['status']);
    }
    
    public function testGetRequestOptionsWithFetchChildFalse(): void
    {
        $departmentId = 300;
        $fetchChild = false;
        
        $this->request->setDepartmentId($departmentId);
        $this->request->setFetchChild($fetchChild);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('fetch_child', $options['query']);
        $this->assertSame(0, $options['query']['fetch_child']); // false转为0
    }
    
    public function testGetRequestOptionsWithOptionalFieldsNull(): void
    {
        $departmentId = 400;
        
        $this->request->setDepartmentId($departmentId);
        $this->request->setFetchChild(null);
        $this->request->setStatus(null);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('department_id', $options['query']);
        $this->assertArrayNotHasKey('fetch_child', $options['query']);
        $this->assertArrayNotHasKey('status', $options['query']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
}
