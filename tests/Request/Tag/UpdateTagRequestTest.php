<?php

namespace WechatWorkStaffBundle\Tests\Request\Tag;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Tag\UpdateTagRequest;

class UpdateTagRequestTest extends TestCase
{
    private UpdateTagRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new UpdateTagRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(UpdateTagRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testNameGetterAndSetter(): void
    {
        $name = '更新标签名称';
        
        $this->request->setName($name);
        $this->assertSame($name, $this->request->getName());
    }
    
    public function testNameWithDifferentValues(): void
    {
        $testCases = [
            '技术部',
            'Tech Team',
            '销售团队Sales',
            'VIP客户群',
            '32个字符内的标签名称测试用例长度验证ABCD',
            'Short',
            'A'
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
    
    public function testIdWithDifferentValues(): void
    {
        $testCases = [1, 100, 999, 1234567890];
        
        foreach ($testCases as $id) {
            $this->request->setId($id);
            $this->assertSame($id, $this->request->getId());
        }
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/tag/update', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('POST', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptions(): void
    {
        $name = '测试更新标签';
        $id = 456;
        
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
    
    public function testGetRequestOptionsWithSpecialCharacters(): void
    {
        $name = '标签@#$%^&*()_+中文';
        $id = 789;
        
        $this->request->setName($name);
        $this->request->setId($id);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertSame($name, $options['json']['tagname']);
        $this->assertSame($id, $options['json']['tagid']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 