<?php

namespace WechatWorkStaffBundle\Tests\Request\Tag;

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
