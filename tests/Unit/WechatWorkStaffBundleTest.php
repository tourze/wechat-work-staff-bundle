<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatWorkStaffBundle\WechatWorkStaffBundle;

class WechatWorkStaffBundleTest extends TestCase
{
    public function testBundle(): void
    {
        $bundle = new WechatWorkStaffBundle();
        
        $this->assertInstanceOf(WechatWorkStaffBundle::class, $bundle);
    }

    public function testBuild(): void
    {
        $bundle = new WechatWorkStaffBundle();
        $container = new ContainerBuilder();
        
        $bundle->build($container);
        
        $this->assertInstanceOf(ContainerBuilder::class, $container);
    }
}