<?php

namespace WechatWorkStaffBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\DependencyInjection\WechatWorkStaffExtension;

class WechatWorkStaffExtensionTest extends TestCase
{
    private WechatWorkStaffExtension $extension;
    
    protected function setUp(): void
    {
        $this->extension = new WechatWorkStaffExtension();
    }
    
    public function testConstructor(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testExtensionHasLoadMethod(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testLoadMethodSignature(): void
    {
        $reflection = new \ReflectionClass(WechatWorkStaffExtension::class);
        $loadMethod = $reflection->getMethod('load');
        $parameters = $loadMethod->getParameters();
        
        $this->assertCount(2, $parameters, 'load方法应该有2个参数');
        $this->assertEquals('configs', $parameters[0]->getName());
        $this->assertEquals('container', $parameters[1]->getName());
    }
    
    public function testExtensionIsInstantiable(): void
    {
        $this->assertNotNull($this->extension);
        $this->assertInstanceOf(WechatWorkStaffExtension::class, $this->extension);
    }
}
