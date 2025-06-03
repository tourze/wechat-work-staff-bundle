<?php

namespace WechatWorkStaffBundle\Tests\Request\Tag;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Tag\DeleteTagRequest;

class DeleteTagRequestTest extends TestCase
{
    private DeleteTagRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new DeleteTagRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(DeleteTagRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
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
        $this->assertSame('/cgi-bin/tag/delete', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('GET', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptions(): void
    {
        $id = 456;
        $this->request->setId($id);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('tagid', $options['query']);
        $this->assertSame($id, $options['query']['tagid']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 