<?php

namespace WechatWorkStaffBundle\Tests\Request\Tag;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Tag\GetTagListRequest;

class GetTagListRequestTest extends TestCase
{
    private GetTagListRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetTagListRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(GetTagListRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testHasBasicRequestMethods(): void
    {
        $this->assertTrue(method_exists($this->request, 'getRequestPath'));
        $this->assertTrue(method_exists($this->request, 'getRequestMethod'));
        $this->assertTrue(method_exists($this->request, 'getRequestOptions'));
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 