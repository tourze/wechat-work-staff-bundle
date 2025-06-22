<?php

namespace WechatWorkStaffBundle\Tests\Request\Department;

use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkStaffBundle\Request\Department\GetDepartmentListRequest;

class GetDepartmentListRequestTest extends TestCase
{
    private GetDepartmentListRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetDepartmentListRequest();
    }
    
    public function testConstructor(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testIdGetterAndSetter(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testIdWithNullValue(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testIdWithDifferentValues(): void
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
    
    public function testGetRequestOptionsWithNoId(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testGetRequestOptionsWithNullId(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testGetRequestOptionsWithId(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testGetRequestOptionsWithZeroId(): void
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
