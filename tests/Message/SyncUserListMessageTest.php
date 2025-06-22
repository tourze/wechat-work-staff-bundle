<?php

namespace WechatWorkStaffBundle\Tests\Message;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Message\SyncUserListMessage;

class SyncUserListMessageTest extends TestCase
{
    private SyncUserListMessage $message;
    
    protected function setUp(): void
    {
        $this->message = new SyncUserListMessage();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(SyncUserListMessage::class, $this->message);
    }
    
    public function testAgentIdGetterAndSetter(): void
    {
        $agentId = 123;
        
        $this->message->setAgentId($agentId);
        $this->assertSame($agentId, $this->message->getAgentId());
    }
    
    public function testAgentIdWithDifferentValues(): void
    {
        $testCases = [1, 100, 999, 1000001, 9999999];
        
        foreach ($testCases as $agentId) {
            $this->message->setAgentId($agentId);
            $this->assertSame($agentId, $this->message->getAgentId());
        }
    }
    
    public function testAgentIdWithZeroValue(): void
    {
        $agentId = 0;
        
        $this->message->setAgentId($agentId);
        $this->assertSame($agentId, $this->message->getAgentId());
    }
    
    public function testSetAgentIdReturnType(): void
    {
        $agentId = 456;
        
        $result = $this->message->setAgentId($agentId);
        $this->assertNull($result); // setter方法没有返回值
    }
}
