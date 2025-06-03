<?php

namespace WechatWorkStaffBundle\Tests\Request\Auth;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Auth\AuthSuccessConfirmRequest;

class AuthSuccessConfirmRequestTest extends TestCase
{
    private AuthSuccessConfirmRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new AuthSuccessConfirmRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(AuthSuccessConfirmRequest::class, $this->request);
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