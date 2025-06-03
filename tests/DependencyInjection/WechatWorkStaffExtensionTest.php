<?php

namespace WechatWorkStaffBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Extension\Extension;
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
        $this->assertInstanceOf(WechatWorkStaffExtension::class, $this->extension);
        $this->assertInstanceOf(Extension::class, $this->extension);
    }
    
    public function testExtensionHasLoadMethod(): void
    {
        $this->assertTrue(method_exists($this->extension, 'load'));
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