<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatWorkStaffBundle\DependencyInjection\WechatWorkStaffExtension;

class WechatWorkStaffExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $extension = new WechatWorkStaffExtension();
        $container = new ContainerBuilder();
        
        $extension->load([], $container);
        
        $this->assertInstanceOf(ContainerBuilder::class, $container);
    }
}