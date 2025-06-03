<?php

namespace WechatWorkStaffBundle\Tests\Request\Department;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkStaffBundle\Request\Department\GetDepartmentListRequest;

class GetDepartmentListRequestTest extends TestCase
{
    private GetDepartmentListRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetDepartmentListRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(GetDepartmentListRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testIdGetterAndSetter(): void
    {
        $id = 100;
        
        $this->request->setId($id);
        $this->assertSame($id, $this->request->getId());
    }
    
    public function testIdWithNullValue(): void
    {
        $this->request->setId(null);
        $this->assertNull($this->request->getId());
    }
    
    public function testIdWithDifferentValues(): void
    {
        $testCases = [0, 1, 100, 999, 9999];
        
        foreach ($testCases as $id) {
            $this->request->setId($id);
            $this->assertSame($id, $this->request->getId());
        }
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/department/list', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('GET', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptionsWithNoId(): void
    {
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertEmpty($options['query']);
    }
    
    public function testGetRequestOptionsWithNullId(): void
    {
        $this->request->setId(null);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertEmpty($options['query']);
    }
    
    public function testGetRequestOptionsWithId(): void
    {
        $id = 123;
        $this->request->setId($id);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('id', $options['query']);
        $this->assertSame($id, $options['query']['id']);
    }
    
    public function testGetRequestOptionsWithZeroId(): void
    {
        $id = 0;
        $this->request->setId($id);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertEmpty($options['query']); // 0 被视为空值
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
    
    public function testSetAndGetAgent(): void
    {
        $agent = $this->createMock(Agent::class);
        
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());
    }
} 