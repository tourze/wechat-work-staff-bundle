<?php

namespace WechatWorkStaffBundle\Tests\Request\Tag;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Tag\CreateTagRequest;

class CreateTagRequestTest extends TestCase
{
    private CreateTagRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new CreateTagRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(CreateTagRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testNameGetterAndSetter(): void
    {
        $name = '高级用户';
        
        $this->request->setName($name);
        $this->assertSame($name, $this->request->getName());
    }
    
    public function testNameWithDifferentValues(): void
    {
        $testCases = [
            '普通用户',
            'VIP用户',
            '测试标签',
            'Test Tag',
            '32字符限制内的标签名称12345678901234567890'
        ];
        
        foreach ($testCases as $name) {
            $this->request->setName($name);
            $this->assertSame($name, $this->request->getName());
        }
    }
    
    public function testIdGetterAndSetter(): void
    {
        $id = 123;
        
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
        $this->assertSame('/cgi-bin/user/delete', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('POST', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptionsWithNameOnly(): void
    {
        $name = '测试标签';
        $this->request->setName($name);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('tagname', $options['json']);
        $this->assertSame($name, $options['json']['tagname']);
        $this->assertArrayNotHasKey('tagid', $options['json']);
    }
    
    public function testGetRequestOptionsWithNameAndId(): void
    {
        $name = '测试标签';
        $id = 123;
        
        $this->request->setName($name);
        $this->request->setId($id);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('tagname', $options['json']);
        $this->assertArrayHasKey('tagid', $options['json']);
        $this->assertSame($name, $options['json']['tagname']);
        $this->assertSame($id, $options['json']['tagid']);
    }
    
    public function testGetRequestOptionsWithIdNull(): void
    {
        $name = '测试标签';
        
        $this->request->setName($name);
        $this->request->setId(null);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('tagname', $options['json']);
        $this->assertArrayNotHasKey('tagid', $options['json']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 