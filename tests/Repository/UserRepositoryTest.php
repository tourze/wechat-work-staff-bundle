<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;

/**
 * @internal
 */
#[CoversClass(UserRepository::class)]
#[RunTestsInSeparateProcesses]
final class UserRepositoryTest extends AbstractRepositoryTestCase
{
    private UserRepository $repository;

    protected function onSetUp(): void
    {
        $container = self::getContainer();
        $repository = $container->get(UserRepository::class);
        self::assertInstanceOf(UserRepository::class, $repository);
        $this->repository = $repository;
    }

    public function testUserEntityCanBeInstantiated(): void
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user);

        // 测试基本属性设置
        $user->setUserId('test_repository_user');
        $this->assertEquals('test_repository_user', $user->getUserId());

        $user->setName('Repository测试用户');
        $this->assertEquals('Repository测试用户', $user->getName());
    }

    public function testUserEntityFieldOperations(): void
    {
        $user = new User();

        // 测试各种字段的设置和获取
        $user->setAlias('测试别名');
        $this->assertEquals('测试别名', $user->getAlias());

        $user->setPosition('测试职位');
        $this->assertEquals('测试职位', $user->getPosition());

        $user->setMobile('13800138000');
        $this->assertEquals('13800138000', $user->getMobile());

        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $user->getEmail());

        $user->setAvatarUrl('https://example.com/avatar.jpg');
        $this->assertEquals('https://example.com/avatar.jpg', $user->getAvatarUrl());

        $user->setOpenUserId('open_user_id_123');
        $this->assertEquals('open_user_id_123', $user->getOpenUserId());
    }

    public function testUserEntityStringRepresentation(): void
    {
        $user = new User();
        $user->setUserId('stringable_test');
        $user->setName('可字符串化测试');

        // 测试字符串转换功能
        $stringRepresentation = (string) $user;
        $this->assertIsString($stringRepresentation);
    }

    public function testFindOneByWithNullCriteriaShouldReturnEntity(): void
    {
        $user = $this->createTestUser();
        $user->setAgent(null); // Set agent to null for IS NULL test
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['agent' => null]);

        $this->assertInstanceOf(User::class, $result);
        $this->assertNull($result->getAgent());
    }

    public function testLoadUserByUserIdAndCorpShouldReturnUser(): void
    {
        $user = $this->createTestUser();
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $corp = $user->getCorp();
        if (null === $corp) {
            self::fail('Test user corp should not be null');
        }

        $result = $this->repository->loadUserByUserIdAndCorp('test_user_123', $corp);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('test_user_123', $result->getUserId());
    }

    public function testLoadUserByUserIdAndCorpWithNonExistentUserShouldReturnNull(): void
    {
        // Create a real Corp entity instead of a mock for Doctrine queries
        $testCorp = new Corp();
        $testCorp->setName('Test Corp for Non-Existent User');
        $testCorp->setCorpId('test_corp_non_existent');
        $testCorp->setCorpSecret('test_corp_secret_non_existent');
        self::getEntityManager()->persist($testCorp);
        self::getEntityManager()->flush();

        $result = $this->repository->loadUserByUserIdAndCorp('non_existent_user', $testCorp);

        $this->assertNull($result);
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $user = $this->createTestUser();

        $this->repository->save($user, false);

        // Entity should be managed but not yet in database
        $this->assertTrue(self::getEntityManager()->contains($user));

        // Flush manually to persist
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->find($user->getId());
        $this->assertInstanceOf(User::class, $foundEntity);
    }

    public function testRemoveWithFlushShouldDeleteEntity(): void
    {
        $user = $this->createTestUser();
        $this->repository->save($user);
        $entityId = $user->getId();

        $this->repository->remove($user);

        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $user = $this->createTestUser();
        $this->repository->save($user);
        $entityId = $user->getId();

        $this->repository->remove($user, false);

        // Entity should still exist in database until flush
        $foundEntity = $this->repository->find($entityId);
        $this->assertInstanceOf(User::class, $foundEntity);

        // Flush to complete deletion
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    public function testFindOneByWithOrderByShouldReturnCorrectEntity(): void
    {
        $user1 = $this->createTestUser('b_user', 'B User');
        $user2 = $this->createTestUser('a_user', 'A User');

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy([], ['userId' => 'ASC']);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('a_user', $result->getUserId());
    }

    public function testFindByWithAssociationCriteriaShouldReturnCorrectEntities(): void
    {
        $user = $this->createTestUser();
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['corp' => $user->getCorp()]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals($user->getId(), $result[0]->getId());
    }

    public function testCountWithAssociationCriteriaShouldReturnCorrectNumber(): void
    {
        $user = $this->createTestUser();
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['corp' => $user->getCorp()]);

        $this->assertEquals(1, $count);
    }

    public function testFindOneByWithSortingLogicShouldReturnCorrectEntity(): void
    {
        // Clear existing data to avoid interference with sorting
        self::getEntityManager()->createQuery('DELETE FROM ' . User::class)->execute();

        $user1 = $this->createTestUser('c_user', 'C User');
        $user2 = $this->createTestUser('a_user', 'A User');
        $user3 = $this->createTestUser('b_user', 'B User');

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->persist($user3);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy([], ['userId' => 'ASC']);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('a_user', $result->getUserId());

        $resultDesc = $this->repository->findOneBy([], ['userId' => 'DESC']);

        $this->assertInstanceOf(User::class, $resultDesc);
        $this->assertEquals('c_user', $resultDesc->getUserId());
    }

    public function testCreateUserShouldCreateAndPersistNewUser(): void
    {
        $testCorp = new Corp();
        $testCorp->setName('Test Corp for Create');
        $testCorp->setCorpId('test_corp_create_123');
        $testCorp->setCorpSecret('test_corp_secret_create_123');
        self::getEntityManager()->persist($testCorp);

        $testAgent = new Agent();
        $testAgent->setName('Test Agent for Create');
        $testAgent->setAgentId('test_agent_create_123');
        $testAgent->setSecret('test_agent_secret_create_123');
        $testAgent->setCorp($testCorp);
        self::getEntityManager()->persist($testAgent);

        $user = $this->repository->createUser($testCorp, $testAgent, 'created_user_123', 'Created User');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('created_user_123', $user->getUserId());
        $this->assertEquals('Created User', $user->getName());
        $this->assertEquals($testCorp, $user->getCorp());
        $this->assertEquals($testAgent, $user->getAgent());
        $this->assertNotNull($user->getId());
    }

    public function testFindByWithDepartmentAssociationShouldReturnCorrectEntities(): void
    {
        // Create shared corp and agent for both users
        $testCorp = new Corp();
        $testCorp->setName('Shared Corp');
        $testCorp->setCorpId('shared_corp_id');
        $testCorp->setCorpSecret('shared_corp_secret');
        self::getEntityManager()->persist($testCorp);

        $testAgent = new Agent();
        $testAgent->setName('Shared Agent');
        $testAgent->setAgentId('shared_agent_id');
        $testAgent->setSecret('shared_agent_secret');
        $testAgent->setCorp($testCorp);
        self::getEntityManager()->persist($testAgent);

        $user1 = new User();
        $user1->setUserId('user1');
        $user1->setName('User 1');
        $user1->setCorp($testCorp);
        $user1->setAgent($testAgent);

        $user2 = new User();
        $user2->setUserId('user2');
        $user2->setName('User 2');
        $user2->setCorp($testCorp);
        $user2->setAgent($testAgent);

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['corp' => $testCorp]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $entity) {
            /** @var User $entity */
            $this->assertEquals($testCorp, $entity->getCorp());
        }
    }

    public function testCountWithDepartmentAssociationShouldReturnCorrectNumber(): void
    {
        // Create shared corp and agent for both users
        $testCorp = new Corp();
        $testCorp->setName('Shared Corp 2');
        $testCorp->setCorpId('shared_corp_id_2');
        $testCorp->setCorpSecret('shared_corp_secret_2');
        self::getEntityManager()->persist($testCorp);

        $testAgent = new Agent();
        $testAgent->setName('Shared Agent 2');
        $testAgent->setAgentId('shared_agent_id_2');
        $testAgent->setSecret('shared_agent_secret_2');
        $testAgent->setCorp($testCorp);
        self::getEntityManager()->persist($testAgent);

        $user1 = new User();
        $user1->setUserId('user3');
        $user1->setName('User 3');
        $user1->setCorp($testCorp);
        $user1->setAgent($testAgent);

        $user2 = new User();
        $user2->setUserId('user4');
        $user2->setName('User 4');
        $user2->setCorp($testCorp);
        $user2->setAgent($testAgent);

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['corp' => $testCorp]);

        $this->assertEquals(2, $count);
    }

    public function testFindByWithNullEmailShouldReturnUsersWithoutEmail(): void
    {
        // Clear existing data to avoid interference
        self::getEntityManager()->createQuery('DELETE FROM ' . User::class)->execute();

        $user1 = $this->createTestUser('user1', 'User 1');
        $user1->setEmail(null);
        $user2 = $this->createTestUser('user2', 'User 2');
        $user2->setEmail('user2@test.com');

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['email' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        /** @var User $user */
        $user = $result[0];
        $this->assertNull($user->getEmail());
    }

    public function testCountWithNullEmailShouldReturnCorrectNumber(): void
    {
        // Clear existing data to avoid interference
        self::getEntityManager()->createQuery('DELETE FROM ' . User::class)->execute();

        $user1 = $this->createTestUser('user1', 'User 1');
        $user1->setEmail(null);
        $user2 = $this->createTestUser('user2', 'User 2');
        $user2->setEmail('user2@test.com');

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['email' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByWithNullMobileShouldReturnUsersWithoutMobile(): void
    {
        // Clear existing data to avoid interference
        self::getEntityManager()->createQuery('DELETE FROM ' . User::class)->execute();

        $user1 = $this->createTestUser('user1', 'User 1');
        $user1->setMobile(null);
        $user2 = $this->createTestUser('user2', 'User 2');
        $user2->setMobile('13800138002');

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['mobile' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        /** @var User $user */
        $user = $result[0];
        $this->assertNull($user->getMobile());
    }

    public function testCountWithNullMobileShouldReturnCorrectNumber(): void
    {
        // Clear existing data to avoid interference
        self::getEntityManager()->createQuery('DELETE FROM ' . User::class)->execute();

        $user1 = $this->createTestUser('user1', 'User 1');
        $user1->setMobile(null);
        $user2 = $this->createTestUser('user2', 'User 2');
        $user2->setMobile('13800138002');

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['mobile' => null]);

        $this->assertEquals(1, $count);
    }

    public function testCountByAssociationCorpShouldReturnCorrectNumber(): void
    {
        // Create shared corp and agent for both users
        $testCorp = new Corp();
        $testCorp->setName('Count Corp');
        $testCorp->setCorpId('count_corp_id');
        $testCorp->setCorpSecret('count_corp_secret');
        self::getEntityManager()->persist($testCorp);

        $testAgent = new Agent();
        $testAgent->setName('Count Agent');
        $testAgent->setAgentId('count_agent_id');
        $testAgent->setSecret('count_agent_secret');
        $testAgent->setCorp($testCorp);
        self::getEntityManager()->persist($testAgent);

        $user1 = new User();
        $user1->setUserId('count_user1');
        $user1->setName('Count User 1');
        $user1->setCorp($testCorp);
        $user1->setAgent($testAgent);

        $user2 = new User();
        $user2->setUserId('count_user2');
        $user2->setName('Count User 2');
        $user2->setCorp($testCorp);
        $user2->setAgent($testAgent);

        self::getEntityManager()->persist($user1);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['corp' => $testCorp]);

        $this->assertEquals(2, $count);
    }

    public function testFindOneByAssociationCorpShouldReturnMatchingEntity(): void
    {
        // Create a specific corp for this test
        $testCorp = new Corp();
        $testCorp->setName('Specific Corp');
        $testCorp->setCorpId('specific_corp_id');
        $testCorp->setCorpSecret('specific_corp_secret');
        self::getEntityManager()->persist($testCorp);

        $testAgent = new Agent();
        $testAgent->setName('Specific Agent');
        $testAgent->setAgentId('specific_agent_id');
        $testAgent->setSecret('specific_agent_secret');
        $testAgent->setCorp($testCorp);
        self::getEntityManager()->persist($testAgent);

        $user = new User();
        $user->setUserId('specific_user');
        $user->setName('Specific User');
        $user->setCorp($testCorp);
        $user->setAgent($testAgent);

        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['corp' => $testCorp]);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($testCorp, $result->getCorp());
    }

    private function createTestUser(string $userId = 'test_user_123', string $name = 'Test User'): User
    {
        $user = new User();
        $user->setUserId($userId);
        $user->setName($name);

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

        $user->setCorp($testCorp);
        $user->setAgent($testAgent);

        return $user;
    }

    protected function createNewEntity(): object
    {
        return $this->createTestUser('Test User ' . uniqid());
    }

    protected function getRepository(): UserRepository
    {
        return $this->repository;
    }
}
