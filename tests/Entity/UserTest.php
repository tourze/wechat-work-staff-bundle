<?php

namespace WechatWorkStaffBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Entity\UserTag;

class UserTest extends TestCase
{
    private User $user;
    
    protected function setUp(): void
    {
        $this->user = new User();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->user->getDepartments());
        $this->assertInstanceOf(ArrayCollection::class, $this->user->getTags());
        $this->assertCount(0, $this->user->getDepartments());
        $this->assertCount(0, $this->user->getTags());
    }
    
    public function testToString_WithoutId(): void
    {
        $this->assertSame('', (string) $this->user);
    }
    
    public function testToString_WithIdAndData(): void
    {
        $reflection = new \ReflectionClass(User::class);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->user, 123);
        
        $this->user->setUserId('test-user');
        $this->user->setName('Test User');
        
        $this->assertSame('Test User(test-user)', (string) $this->user);
    }
    
    public function testGetterAndSetter_ForBasicProperties(): void
    {
        $this->user->setUserId('test-user');
        $this->user->setName('Test User');
        $this->user->setAlias('Test Alias');
        $this->user->setMobile('13800138000');
        $this->user->setEmail('test@example.com');
        $this->user->setPosition('Developer');
        $this->user->setOpenUserId('open-user-id');
        $this->user->setAvatarUrl('http://example.com/avatar.jpg');
        
        $this->assertSame('test-user', $this->user->getUserId());
        $this->assertSame('Test User', $this->user->getName());
        $this->assertSame('Test Alias', $this->user->getAlias());
        $this->assertSame('13800138000', $this->user->getMobile());
        $this->assertSame('test@example.com', $this->user->getEmail());
        $this->assertSame('Developer', $this->user->getPosition());
        $this->assertSame('open-user-id', $this->user->getOpenUserId());
        $this->assertSame('http://example.com/avatar.jpg', $this->user->getAvatarUrl());
    }
    
    public function testGetterAndSetter_ForObjectProperties(): void
    {
        $corp = $this->createMock(Corp::class);
        $agent = $this->createMock(Agent::class);
        
        $this->user->setCorp($corp);
        $this->user->setAgent($agent);
        
        $this->assertSame($corp, $this->user->getCorp());
        $this->assertSame($agent, $this->user->getAgent());
    }
    
    public function testGetterAndSetter_ForTrackingProperties(): void
    {
        $now = new \DateTime();
        
        $this->user->setCreatedBy('admin');
        $this->user->setUpdatedBy('manager');
        $this->user->setCreatedFromIp('192.168.1.1');
        $this->user->setUpdatedFromIp('192.168.1.2');
        $this->user->setCreateTime($now);
        $this->user->setUpdateTime($now);
        
        $this->assertSame('admin', $this->user->getCreatedBy());
        $this->assertSame('manager', $this->user->getUpdatedBy());
        $this->assertSame('192.168.1.1', $this->user->getCreatedFromIp());
        $this->assertSame('192.168.1.2', $this->user->getUpdatedFromIp());
        $this->assertSame($now, $this->user->getCreateTime());
        $this->assertSame($now, $this->user->getUpdateTime());
    }
    
    public function testDepartmentCollection(): void
    {
        $department = $this->createMock(Department::class);
        
        $this->assertCount(0, $this->user->getDepartments());
        
        $this->user->addDepartment($department);
        $this->assertCount(1, $this->user->getDepartments());
        $this->assertTrue($this->user->getDepartments()->contains($department));
        
        $this->user->removeDepartment($department);
        $this->assertCount(0, $this->user->getDepartments());
        $this->assertFalse($this->user->getDepartments()->contains($department));
    }
    
    public function testTagCollection(): void
    {
        $tag = $this->createMock(UserTag::class);
        
        $this->assertCount(0, $this->user->getTags());
        
        $this->user->addTag($tag);
        $this->assertCount(1, $this->user->getTags());
        $this->assertTrue($this->user->getTags()->contains($tag));
        
        $this->user->removeTag($tag);
        $this->assertCount(0, $this->user->getTags());
        $this->assertFalse($this->user->getTags()->contains($tag));
    }
} 