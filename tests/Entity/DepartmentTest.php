<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Entity\User;

/**
 * @internal
 */
#[CoversClass(Department::class)]
final class DepartmentTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Department();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'remoteId' => ['remoteId', 1001],
            'name' => ['name', '技术部'],
            'enName' => ['enName', 'Technology Department'],
            'sortNumber' => ['sortNumber', '100'],
            'parent' => ['parent', null],
            'corp' => ['corp', null],
            'agent' => ['agent', null],
            'createdBy' => ['createdBy', 'admin'],
            'updatedBy' => ['updatedBy', 'manager'],
            'createdFromIp' => ['createdFromIp', '192.168.1.200'],
            'updatedFromIp' => ['updatedFromIp', '10.0.0.100'],
            'createTime' => ['createTime', new \DateTimeImmutable('2024-01-01 09:00:00')],
            'updateTime' => ['updateTime', new \DateTimeImmutable('2024-01-02 18:00:00')],
        ];
    }

    private Department $department;

    protected function setUp(): void
    {
        $this->department = new Department();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->department->getChildren());
        $this->assertInstanceOf(ArrayCollection::class, $this->department->getUsers());
        $this->assertCount(0, $this->department->getChildren());
        $this->assertCount(0, $this->department->getUsers());
    }

    public function testToStringWhenIdIsNull(): void
    {
        // ID初始值是0，当ID为0时返回空字符串
        $this->assertSame('', (string) $this->department);
    }

    public function testToStringWithIdAndName(): void
    {
        $this->department->setName('测试部门');

        // 使用反射设置ID，因为ID是由数据库生成的
        $reflection = new \ReflectionClass($this->department);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->department, 123);

        $this->assertSame('测试部门(123)', (string) $this->department);
    }

    public function testAddChild(): void
    {
        $child = new Department();

        $this->department->addChild($child);
        $this->assertCount(1, $this->department->getChildren());
        $this->assertTrue($this->department->getChildren()->contains($child));
        $this->assertSame($this->department, $child->getParent());
    }

    public function testAddChildTwiceDoesNotDuplicate(): void
    {
        $child = new Department();

        $this->department->addChild($child);
        $this->department->addChild($child);

        $this->assertCount(1, $this->department->getChildren());
    }

    public function testRemoveChild(): void
    {
        $child = new Department();
        $this->department->addChild($child);

        $this->department->removeChild($child);
        $this->assertCount(0, $this->department->getChildren());
        $this->assertNull($child->getParent());
    }

    public function testRemoveChildThatDoesNotExist(): void
    {
        $child = new Department();

        $this->department->removeChild($child);
        $this->assertCount(0, $this->department->getChildren());
    }

    public function testAddUser(): void
    {
        $user = new User();

        $this->department->addUser($user);
        $this->assertCount(1, $this->department->getUsers());
        $this->assertTrue($this->department->getUsers()->contains($user));
    }

    public function testAddUserTwiceDoesNotDuplicate(): void
    {
        $user = new User();

        $this->department->addUser($user);
        $this->department->addUser($user);

        $this->assertCount(1, $this->department->getUsers());
    }

    public function testRemoveUser(): void
    {
        $user = new User();
        $this->department->addUser($user);

        $this->department->removeUser($user);
        $this->assertCount(0, $this->department->getUsers());
    }

    public function testHierarchicalStructure(): void
    {
        $parent = new Department();
        $parent->setName('总公司');

        $child1 = new Department();
        $child1->setName('技术部');

        $child2 = new Department();
        $child2->setName('销售部');

        $parent->addChild($child1);
        $parent->addChild($child2);

        $this->assertCount(2, $parent->getChildren());
        $this->assertSame($parent, $child1->getParent());
        $this->assertSame($parent, $child2->getParent());
    }
}
