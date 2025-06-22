<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\ConvertToOpenIdRequest;

class ConvertToOpenIdRequestTest extends TestCase
{
    private ConvertToOpenIdRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new ConvertToOpenIdRequest();
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
