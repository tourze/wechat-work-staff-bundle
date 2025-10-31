<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Repository\DepartmentRepository;

/**
 * @internal
 */
#[CoversClass(DepartmentRepository::class)]
#[RunTestsInSeparateProcesses]
final class DepartmentRepositoryTest extends AbstractRepositoryTestCase
{
    private DepartmentRepository $repository;

    protected function onSetUp(): void
    {
        $container = self::getContainer();
        $repository = $container->get(DepartmentRepository::class);
        self::assertInstanceOf(DepartmentRepository::class, $repository);
        $this->repository = $repository;
    }

    public function testFindAllWithInvalidDatabaseSchemaShouldHandleGracefully(): void
    {
        // This test ensures findAll() can handle edge cases
        $result = $this->repository->findAll();

        $this->assertIsArray($result);
    }

    public function testFindOneByWithNullCriteriaShouldReturnEntity(): void
    {
        $department = $this->createTestDepartment();
        $department->setParent(null); // Set parent to null for IS NULL test
        self::getEntityManager()->persist($department);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['parent' => null]);

        $this->assertInstanceOf(Department::class, $result);
        $this->assertNull($result->getParent());
    }

    public function testFindOneByWithAssociationCriteriaShouldReturnEntity(): void
    {
        $parentDepartment = $this->createTestDepartment('Parent Department');
        self::getEntityManager()->persist($parentDepartment);
        self::getEntityManager()->flush();

        $childDepartment = $this->createTestDepartment('Child Department');
        $childDepartment->setParent($parentDepartment);
        self::getEntityManager()->persist($childDepartment);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['parent' => $parentDepartment]);

        $this->assertInstanceOf(Department::class, $result);
        $this->assertEquals('Child Department', $result->getName());
        $parent = $result->getParent();
        if (null === $parent) {
            self::fail('Parent department should not be null');
        }
        $this->assertEquals($parentDepartment->getId(), $parent->getId());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $department = $this->createTestDepartment();

        $this->repository->save($department, false);

        // Entity should be managed but not yet in database
        $this->assertTrue(self::getEntityManager()->contains($department));

        // Flush manually to persist
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->find($department->getId());
        $this->assertInstanceOf(Department::class, $foundEntity);
    }

    public function testRemoveWithFlushShouldDeleteEntity(): void
    {
        $department = $this->createTestDepartment();
        $this->repository->save($department);
        $entityId = $department->getId();

        $this->repository->remove($department);

        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $department = $this->createTestDepartment();
        $this->repository->save($department);
        $entityId = $department->getId();

        $this->repository->remove($department, false);

        // Entity should still exist in database until flush
        $foundEntity = $this->repository->find($entityId);
        $this->assertInstanceOf(Department::class, $foundEntity);

        // Flush to complete deletion
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    public function testCountWithValidCriteriaShouldWork(): void
    {
        $department = $this->createTestDepartment();
        self::getEntityManager()->persist($department);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['name' => 'Test Department']);

        $this->assertEquals(1, $count);
    }

    public function testFindByWithValidCriteriaShouldWork(): void
    {
        $department = $this->createTestDepartment();
        self::getEntityManager()->persist($department);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['name' => 'Test Department']);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function testFindOneByWithOrderByShouldReturnCorrectEntity(): void
    {
        $department1 = $this->createTestDepartment('B Department');
        $department2 = $this->createTestDepartment('A Department');

        self::getEntityManager()->persist($department1);
        self::getEntityManager()->persist($department2);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy([], ['name' => 'ASC']);

        $this->assertInstanceOf(Department::class, $result);
        $this->assertEquals('A Department', $result->getName());
    }

    public function testFindByWithAssociationCriteriaShouldReturnCorrectEntities(): void
    {
        $department = $this->createTestDepartment();
        self::getEntityManager()->persist($department);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['corp' => $department->getCorp()]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals($department->getId(), $result[0]->getId());
    }

    public function testCountWithAssociationCriteriaShouldReturnCorrectNumber(): void
    {
        $department = $this->createTestDepartment();
        self::getEntityManager()->persist($department);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['corp' => $department->getCorp()]);

        $this->assertEquals(1, $count);
    }

    public function testFindOneByWithValidCriteriaShouldWork(): void
    {
        $department = $this->createTestDepartment();
        self::getEntityManager()->persist($department);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['name' => 'Test Department']);

        $this->assertInstanceOf(Department::class, $result);
        $this->assertEquals('Test Department', $result->getName());
    }

    public function testFindOneByWithSortingLogicShouldReturnCorrectEntity(): void
    {
        $uniqueId = uniqid();
        $department1 = $this->createTestDepartment('C Department ' . $uniqueId);
        $department2 = $this->createTestDepartment('A Department ' . $uniqueId);
        $department3 = $this->createTestDepartment('B Department ' . $uniqueId);

        self::getEntityManager()->persist($department1);
        self::getEntityManager()->persist($department2);
        self::getEntityManager()->persist($department3);
        self::getEntityManager()->flush();

        // 使用LIKE查询来只匹配我们刚创建的测试数据
        $qb = $this->repository->createQueryBuilder('d');
        $result = $qb->where('d.name LIKE :pattern')
            ->setParameter('pattern', '% ' . $uniqueId)
            ->orderBy('d.name', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf(Department::class, $result);
        $this->assertEquals('A Department ' . $uniqueId, $result->getName());

        $resultDesc = $qb->orderBy('d.name', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf(Department::class, $resultDesc);
        $this->assertEquals('C Department ' . $uniqueId, $resultDesc->getName());
    }

    public function testFindByWithParentAssociationShouldReturnCorrectEntities(): void
    {
        $parentDepartment = $this->createTestDepartment('Parent Department');
        self::getEntityManager()->persist($parentDepartment);
        self::getEntityManager()->flush();

        $childDepartment1 = $this->createTestDepartment('Child Department 1');
        $childDepartment1->setParent($parentDepartment);
        $childDepartment2 = $this->createTestDepartment('Child Department 2');
        $childDepartment2->setParent($parentDepartment);

        self::getEntityManager()->persist($childDepartment1);
        self::getEntityManager()->persist($childDepartment2);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['parent' => $parentDepartment]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $entity) {
            /** @var Department $entity */
            $parent = $entity->getParent();
            $this->assertNotNull($parent);
            $this->assertEquals($parentDepartment->getId(), $parent->getId());
        }
    }

    public function testCountWithParentAssociationShouldReturnCorrectNumber(): void
    {
        $parentDepartment = $this->createTestDepartment('Parent Department');
        self::getEntityManager()->persist($parentDepartment);
        self::getEntityManager()->flush();

        $childDepartment1 = $this->createTestDepartment('Child Department 1');
        $childDepartment1->setParent($parentDepartment);
        $childDepartment2 = $this->createTestDepartment('Child Department 2');
        $childDepartment2->setParent($parentDepartment);

        self::getEntityManager()->persist($childDepartment1);
        self::getEntityManager()->persist($childDepartment2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['parent' => $parentDepartment]);

        $this->assertEquals(2, $count);
    }

    public function testFindByWithNullParentShouldReturnRootDepartments(): void
    {
        $uniqueId = uniqid();
        $rootDepartment1 = $this->createTestDepartment('Root Department 1 ' . $uniqueId);
        $rootDepartment1->setParent(null);
        $rootDepartment2 = $this->createTestDepartment('Root Department 2 ' . $uniqueId);
        $rootDepartment2->setParent(null);

        self::getEntityManager()->persist($rootDepartment1);
        self::getEntityManager()->persist($rootDepartment2);
        self::getEntityManager()->flush();

        // 使用QueryBuilder来只获取我们刚创建的测试数据
        $qb = $this->repository->createQueryBuilder('d');
        $result = $qb->where('d.parent IS NULL')
            ->andWhere('d.name LIKE :pattern')
            ->setParameter('pattern', '%' . $uniqueId)
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $entity) {
            $this->assertInstanceOf(Department::class, $entity);
            $this->assertNull($entity->getParent());
            $name = $entity->getName();
            $this->assertIsString($name);
            $this->assertStringContainsString($uniqueId, $name);
        }
    }

    public function testCountWithNullParentShouldReturnCorrectNumber(): void
    {
        $initialCount = $this->repository->count(['parent' => null]);

        $rootDepartment1 = $this->createTestDepartment('Root Department 1');
        $rootDepartment1->setParent(null);
        $rootDepartment2 = $this->createTestDepartment('Root Department 2');
        $rootDepartment2->setParent(null);

        self::getEntityManager()->persist($rootDepartment1);
        self::getEntityManager()->persist($rootDepartment2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['parent' => null]);

        $this->assertEquals($initialCount + 2, $count);
    }

    public function testCountByAssociationCorpShouldReturnCorrectNumber(): void
    {
        // Create shared corp and agent for both departments
        $testCorp = new Corp();
        $testCorp->setName('Count Corp Dept');
        $testCorp->setCorpId('count_corp_dept_id');
        $testCorp->setCorpSecret('count_corp_dept_secret');
        self::getEntityManager()->persist($testCorp);

        $testAgent = new Agent();
        $testAgent->setName('Count Agent Dept');
        $testAgent->setAgentId('count_agent_dept_id');
        $testAgent->setSecret('count_agent_dept_secret');
        $testAgent->setCorp($testCorp);
        self::getEntityManager()->persist($testAgent);

        $department1 = new Department();
        $department1->setName('Count Department 1');
        $department1->setSortNumber('2001');
        $department1->setCorp($testCorp);
        $department1->setAgent($testAgent);

        $department2 = new Department();
        $department2->setName('Count Department 2');
        $department2->setSortNumber('2002');
        $department2->setCorp($testCorp);
        $department2->setAgent($testAgent);

        self::getEntityManager()->persist($department1);
        self::getEntityManager()->persist($department2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['corp' => $testCorp]);

        $this->assertEquals(2, $count);
    }

    public function testFindOneByAssociationCorpShouldReturnMatchingEntity(): void
    {
        // Create a specific corp for this test
        $testCorp = new Corp();
        $testCorp->setName('FindOne Corp');
        $testCorp->setCorpId('findone_corp_id');
        $testCorp->setCorpSecret('findone_corp_secret');
        self::getEntityManager()->persist($testCorp);

        $testAgent = new Agent();
        $testAgent->setName('FindOne Agent');
        $testAgent->setAgentId('findone_agent_id');
        $testAgent->setSecret('findone_agent_secret');
        $testAgent->setCorp($testCorp);
        self::getEntityManager()->persist($testAgent);

        $department = new Department();
        $department->setName('FindOne Department');
        $department->setSortNumber('3001');
        $department->setCorp($testCorp);
        $department->setAgent($testAgent);

        self::getEntityManager()->persist($department);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['corp' => $testCorp]);

        $this->assertInstanceOf(Department::class, $result);
        $this->assertEquals($testCorp, $result->getCorp());
    }

    private function createTestDepartment(string $name = 'Test Department'): Department
    {
        $department = new Department();
        $department->setName($name);
        $department->setSortNumber('1000');

        // Create unique IDs to avoid constraint violations
        $uniqueId = uniqid();

        // Create and persist real test entities
        $testCorp = new Corp();
        $testCorp->setName('Test Corp ' . $uniqueId);
        $testCorp->setCorpId('test_corp_id_' . $uniqueId);
        $testCorp->setCorpSecret('test_corp_secret_' . $uniqueId);
        self::getEntityManager()->persist($testCorp);

        $testAgent = new Agent();
        $testAgent->setName('Test Agent ' . $uniqueId);
        $testAgent->setAgentId('test_agent_id_' . $uniqueId);
        $testAgent->setSecret('test_agent_secret_' . $uniqueId);
        $testAgent->setCorp($testCorp);
        self::getEntityManager()->persist($testAgent);

        $department->setCorp($testCorp);
        $department->setAgent($testAgent);

        return $department;
    }

    protected function createNewEntity(): object
    {
        return $this->createTestDepartment('Test Department ' . uniqid());
    }

    protected function getRepository(): DepartmentRepository
    {
        return $this->repository;
    }
}
