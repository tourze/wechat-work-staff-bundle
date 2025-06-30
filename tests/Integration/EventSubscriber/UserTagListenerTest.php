<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\EventSubscriber\UserTagListener;

class UserTagListenerTest extends TestCase
{
    public function testEventSubscriber(): void
    {
        $workService = $this->createMock(WorkService::class);
        $listener = new UserTagListener($workService);
        
        $this->assertInstanceOf(UserTagListener::class, $listener);
    }
}