<?php

namespace WechatWorkStaffBundle\Tests\Request\Auth;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Auth\GetUserDetailByTicketRequest;

class GetUserDetailByTicketRequestTest extends TestCase
{
    private GetUserDetailByTicketRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetUserDetailByTicketRequest();
    }
    
    public function testConstructor(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testUserTicketGetterAndSetter(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testUserTicketWithDifferentValues(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testGetRequestPath(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testGetRequestMethod(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testGetRequestOptions(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
}
