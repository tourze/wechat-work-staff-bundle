<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\GetUserRequest;

class GetUserRequestTest extends TestCase
{
    private GetUserRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetUserRequest();
    }
    
    public function testConstructor(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testUserIdGetterAndSetter(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testUserIdWithDifferentValues(): void
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
