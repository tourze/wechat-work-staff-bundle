<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Service\AttributeControllerLoader;

class AttributeControllerLoaderTest extends TestCase
{
    public function testService(): void
    {
        $service = new AttributeControllerLoader();
        
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }
}