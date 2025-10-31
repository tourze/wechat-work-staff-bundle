<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\DoctrineResolveTargetEntityBundle\Testing\TestEntityGenerator;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkStaffBundle\Entity\AgentUser;

/**
 * @internal
 */
#[CoversClass(AgentUser::class)]
final class AgentUserTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new AgentUser();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'agent' => ['agent', null],
            'userId' => ['userId', 'user12345'],
            'openId' => ['openId', 'openid12345'],
            'createdFromIp' => ['createdFromIp', '192.168.1.100'],
            'updatedFromIp' => ['updatedFromIp', '10.0.0.50'],
            'createTime' => ['createTime', new \DateTimeImmutable('2024-01-01 10:00:00')],
            'updateTime' => ['updateTime', new \DateTimeImmutable('2024-01-02 15:30:00')],
        ];
    }

    private AgentUser $agentUser;

    private TestEntityGenerator $testEntityGenerator;

    protected function setUp(): void
    {
        $this->agentUser = new AgentUser();
        $this->testEntityGenerator = new TestEntityGenerator(sys_get_temp_dir() . '/test_entities');
    }

    public function testChainedSetters(): void
    {
        /** @var AgentInterface $agent */
        $agent = $this->testEntityGenerator
            ->generateTestImplementation(AgentInterface::class)
        ;
        $userId = 'chain_user';
        $openId = 'chain_open_id';
        $ip = '127.0.0.1';

        // Setter methods now return void, so we need to call them separately
        $this->agentUser->setAgent($agent);
        $this->agentUser->setUserId($userId);
        $this->agentUser->setOpenId($openId);
        $this->agentUser->setCreatedFromIp($ip);
        $this->agentUser->setUpdatedFromIp($ip);

        // Verify all properties were set correctly
        $this->assertSame($agent, $this->agentUser->getAgent());
        $this->assertSame($userId, $this->agentUser->getUserId());
        $this->assertSame($openId, $this->agentUser->getOpenId());
        $this->assertSame($ip, $this->agentUser->getCreatedFromIp());
        $this->assertSame($ip, $this->agentUser->getUpdatedFromIp());
    }
}
