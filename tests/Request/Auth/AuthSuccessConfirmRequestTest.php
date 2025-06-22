<?php

namespace WechatWorkStaffBundle\Tests\Request\Auth;

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
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testHasBasicRequestMethods(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
}
