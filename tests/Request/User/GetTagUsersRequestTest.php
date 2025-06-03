<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\GetTagUsersRequest;

class GetTagUsersRequestTest extends TestCase
{
    private GetTagUsersRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetTagUsersRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(GetTagUsersRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testTagIdGetterAndSetter(): void
    {
        $tagId = '100';
        
        $this->request->setTagId($tagId);
        $this->assertSame($tagId, $this->request->getTagId());
    }
    
    public function testTagIdWithDifferentValues(): void
    {
        $testCases = [
            '1',
            '999',
            '12345',
            'tag-001',
            'custom_tag'
        ];
        
        foreach ($testCases as $tagId) {
            $this->request->setTagId($tagId);
            $this->assertSame($tagId, $this->request->getTagId());
        }
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/tag/get', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('GET', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptions(): void
    {
        $tagId = 'test_tag_123';
        $this->request->setTagId($tagId);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('tagid', $options['query']);
        $this->assertSame($tagId, $options['query']['tagid']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 