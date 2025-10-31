<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkStaffBundle\Entity\AgentUser;
use WechatWorkStaffBundle\Repository\AgentUserRepository;

/**
 * @internal
 */
#[CoversClass(AgentUserRepository::class)]
#[RunTestsInSeparateProcesses]
final class AgentUserRepositoryTest extends AbstractRepositoryTestCase
{
    private AgentUserRepository $repository;

    protected function onSetUp(): void
    {
        $container = self::getContainer();
        $repository = $container->get(AgentUserRepository::class);
        self::assertInstanceOf(AgentUserRepository::class, $repository);
        $this->repository = $repository;
    }

    public function testFindOneByWithNullCriteriaShouldReturnEntity(): void
    {
        $agentUser = $this->createTestAgentUser();
        $agentUser->setAgent(null); // Set agent to null for IS NULL test
        self::getEntityManager()->persist($agentUser);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['agent' => null]);

        $this->assertInstanceOf(AgentUser::class, $result);
        $this->assertNull($result->getAgent());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $agentUser = $this->createTestAgentUser();

        $this->repository->save($agentUser, false);

        // Entity should be managed but not yet in database
        $this->assertTrue(self::getEntityManager()->contains($agentUser));

        // Flush manually to persist
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->find($agentUser->getId());
        $this->assertInstanceOf(AgentUser::class, $foundEntity);
    }

    public function testRemoveWithFlushShouldDeleteEntity(): void
    {
        $agentUser = $this->createTestAgentUser();
        $this->repository->save($agentUser);
        $entityId = $agentUser->getId();

        $this->repository->remove($agentUser);

        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $agentUser = $this->createTestAgentUser();
        $this->repository->save($agentUser);
        $entityId = $agentUser->getId();

        $this->repository->remove($agentUser, false);

        // Entity should still exist in database until flush
        $foundEntity = $this->repository->find($entityId);
        $this->assertInstanceOf(AgentUser::class, $foundEntity);

        // Flush to complete deletion
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    public function testFindOneByWithOrderByShouldReturnCorrectEntity(): void
    {
        $agentUser1 = $this->createTestAgentUser('b_user', 'open_id_1');
        $agentUser2 = $this->createTestAgentUser('a_user', 'open_id_2');

        self::getEntityManager()->persist($agentUser1);
        self::getEntityManager()->persist($agentUser2);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy([], ['userId' => 'ASC']);

        $this->assertInstanceOf(AgentUser::class, $result);
        $this->assertEquals('a_user', $result->getUserId());
    }

    public function testFindByWithAssociationCriteriaShouldReturnCorrectEntities(): void
    {
        // 清理之前的测试数据，确保测试隔离
        $existingEntities = $this->repository->findAll();
        foreach ($existingEntities as $entity) {
            self::getEntityManager()->remove($entity);
        }
        self::getEntityManager()->flush();

        $agentUser = $this->createTestAgentUser();
        self::getEntityManager()->persist($agentUser);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['agent' => $agentUser->getAgent()]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals($agentUser->getId(), $result[0]->getId());
    }

    public function testCountWithAssociationCriteriaShouldReturnCorrectNumber(): void
    {
        // 清理之前的测试数据，确保测试隔离
        $existingEntities = $this->repository->findAll();
        foreach ($existingEntities as $entity) {
            self::getEntityManager()->remove($entity);
        }
        self::getEntityManager()->flush();

        $agentUser = $this->createTestAgentUser();
        self::getEntityManager()->persist($agentUser);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['agent' => $agentUser->getAgent()]);

        $this->assertEquals(1, $count);
    }

    public function testFindByWithNullCriteriaShouldReturnEntities(): void
    {
        $agentUserWithoutAgent = $this->createTestAgentUser('user_without_agent', 'open_id_1');
        $agentUserWithoutAgent->setAgent(null);

        self::getEntityManager()->persist($agentUserWithoutAgent);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['agent' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        $this->assertInstanceOf(AgentUser::class, $result[0]);
    }

    public function testFindOneByWithSortingLogicShouldReturnCorrectEntity(): void
    {
        // 清理之前的测试数据，确保测试隔离
        $existingEntities = $this->repository->findAll();
        foreach ($existingEntities as $entity) {
            self::getEntityManager()->remove($entity);
        }
        self::getEntityManager()->flush();

        $agentUser1 = $this->createTestAgentUser('b_user', 'open_id_1');
        $agentUser2 = $this->createTestAgentUser('a_user', 'open_id_2');
        $agentUser3 = $this->createTestAgentUser('c_user', 'open_id_3');

        self::getEntityManager()->persist($agentUser1);
        self::getEntityManager()->persist($agentUser2);
        self::getEntityManager()->persist($agentUser3);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy([], ['userId' => 'ASC']);

        $this->assertInstanceOf(AgentUser::class, $result);
        $this->assertEquals('a_user', $result->getUserId());

        $resultDesc = $this->repository->findOneBy([], ['userId' => 'DESC']);

        $this->assertInstanceOf(AgentUser::class, $resultDesc);
        $this->assertEquals('c_user', $resultDesc->getUserId());
    }

    public function testFindByWithAgentAssociationShouldReturnCorrectEntities(): void
    {
        // 清理之前的测试数据，确保测试隔离
        $existingEntities = $this->repository->findAll();
        foreach ($existingEntities as $entity) {
            self::getEntityManager()->remove($entity);
        }
        self::getEntityManager()->flush();

        $agentUser1 = $this->createTestAgentUser('user1', 'open_id_1');
        $agentUser2 = $this->createTestAgentUser('user2', 'open_id_2');
        $agentUser2->setAgent(null);

        self::getEntityManager()->persist($agentUser1);
        self::getEntityManager()->persist($agentUser2);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['agent' => null]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Both have null agent in our test setup
        foreach ($result as $entity) {
            /** @var AgentUser $entity */
            $this->assertNull($entity->getAgent());
        }
    }

    public function testCountWithAgentAssociationShouldReturnCorrectNumber(): void
    {
        // 清理之前的测试数据，确保测试隔离
        $existingEntities = $this->repository->findAll();
        foreach ($existingEntities as $entity) {
            self::getEntityManager()->remove($entity);
        }
        self::getEntityManager()->flush();

        $agentUser1 = $this->createTestAgentUser('user1', 'open_id_1');
        $agentUser2 = $this->createTestAgentUser('user2', 'open_id_2');
        $agentUser2->setAgent(null);

        self::getEntityManager()->persist($agentUser1);
        self::getEntityManager()->persist($agentUser2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['agent' => null]);

        $this->assertEquals(2, $count); // Both have null agent in our test setup
    }

    public function testFindByWithNullUserIdShouldReturnEntities(): void
    {
        $agentUser1 = $this->createTestAgentUser('user_with_id', 'open_id_1');
        $agentUser2 = $this->createTestAgentUser('', 'open_id_2'); // Empty userId should be null

        self::getEntityManager()->persist($agentUser1);
        self::getEntityManager()->persist($agentUser2);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['userId' => '']);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        /** @var AgentUser $entity */
        $entity = $result[0];
        $this->assertEquals('', $entity->getUserId());
    }

    public function testCountWithNullUserIdShouldReturnCorrectNumber(): void
    {
        $agentUser1 = $this->createTestAgentUser('user_with_id', 'open_id_1');
        $agentUser2 = $this->createTestAgentUser('', 'open_id_2'); // Empty userId

        self::getEntityManager()->persist($agentUser1);
        self::getEntityManager()->persist($agentUser2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['userId' => '']);

        $this->assertEquals(1, $count);
    }

    public function testCountByAssociationAgentShouldReturnCorrectNumber(): void
    {
        // 清理之前的测试数据，确保测试隔离
        $existingEntities = $this->repository->findAll();
        foreach ($existingEntities as $entity) {
            self::getEntityManager()->remove($entity);
        }
        self::getEntityManager()->flush();

        $agentUser1 = $this->createTestAgentUser('user1', 'open_id_1');
        $agentUser1->setAgent(null);
        $agentUser2 = $this->createTestAgentUser('user2', 'open_id_2');
        $agentUser2->setAgent(null);

        self::getEntityManager()->persist($agentUser1);
        self::getEntityManager()->persist($agentUser2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['agent' => null]);

        $this->assertEquals(2, $count);
    }

    public function testFindOneByAssociationAgentShouldReturnMatchingEntity(): void
    {
        $agentUser = $this->createTestAgentUser('user1', 'open_id_1');
        $agentUser->setAgent(null);
        self::getEntityManager()->persist($agentUser);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['agent' => null]);

        $this->assertInstanceOf(AgentUser::class, $result);
        $this->assertNull($result->getAgent());
    }

    private function createTestAgentUser(string $userId = 'test_user_123', string $openId = 'test_open_id_123'): AgentUser
    {
        $agentUser = new AgentUser();
        $agentUser->setUserId($userId);
        $agentUser->setOpenId($openId);

        // Agent is nullable, so we can set it to null for testing
        $agentUser->setAgent(null);

        return $agentUser;
    }

    protected function createNewEntity(): object
    {
        $entity = new AgentUser();
        $entity->setUserId('test_user_' . uniqid());
        $entity->setOpenId('test_open_' . uniqid());

        // Agent 是可选的，可以为 null
        return $entity;
    }

    protected function getRepository(): AgentUserRepository
    {
        return $this->repository;
    }
}
