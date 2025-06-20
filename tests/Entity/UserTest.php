<?php

namespace WechatWorkStaffBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
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

    public function testToStringWhenIdIsNull(): void
    {
        $this->assertSame('', (string)$this->user);
    }

    public function testToStringWithIdUserIdAndName(): void
    {
        $this->user->setUserId('test_user');
        $this->user->setName('测试用户');

        // 使用反射设置ID
        $reflection = new \ReflectionClass($this->user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->user, 123);

        $this->assertSame('测试用户(test_user)', (string)$this->user);
    }

    public function testSetAndGetId(): void
    {
        $this->assertSame(0, $this->user->getId());
    }

    public function testSetAndGetCorp(): void
    {
        $corp = $this->createMock(CorpInterface::class);

        $result = $this->user->setCorp($corp);
        $this->assertSame($this->user, $result);
        $this->assertSame($corp, $this->user->getCorp());
    }

    public function testSetAndGetCorpWithNull(): void
    {
        $result = $this->user->setCorp(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getCorp());
    }

    public function testSetAndGetAgent(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $result = $this->user->setAgent($agent);
        $this->assertSame($this->user, $result);
        $this->assertSame($agent, $this->user->getAgent());
    }

    public function testSetAndGetAgentWithNull(): void
    {
        $result = $this->user->setAgent(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getAgent());
    }

    public function testSetAndGetUserId(): void
    {
        $userId = 'employee123';

        $result = $this->user->setUserId($userId);
        $this->assertSame($this->user, $result);
        $this->assertSame($userId, $this->user->getUserId());
    }

    public function testSetAndGetName(): void
    {
        $name = '张三';

        $result = $this->user->setName($name);
        $this->assertSame($this->user, $result);
        $this->assertSame($name, $this->user->getName());
    }

    public function testSetAndGetAlias(): void
    {
        $alias = '小张';

        $result = $this->user->setAlias($alias);
        $this->assertSame($this->user, $result);
        $this->assertSame($alias, $this->user->getAlias());
    }

    public function testSetAndGetAliasWithNull(): void
    {
        $result = $this->user->setAlias(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getAlias());
    }

    public function testSetAndGetPosition(): void
    {
        $position = '软件工程师';

        $result = $this->user->setPosition($position);
        $this->assertSame($this->user, $result);
        $this->assertSame($position, $this->user->getPosition());
    }

    public function testSetAndGetPositionWithNull(): void
    {
        $result = $this->user->setPosition(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getPosition());
    }

    public function testSetAndGetMobile(): void
    {
        $mobile = '13800138000';

        $result = $this->user->setMobile($mobile);
        $this->assertSame($this->user, $result);
        $this->assertSame($mobile, $this->user->getMobile());
    }

    public function testSetAndGetMobileWithNull(): void
    {
        $result = $this->user->setMobile(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getMobile());
    }

    public function testSetAndGetEmail(): void
    {
        $email = 'test@example.com';

        $result = $this->user->setEmail($email);
        $this->assertSame($this->user, $result);
        $this->assertSame($email, $this->user->getEmail());
    }

    public function testSetAndGetEmailWithNull(): void
    {
        $result = $this->user->setEmail(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getEmail());
    }

    public function testSetAndGetOpenUserId(): void
    {
        $openUserId = 'open_user_id_123';

        $result = $this->user->setOpenUserId($openUserId);
        $this->assertSame($this->user, $result);
        $this->assertSame($openUserId, $this->user->getOpenUserId());
    }

    public function testSetAndGetOpenUserIdWithNull(): void
    {
        $result = $this->user->setOpenUserId(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getOpenUserId());
    }

    public function testSetAndGetAvatarUrl(): void
    {
        $avatarUrl = 'https://example.com/avatar.jpg';

        $result = $this->user->setAvatarUrl($avatarUrl);
        $this->assertSame($this->user, $result);
        $this->assertSame($avatarUrl, $this->user->getAvatarUrl());
    }

    public function testSetAndGetAvatarUrlWithNull(): void
    {
        $result = $this->user->setAvatarUrl(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getAvatarUrl());
    }

    public function testAddDepartment(): void
    {
        $department = new Department();

        $result = $this->user->addDepartment($department);
        $this->assertSame($this->user, $result);
        $this->assertCount(1, $this->user->getDepartments());
        $this->assertTrue($this->user->getDepartments()->contains($department));
    }

    public function testAddDepartmentTwiceDoesNotDuplicate(): void
    {
        $department = new Department();

        $this->user->addDepartment($department);
        $this->user->addDepartment($department);

        $this->assertCount(1, $this->user->getDepartments());
    }

    public function testRemoveDepartment(): void
    {
        $department = new Department();
        $this->user->addDepartment($department);

        $result = $this->user->removeDepartment($department);
        $this->assertSame($this->user, $result);
        $this->assertCount(0, $this->user->getDepartments());
    }

    public function testAddTag(): void
    {
        $tag = new UserTag();

        $result = $this->user->addTag($tag);
        $this->assertSame($this->user, $result);
        $this->assertCount(1, $this->user->getTags());
        $this->assertTrue($this->user->getTags()->contains($tag));
    }

    public function testAddTagTwiceDoesNotDuplicate(): void
    {
        $tag = new UserTag();

        $this->user->addTag($tag);
        $this->user->addTag($tag);

        $this->assertCount(1, $this->user->getTags());
    }

    public function testRemoveTag(): void
    {
        $tag = new UserTag();
        $this->user->addTag($tag);

        $result = $this->user->removeTag($tag);
        $this->assertSame($this->user, $result);
        $this->assertCount(0, $this->user->getTags());
    }

    public function testSetAndGetCreatedBy(): void
    {
        $createdBy = 'admin';

        $result = $this->user->setCreatedBy($createdBy);
        $this->assertSame($this->user, $result);
        $this->assertSame($createdBy, $this->user->getCreatedBy());
    }

    public function testSetAndGetCreatedByWithNull(): void
    {
        $result = $this->user->setCreatedBy(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getCreatedBy());
    }

    public function testSetAndGetUpdatedBy(): void
    {
        $updatedBy = 'moderator';

        $result = $this->user->setUpdatedBy($updatedBy);
        $this->assertSame($this->user, $result);
        $this->assertSame($updatedBy, $this->user->getUpdatedBy());
    }

    public function testSetAndGetUpdatedByWithNull(): void
    {
        $result = $this->user->setUpdatedBy(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getUpdatedBy());
    }

    public function testSetAndGetCreatedFromIp(): void
    {
        $ip = '192.168.1.200';

        $result = $this->user->setCreatedFromIp($ip);
        $this->assertSame($this->user, $result);
        $this->assertSame($ip, $this->user->getCreatedFromIp());
    }

    public function testSetAndGetCreatedFromIpWithNull(): void
    {
        $result = $this->user->setCreatedFromIp(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getCreatedFromIp());
    }

    public function testSetAndGetUpdatedFromIp(): void
    {
        $ip = '10.0.0.75';

        $result = $this->user->setUpdatedFromIp($ip);
        $this->assertSame($this->user, $result);
        $this->assertSame($ip, $this->user->getUpdatedFromIp());
    }

    public function testSetAndGetUpdatedFromIpWithNull(): void
    {
        $result = $this->user->setUpdatedFromIp(null);
        $this->assertSame($this->user, $result);
        $this->assertNull($this->user->getUpdatedFromIp());
    }

    public function testSetAndGetCreateTime(): void
    {
        $dateTime = new \DateTimeImmutable('2024-01-01 09:00:00');

        $this->user->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->user->getCreateTime());
    }

    public function testSetAndGetCreateTimeWithNull(): void
    {
        $this->user->setCreateTime(null);
        $this->assertNull($this->user->getCreateTime());
    }

    public function testSetAndGetUpdateTime(): void
    {
        $dateTime = new \DateTimeImmutable('2024-01-02 16:45:00');

        $this->user->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->user->getUpdateTime());
    }

    public function testSetAndGetUpdateTimeWithNull(): void
    {
        $this->user->setUpdateTime(null);
        $this->assertNull($this->user->getUpdateTime());
    }

    public function testUserDepartmentAndTagAssociations(): void
    {
        $department1 = new Department();
        $department1->setName('技术部');

        $department2 = new Department();
        $department2->setName('产品部');

        $tag1 = new UserTag();
        $tag1->setName('高级');

        $tag2 = new UserTag();
        $tag2->setName('骨干');

        $this->user->addDepartment($department1);
        $this->user->addDepartment($department2);
        $this->user->addTag($tag1);
        $this->user->addTag($tag2);

        $this->assertCount(2, $this->user->getDepartments());
        $this->assertCount(2, $this->user->getTags());

        $this->assertTrue($this->user->getDepartments()->contains($department1));
        $this->assertTrue($this->user->getDepartments()->contains($department2));
        $this->assertTrue($this->user->getTags()->contains($tag1));
        $this->assertTrue($this->user->getTags()->contains($tag2));
    }

    public function testCompleteUserProfile(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        $agent = $this->createMock(AgentInterface::class);

        $this->user
            ->setCorp($corp)
            ->setAgent($agent)
            ->setUserId('complete_user')
            ->setName('完整用户')
            ->setAlias('全量')
            ->setPosition('架构师')
            ->setMobile('13900139000')
            ->setEmail('complete@example.com')
            ->setOpenUserId('open_complete_123')
            ->setAvatarUrl('https://example.com/complete.jpg');

        $this->assertSame($corp, $this->user->getCorp());
        $this->assertSame($agent, $this->user->getAgent());
        $this->assertSame('complete_user', $this->user->getUserId());
        $this->assertSame('完整用户', $this->user->getName());
        $this->assertSame('全量', $this->user->getAlias());
        $this->assertSame('架构师', $this->user->getPosition());
        $this->assertSame('13900139000', $this->user->getMobile());
        $this->assertSame('complete@example.com', $this->user->getEmail());
        $this->assertSame('open_complete_123', $this->user->getOpenUserId());
        $this->assertSame('https://example.com/complete.jpg', $this->user->getAvatarUrl());
    }
}
