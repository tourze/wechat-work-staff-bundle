<?php

namespace WechatWorkStaffBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkStaffBundle\Entity\AgentUser;

class AgentUserTest extends TestCase
{
    private AgentUser $agentUser;
    
    protected function setUp(): void
    {
        $this->agentUser = new AgentUser();
    }
    
    public function testSetAndGetId(): void
    {
        $this->assertNull($this->agentUser->getId());
    }
    
    public function testSetAndGetAgent(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        
        $result = $this->agentUser->setAgent($agent);
        $this->assertSame($this->agentUser, $result);
        $this->assertSame($agent, $this->agentUser->getAgent());
    }
    
    public function testSetAndGetAgentWithNull(): void
    {
        $result = $this->agentUser->setAgent(null);
        $this->assertSame($this->agentUser, $result);
        $this->assertNull($this->agentUser->getAgent());
    }
    
    public function testSetAndGetUserId(): void
    {
        $userId = 'test_user_123';
        
        $result = $this->agentUser->setUserId($userId);
        $this->assertSame($this->agentUser, $result);
        $this->assertSame($userId, $this->agentUser->getUserId());
    }
    
    public function testSetAndGetUserIdWithEmptyString(): void
    {
        $result = $this->agentUser->setUserId('');
        $this->assertSame($this->agentUser, $result);
        $this->assertSame('', $this->agentUser->getUserId());
    }
    
    public function testSetAndGetOpenId(): void
    {
        $openId = 'open_id_123456';
        
        $result = $this->agentUser->setOpenId($openId);
        $this->assertSame($this->agentUser, $result);
        $this->assertSame($openId, $this->agentUser->getOpenId());
    }
    
    public function testSetAndGetOpenIdWithEmptyString(): void
    {
        $result = $this->agentUser->setOpenId('');
        $this->assertSame($this->agentUser, $result);
        $this->assertSame('', $this->agentUser->getOpenId());
    }
    
    public function testSetAndGetCreatedFromIp(): void
    {
        $ip = '192.168.1.1';
        
        $result = $this->agentUser->setCreatedFromIp($ip);
        $this->assertSame($this->agentUser, $result);
        $this->assertSame($ip, $this->agentUser->getCreatedFromIp());
    }
    
    public function testSetAndGetCreatedFromIpWithNull(): void
    {
        $result = $this->agentUser->setCreatedFromIp(null);
        $this->assertSame($this->agentUser, $result);
        $this->assertNull($this->agentUser->getCreatedFromIp());
    }
    
    public function testSetAndGetUpdatedFromIp(): void
    {
        $ip = '10.0.0.1';
        
        $result = $this->agentUser->setUpdatedFromIp($ip);
        $this->assertSame($this->agentUser, $result);
        $this->assertSame($ip, $this->agentUser->getUpdatedFromIp());
    }
    
    public function testSetAndGetUpdatedFromIpWithNull(): void
    {
        $result = $this->agentUser->setUpdatedFromIp(null);
        $this->assertSame($this->agentUser, $result);
        $this->assertNull($this->agentUser->getUpdatedFromIp());
    }
    
    public function testSetAndGetCreateTime(): void
    {
        $dateTime = new \DateTime('2024-01-01 12:00:00');
        
        $this->agentUser->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->agentUser->getCreateTime());
    }
    
    public function testSetAndGetCreateTimeWithNull(): void
    {
        $this->agentUser->setCreateTime(null);
        $this->assertNull($this->agentUser->getCreateTime());
    }
    
    public function testSetAndGetUpdateTime(): void
    {
        $dateTime = new \DateTime('2024-01-02 15:30:00');
        
        $this->agentUser->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->agentUser->getUpdateTime());
    }
    
    public function testSetAndGetUpdateTimeWithNull(): void
    {
        $this->agentUser->setUpdateTime(null);
        $this->assertNull($this->agentUser->getUpdateTime());
    }
    
    public function testChainedSetters(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        $userId = 'chain_user';
        $openId = 'chain_open_id';
        $ip = '127.0.0.1';
        
        $result = $this->agentUser
            ->setAgent($agent)
            ->setUserId($userId)
            ->setOpenId($openId)
            ->setCreatedFromIp($ip)
            ->setUpdatedFromIp($ip);
        
        $this->assertSame($this->agentUser, $result);
        $this->assertSame($agent, $this->agentUser->getAgent());
        $this->assertSame($userId, $this->agentUser->getUserId());
        $this->assertSame($openId, $this->agentUser->getOpenId());
        $this->assertSame($ip, $this->agentUser->getCreatedFromIp());
        $this->assertSame($ip, $this->agentUser->getUpdatedFromIp());
    }
} 