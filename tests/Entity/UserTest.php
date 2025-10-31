<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\DoctrineResolveTargetEntityBundle\Testing\TestEntityGenerator;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Entity\UserTag;

/**
 * @internal
 */
#[CoversClass(User::class)]
final class UserTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new User();
    }

    /**
     * @return array<string, array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            'corp' => ['corp', null],
            'agent' => ['agent', null],
            'userId' => ['userId', 'zhangsan'],
            'name' => ['name', '张三'],
            'alias' => ['alias', '小张'],
            'position' => ['position', '工程师'],
            'mobile' => ['mobile', '13800138000'],
            'email' => ['email', 'zhangsan@example.com'],
            'openUserId' => ['openUserId', 'openid123'],
            'avatarUrl' => ['avatarUrl', 'https://example.com/avatar.jpg'],
            'createdBy' => ['createdBy', 'admin'],
            'updatedBy' => ['updatedBy', 'hr'],
            'createdFromIp' => ['createdFromIp', '192.168.1.150'],
            'updatedFromIp' => ['updatedFromIp', '10.0.0.75'],
            'createTime' => ['createTime', new \DateTimeImmutable('2024-01-01 08:00:00')],
            'updateTime' => ['updateTime', new \DateTimeImmutable('2024-01-02 16:00:00')],
        ];
    }

    private User $user;

    private TestEntityGenerator $testEntityGenerator;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->testEntityGenerator = new TestEntityGenerator(sys_get_temp_dir() . '/test_entities');
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
        // ID初始值是0，当ID为0时返回空字符串
        $this->assertSame('', (string) $this->user);
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

        $this->assertSame('测试用户(test_user)', (string) $this->user);
    }

    public function testAddDepartment(): void
    {
        $department = new Department();

        $this->user->addDepartment($department);
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

        $this->user->removeDepartment($department);
        $this->assertCount(0, $this->user->getDepartments());
    }

    public function testAddTag(): void
    {
        $tag = new UserTag();

        $this->user->addTag($tag);
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

        $this->user->removeTag($tag);
        $this->assertCount(0, $this->user->getTags());
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
        /** @var CorpInterface $corp */
        $corp = $this->testEntityGenerator
            ->generateTestImplementation(CorpInterface::class)
        ;
        /** @var AgentInterface $agent */
        $agent = $this->testEntityGenerator
            ->generateTestImplementation(AgentInterface::class)
        ;

        $this->user->setCorp($corp);
        $this->user->setAgent($agent);
        $this->user->setUserId('complete_user');
        $this->user->setName('完整用户');
        $this->user->setAlias('全量');
        $this->user->setPosition('架构师');
        $this->user->setMobile('13900139000');
        $this->user->setEmail('complete@example.com');
        $this->user->setOpenUserId('open_complete_123');
        $this->user->setAvatarUrl('https://example.com/complete.jpg');

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

    public function testUserEntityImplementsStringable(): void
    {
        // 测试__toString方法是否存在并可以调用

        $this->user->setUserId('test_user');
        $this->user->setName('测试用户');

        // 使用反射设置ID为非零值，这样__toString才会返回有效字符串
        $reflection = new \ReflectionClass($this->user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->user, 1);

        $stringRepresentation = (string) $this->user;
        $this->assertNotEmpty($stringRepresentation);
    }
}
