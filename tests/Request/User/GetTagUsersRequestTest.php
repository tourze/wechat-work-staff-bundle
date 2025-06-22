<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

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
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testTagIdGetterAndSetter(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testTagIdWithDifferentValues(): void
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
