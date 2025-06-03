<?php

namespace WechatWorkStaffBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Entity\UserTag;

class UserTagTest extends TestCase
{
    private UserTag $userTag;
    
    protected function setUp(): void
    {
        $this->userTag = new UserTag();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->userTag->getUsers());
        $this->assertCount(0, $this->userTag->getUsers());
    }
    
    public function testSetAndGetId(): void
    {
        $this->assertSame(0, $this->userTag->getId());
    }
    
    public function testSetAndGetName(): void
    {
        $name = '高级工程师';
        
        $result = $this->userTag->setName($name);
        $this->assertSame($this->userTag, $result);
        $this->assertSame($name, $this->userTag->getName());
    }
    
    public function testSetAndGetAgent(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        
        $result = $this->userTag->setAgent($agent);
        $this->assertSame($this->userTag, $result);
        $this->assertSame($agent, $this->userTag->getAgent());
    }
    
    public function testSetAndGetAgentWithNull(): void
    {
        $result = $this->userTag->setAgent(null);
        $this->assertSame($this->userTag, $result);
        $this->assertNull($this->userTag->getAgent());
    }
    
    public function testSetAndGetCorp(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        
        $result = $this->userTag->setCorp($corp);
        $this->assertSame($this->userTag, $result);
        $this->assertSame($corp, $this->userTag->getCorp());
    }
    
    public function testSetAndGetCorpWithNull(): void
    {
        $result = $this->userTag->setCorp(null);
        $this->assertSame($this->userTag, $result);
        $this->assertNull($this->userTag->getCorp());
    }
    
    public function testSetAndGetTagId(): void
    {
        $tagId = 12345;
        
        $result = $this->userTag->setTagId($tagId);
        $this->assertSame($this->userTag, $result);
        $this->assertSame($tagId, $this->userTag->getTagId());
    }
    
    public function testSetAndGetTagIdWithNull(): void
    {
        $result = $this->userTag->setTagId(null);
        $this->assertSame($this->userTag, $result);
        $this->assertNull($this->userTag->getTagId());
    }
    
    public function testAddUser(): void
    {
        $user = new User();
        
        $result = $this->userTag->addUser($user);
        $this->assertSame($this->userTag, $result);
        $this->assertCount(1, $this->userTag->getUsers());
        $this->assertTrue($this->userTag->getUsers()->contains($user));
    }
    
    public function testAddUserTwiceDoesNotDuplicate(): void
    {
        $user = new User();
        
        $this->userTag->addUser($user);
        $this->userTag->addUser($user);
        
        $this->assertCount(1, $this->userTag->getUsers());
    }
    
    public function testRemoveUser(): void
    {
        $user = new User();
        $this->userTag->addUser($user);
        
        $result = $this->userTag->removeUser($user);
        $this->assertSame($this->userTag, $result);
        $this->assertCount(0, $this->userTag->getUsers());
    }
    
    public function testReplaceUsers(): void
    {
        $user1 = new User();
        $user1->setUserId('user1');
        
        $user2 = new User();
        $user2->setUserId('user2');
        
        $user3 = new User();
        $user3->setUserId('user3');
        
        // 首先添加一些用户
        $this->userTag->addUser($user1);
        $this->userTag->addUser($user2);
        $this->assertCount(2, $this->userTag->getUsers());
        
        // 创建新的用户集合
        $newUsers = new ArrayCollection([$user2, $user3]);
        
        $result = $this->userTag->replaceUsers($newUsers);
        $this->assertSame($this->userTag, $result);
        $this->assertCount(2, $this->userTag->getUsers());
        
        $this->assertFalse($this->userTag->getUsers()->contains($user1));
        $this->assertTrue($this->userTag->getUsers()->contains($user2));
        $this->assertTrue($this->userTag->getUsers()->contains($user3));
    }
    
    public function testReplaceUsersWithEmptyCollection(): void
    {
        $user1 = new User();
        $user2 = new User();
        
        $this->userTag->addUser($user1);
        $this->userTag->addUser($user2);
        $this->assertCount(2, $this->userTag->getUsers());
        
        $emptyCollection = new ArrayCollection([]);
        
        $result = $this->userTag->replaceUsers($emptyCollection);
        $this->assertSame($this->userTag, $result);
        $this->assertCount(0, $this->userTag->getUsers());
    }
    
    public function testSetAndGetCreatedBy(): void
    {
        $createdBy = 'admin';
        
        $result = $this->userTag->setCreatedBy($createdBy);
        $this->assertSame($this->userTag, $result);
        $this->assertSame($createdBy, $this->userTag->getCreatedBy());
    }
    
    public function testSetAndGetCreatedByWithNull(): void
    {
        $result = $this->userTag->setCreatedBy(null);
        $this->assertSame($this->userTag, $result);
        $this->assertNull($this->userTag->getCreatedBy());
    }
    
    public function testSetAndGetUpdatedBy(): void
    {
        $updatedBy = 'moderator';
        
        $result = $this->userTag->setUpdatedBy($updatedBy);
        $this->assertSame($this->userTag, $result);
        $this->assertSame($updatedBy, $this->userTag->getUpdatedBy());
    }
    
    public function testSetAndGetUpdatedByWithNull(): void
    {
        $result = $this->userTag->setUpdatedBy(null);
        $this->assertSame($this->userTag, $result);
        $this->assertNull($this->userTag->getUpdatedBy());
    }
    
    public function testSetAndGetCreatedFromIp(): void
    {
        $ip = '192.168.1.50';
        
        $result = $this->userTag->setCreatedFromIp($ip);
        $this->assertSame($this->userTag, $result);
        $this->assertSame($ip, $this->userTag->getCreatedFromIp());
    }
    
    public function testSetAndGetCreatedFromIpWithNull(): void
    {
        $result = $this->userTag->setCreatedFromIp(null);
        $this->assertSame($this->userTag, $result);
        $this->assertNull($this->userTag->getCreatedFromIp());
    }
    
    public function testSetAndGetUpdatedFromIp(): void
    {
        $ip = '10.0.0.25';
        
        $result = $this->userTag->setUpdatedFromIp($ip);
        $this->assertSame($this->userTag, $result);
        $this->assertSame($ip, $this->userTag->getUpdatedFromIp());
    }
    
    public function testSetAndGetUpdatedFromIpWithNull(): void
    {
        $result = $this->userTag->setUpdatedFromIp(null);
        $this->assertSame($this->userTag, $result);
        $this->assertNull($this->userTag->getUpdatedFromIp());
    }
    
    public function testSetAndGetCreateTime(): void
    {
        $dateTime = new \DateTime('2024-01-01 11:00:00');
        
        $this->userTag->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->userTag->getCreateTime());
    }
    
    public function testSetAndGetCreateTimeWithNull(): void
    {
        $this->userTag->setCreateTime(null);
        $this->assertNull($this->userTag->getCreateTime());
    }
    
    public function testSetAndGetUpdateTime(): void
    {
        $dateTime = new \DateTime('2024-01-02 17:30:00');
        
        $this->userTag->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->userTag->getUpdateTime());
    }
    
    public function testSetAndGetUpdateTimeWithNull(): void
    {
        $this->userTag->setUpdateTime(null);
        $this->assertNull($this->userTag->getUpdateTime());
    }
    
    public function testTagWithMultipleUsers(): void
    {
        $user1 = new User();
        $user1->setUserId('emp001');
        $user1->setName('员工1');
        
        $user2 = new User();
        $user2->setUserId('emp002');
        $user2->setName('员工2');
        
        $user3 = new User();
        $user3->setUserId('emp003');
        $user3->setName('员工3');
        
        $this->userTag->setName('团队核心');
        $this->userTag->setTagId(999);
        
        $this->userTag->addUser($user1);
        $this->userTag->addUser($user2);
        $this->userTag->addUser($user3);
        
        $this->assertCount(3, $this->userTag->getUsers());
        $this->assertSame('团队核心', $this->userTag->getName());
        $this->assertSame(999, $this->userTag->getTagId());
        
        $this->assertTrue($this->userTag->getUsers()->contains($user1));
        $this->assertTrue($this->userTag->getUsers()->contains($user2));
        $this->assertTrue($this->userTag->getUsers()->contains($user3));
    }
    
    public function testTagWithCompleteConfiguration(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        $agent = $this->createMock(AgentInterface::class);
        
        $this->userTag
            ->setName('资深专家')
            ->setAgent($agent)
            ->setCorp($corp)
            ->setTagId(12345)
            ->setCreatedBy('system')
            ->setUpdatedBy('admin');
        
        $this->assertSame('资深专家', $this->userTag->getName());
        $this->assertSame($agent, $this->userTag->getAgent());
        $this->assertSame($corp, $this->userTag->getCorp());
        $this->assertSame(12345, $this->userTag->getTagId());
        $this->assertSame('system', $this->userTag->getCreatedBy());
        $this->assertSame('admin', $this->userTag->getUpdatedBy());
    }
} 