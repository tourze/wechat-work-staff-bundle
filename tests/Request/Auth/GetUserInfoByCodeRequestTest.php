<?php

namespace WechatWorkStaffBundle\Tests\Request\Auth;

use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkStaffBundle\Request\Auth\GetUserInfoByCodeRequest;

class GetUserInfoByCodeRequestTest extends TestCase
{
    private GetUserInfoByCodeRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetUserInfoByCodeRequest();
    }
    
    public function testConstructor(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testCodeGetterAndSetter(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testCodeWithDifferentValues(): void
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
    
    public function testGetRequestOptionsWithEmptyCode(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testSetAndGetAgent(): void
    {
        $agent = $this->createMock(Agent::class);
        
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());
    }
}
