<?php

namespace WechatWorkStaffBundle\Tests\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Entity\UserTag;
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
        $this->assertInstanceOf(UserTagListener::class, $this->listener);
    }
    
    public function testConstructorWithWorkService(): void
    {
        /** @var WorkService&MockObject $workService */
        $workService = $this->createMock(WorkService::class);
        $listener = new UserTagListener($workService);
        
        $this->assertInstanceOf(UserTagListener::class, $listener);
    }
    
    public function testPrePersistWithExistingTagIdAndName(): void
    {
        /** @var UserTag&MockObject $userTag */
        $userTag = $this->createMock(UserTag::class);
        
        // 模拟已经有tagId和name，应该直接返回
        $userTag->method('getTagId')->willReturn(123);
        $userTag->method('getName')->willReturn('测试标签');
        
        // 不应该调用WorkService
        $this->workService->expects($this->never())->method('request');
        
        $this->listener->prePersist($userTag);
    }
    
    public function testPrePersistWithMissingTagId(): void
    {
        /** @var UserTag&MockObject $userTag */
        $userTag = $this->createMock(UserTag::class);
        /** @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        
        // 模拟没有tagId但有name
        $userTag->method('getTagId')->willReturn(null);
        $userTag->method('getName')->willReturn('新建标签');
        $userTag->method('getAgent')->willReturn($agent);
        
        // 模拟API返回的响应
        $response = ['tagid' => 456];
        
        $this->workService
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);
        
        // 应该设置返回的tagId
        $userTag->expects($this->once())
            ->method('setTagId')
            ->with(456);
        
        $this->listener->prePersist($userTag);
    }
    
    public function testPrePersistWithoutTagIdInResponse(): void
    {
        /** @var UserTag&MockObject $userTag */
        $userTag = $this->createMock(UserTag::class);
        /** @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        
        $userTag->method('getTagId')->willReturn(null);
        $userTag->method('getName')->willReturn('新建标签');
        $userTag->method('getAgent')->willReturn($agent);
        
        // 模拟API返回没有tagid的响应
        $response = ['errcode' => 0, 'errmsg' => 'ok'];
        
        $this->workService
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);
        
        // 不应该调用setTagId
        $userTag->expects($this->never())
            ->method('setTagId');
        
        $this->listener->prePersist($userTag);
    }
    
    public function testPreRemove(): void
    {
        /** @var UserTag&MockObject $userTag */
        $userTag = $this->createMock(UserTag::class);
        /** @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        
        $userTag->method('getAgent')->willReturn($agent);
        $userTag->method('getTagId')->willReturn(123);
        
        $this->workService
            ->expects($this->once())
            ->method('request');
        
        $this->listener->preRemove($userTag);
    }
    
    public function testPreUpdate(): void
    {
        /** @var UserTag&MockObject $userTag */
        $userTag = $this->createMock(UserTag::class);
        /** @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        
        $userTag->method('getAgent')->willReturn($agent);
        $userTag->method('getTagId')->willReturn(123);
        
        $this->workService
            ->expects($this->once())
            ->method('request');
        
        $this->listener->preUpdate($userTag);
    }
    
    public function testMethodsExist(): void
    {
        $this->assertTrue(method_exists($this->listener, 'prePersist'));
        $this->assertTrue(method_exists($this->listener, 'preRemove'));
        $this->assertTrue(method_exists($this->listener, 'preUpdate'));
    }
} 