<?php

namespace WechatWorkStaffBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WechatWorkStaffBundle\WechatWorkStaffBundle;

class WechatWorkStaffBundleTest extends TestCase
{
    private WechatWorkStaffBundle $bundle;
    
    protected function setUp(): void
    {
        $this->bundle = new WechatWorkStaffBundle();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(WechatWorkStaffBundle::class, $this->bundle);
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }
    
    public function testBundleHasBuildMethod(): void
    {
        // Bundle 基类总是有 build 方法，移除不必要的测试
        $this->markTestSkipped('Bundle base class always has build method');
    }
    
    public function testBuildMethodSignature(): void
    {
        $reflection = new \ReflectionClass(WechatWorkStaffBundle::class);
        $buildMethod = $reflection->getMethod('build');
        $parameters = $buildMethod->getParameters();
        
        $this->assertCount(1, $parameters, 'build方法应该有1个参数');
        $this->assertEquals('container', $parameters[0]->getName());
    }
    
    public function testBundleIsInstantiable(): void
    {
        $this->assertNotNull($this->bundle);
        $this->assertInstanceOf(WechatWorkStaffBundle::class, $this->bundle);
    }
    
    public function testBuildWithContainerBuilder(): void
    {
        $container = $this->createMock(ContainerBuilder::class);
        
        // 测试build方法可以被调用而不抛出异常
        $container->expects($this->exactly(2))
            ->method('addCompilerPass');
        
        $this->bundle->build($container);
        
        // 如果没有异常抛出，测试通过
        $this->assertTrue(true);
    }
}
