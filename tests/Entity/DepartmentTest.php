<?php

namespace WechatWorkStaffBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Entity\User;

class DepartmentTest extends TestCase
{
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
        $this->assertSame('', (string)$this->department);
    }

    public function testToStringWithIdAndName(): void
    {
        $this->department->setName('测试部门');

        // 使用反射设置ID，因为ID是由数据库生成的
        $reflection = new \ReflectionClass($this->department);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->department, 123);

        $this->assertSame('测试部门(123)', (string)$this->department);
    }

    public function testSetAndGetId(): void
    {
        $this->assertSame(0, $this->department->getId());
    }

    public function testSetAndGetRemoteId(): void
    {
        $remoteId = 456;

        $result = $this->department->setRemoteId($remoteId);
        $this->assertSame($this->department, $result);
        $this->assertSame($remoteId, $this->department->getRemoteId());
    }

    public function testSetAndGetRemoteIdWithNull(): void
    {
        $result = $this->department->setRemoteId(null);
        $this->assertSame($this->department, $result);
        $this->assertNull($this->department->getRemoteId());
    }

    public function testSetAndGetName(): void
    {
        $name = '人力资源部';

        $result = $this->department->setName($name);
        $this->assertSame($this->department, $result);
        $this->assertSame($name, $this->department->getName());
    }

    public function testGetNameReturnsStringWhenNull(): void
    {
        $this->assertSame('', $this->department->getName());
    }

    public function testSetAndGetEnName(): void
    {
        $enName = 'Human Resources';

        $result = $this->department->setEnName($enName);
        $this->assertSame($this->department, $result);
        $this->assertSame($enName, $this->department->getEnName());
    }

    public function testSetAndGetEnNameWithNull(): void
    {
        $result = $this->department->setEnName(null);
        $this->assertSame($this->department, $result);
        $this->assertNull($this->department->getEnName());
    }

    public function testSetAndGetSortNumber(): void
    {
        $sortNumber = '100';

        $result = $this->department->setSortNumber($sortNumber);
        $this->assertSame($this->department, $result);
        $this->assertSame($sortNumber, $this->department->getSortNumber());
    }

    public function testSetAndGetSortNumberWithNull(): void
    {
        $result = $this->department->setSortNumber(null);
        $this->assertSame($this->department, $result);
        $this->assertNull($this->department->getSortNumber());
    }

    public function testSetAndGetParent(): void
    {
        $parent = new Department();

        $result = $this->department->setParent($parent);
        $this->assertSame($this->department, $result);
        $this->assertSame($parent, $this->department->getParent());
    }

    public function testSetAndGetParentWithNull(): void
    {
        $result = $this->department->setParent(null);
        $this->assertSame($this->department, $result);
        $this->assertNull($this->department->getParent());
    }

    public function testAddChild(): void
    {
        $child = new Department();

        $result = $this->department->addChild($child);
        $this->assertSame($this->department, $result);
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

        $result = $this->department->removeChild($child);
        $this->assertSame($this->department, $result);
        $this->assertCount(0, $this->department->getChildren());
        $this->assertNull($child->getParent());
    }

    public function testRemoveChildThatDoesNotExist(): void
    {
        $child = new Department();

        $result = $this->department->removeChild($child);
        $this->assertSame($this->department, $result);
        $this->assertCount(0, $this->department->getChildren());
    }

    public function testAddUser(): void
    {
        $user = new User();

        $result = $this->department->addUser($user);
        $this->assertSame($this->department, $result);
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

        $result = $this->department->removeUser($user);
        $this->assertSame($this->department, $result);
        $this->assertCount(0, $this->department->getUsers());
    }

    public function testSetAndGetCorp(): void
    {
        $corp = $this->createMock(CorpInterface::class);

        $result = $this->department->setCorp($corp);
        $this->assertSame($this->department, $result);
        $this->assertSame($corp, $this->department->getCorp());
    }

    public function testSetAndGetCorpWithNull(): void
    {
        $result = $this->department->setCorp(null);
        $this->assertSame($this->department, $result);
        $this->assertNull($this->department->getCorp());
    }

    public function testSetAndGetAgent(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $result = $this->department->setAgent($agent);
        $this->assertSame($this->department, $result);
        $this->assertSame($agent, $this->department->getAgent());
    }

    public function testSetAndGetAgentWithNull(): void
    {
        $result = $this->department->setAgent(null);
        $this->assertSame($this->department, $result);
        $this->assertNull($this->department->getAgent());
    }

    public function testSetAndGetCreatedBy(): void
    {
        $createdBy = 'admin';

        $result = $this->department->setCreatedBy($createdBy);
        $this->assertSame($this->department, $result);
        $this->assertSame($createdBy, $this->department->getCreatedBy());
    }

    public function testSetAndGetCreatedByWithNull(): void
    {
        $result = $this->department->setCreatedBy(null);
        $this->assertSame($this->department, $result);
        $this->assertNull($this->department->getCreatedBy());
    }

    public function testSetAndGetUpdatedBy(): void
    {
        $updatedBy = 'moderator';

        $result = $this->department->setUpdatedBy($updatedBy);
        $this->assertSame($this->department, $result);
        $this->assertSame($updatedBy, $this->department->getUpdatedBy());
    }

    public function testSetAndGetUpdatedByWithNull(): void
    {
        $result = $this->department->setUpdatedBy(null);
        $this->assertSame($this->department, $result);
        $this->assertNull($this->department->getUpdatedBy());
    }

    public function testSetAndGetCreatedFromIp(): void
    {
        $ip = '192.168.1.100';

        $result = $this->department->setCreatedFromIp($ip);
        $this->assertSame($this->department, $result);
        $this->assertSame($ip, $this->department->getCreatedFromIp());
    }

    public function testSetAndGetCreatedFromIpWithNull(): void
    {
        $result = $this->department->setCreatedFromIp(null);
        $this->assertSame($this->department, $result);
        $this->assertNull($this->department->getCreatedFromIp());
    }

    public function testSetAndGetUpdatedFromIp(): void
    {
        $ip = '10.0.0.50';

        $result = $this->department->setUpdatedFromIp($ip);
        $this->assertSame($this->department, $result);
        $this->assertSame($ip, $this->department->getUpdatedFromIp());
    }

    public function testSetAndGetUpdatedFromIpWithNull(): void
    {
        $result = $this->department->setUpdatedFromIp(null);
        $this->assertSame($this->department, $result);
        $this->assertNull($this->department->getUpdatedFromIp());
    }

    public function testSetAndGetCreateTime(): void
    {
        $dateTime = new \DateTimeImmutable('2024-01-01 10:00:00');

        $this->department->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->department->getCreateTime());
    }

    public function testSetAndGetCreateTimeWithNull(): void
    {
        $this->department->setCreateTime(null);
        $this->assertNull($this->department->getCreateTime());
    }

    public function testSetAndGetUpdateTime(): void
    {
        $dateTime = new \DateTimeImmutable('2024-01-02 14:30:00');

        $this->department->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->department->getUpdateTime());
    }

    public function testSetAndGetUpdateTimeWithNull(): void
    {
        $this->department->setUpdateTime(null);
        $this->assertNull($this->department->getUpdateTime());
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
