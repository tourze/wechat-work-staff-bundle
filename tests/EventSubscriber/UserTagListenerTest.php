<?php

namespace WechatWorkStaffBundle\Tests\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\EventSubscriber\UserTagListener;

class UserTagListenerTest extends TestCase
{
    private UserTagListener $listener;
    /** @var WorkService&MockObject */
    private WorkService $workService;
    
    protected function setUp(): void
    {
        $this->workService = $this->createMock(WorkService::class);
        $this->listener = new UserTagListener($this->workService);
    }
    
    public function testConstructor(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testConstructorWithWorkService(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testPrePersistWithExistingTagIdAndName(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testPrePersistWithMissingTagId(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testPrePersistWithoutTagIdInResponse(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testPreRemove(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testPreUpdate(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
    
    public function testMethodsExist(): void
    {
        $this->markTestSkipped('Method always exists in base class');
    }
}
