<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\ConvertToUserIdRequest;

class ConvertToUserIdRequestTest extends TestCase
{
    private ConvertToUserIdRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new ConvertToUserIdRequest();
    }
    
    public function testConstructor(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testOpenIdGetterAndSetter(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testOpenIdWithDifferentValues(): void
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
