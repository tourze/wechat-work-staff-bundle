<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkStaffBundle\Entity\UserTag;
use WechatWorkStaffBundle\Repository\UserTagRepository;

/**
 * @internal
 */
#[CoversClass(UserTagRepository::class)]
#[RunTestsInSeparateProcesses]
final class UserTagRepositoryTest extends AbstractRepositoryTestCase
{
    private UserTagRepository $repository;

    protected function onSetUp(): void
    {
        $container = self::getContainer();
        $repository = $container->get(UserTagRepository::class);
        self::assertInstanceOf(UserTagRepository::class, $repository);
        $this->repository = $repository;
    }

    public function testRepositoryIsServiceEntityRepository(): void
    {
        $this->assertNotNull($this->repository);
    }

    public function testFindOneByWithNullCriteriaShouldReturnEntity(): void
    {
        $userTag = $this->createTestUserTag();
        $userTag->setAgent(null); // Set agent to null for IS NULL test
        self::getEntityManager()->persist($userTag);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['agent' => null]);

        $this->assertInstanceOf(UserTag::class, $result);
        $this->assertNull($result->getAgent());
    }

    public function testFindOneByWithAssociationCriteriaShouldReturnEntity(): void
    {
        $mockCorp = $this->createTestCorp('test1');

        $userTag = $this->createTestUserTag();
        $userTag->setCorp($mockCorp);
        self::getEntityManager()->persist($userTag);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['corp' => $mockCorp]);

        $this->assertInstanceOf(UserTag::class, $result);
        $this->assertEquals($mockCorp, $result->getCorp());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $userTag = $this->createTestUserTag();

        $this->repository->save($userTag, false);

        // Entity should be managed but not yet in database
        $this->assertTrue(self::getEntityManager()->contains($userTag));

        // Flush manually to persist
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->find($userTag->getId());
        $this->assertInstanceOf(UserTag::class, $foundEntity);
    }

    public function testRemoveWithFlushShouldDeleteEntity(): void
    {
        $userTag = $this->createTestUserTag();
        $this->repository->save($userTag);
        $entityId = $userTag->getId();

        $this->repository->remove($userTag);

        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $userTag = $this->createTestUserTag();
        $this->repository->save($userTag);
        $entityId = $userTag->getId();

        $this->repository->remove($userTag, false);

        // Entity should still exist in database until flush
        $foundEntity = $this->repository->find($entityId);
        $this->assertInstanceOf(UserTag::class, $foundEntity);

        // Flush to complete deletion
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    public function testCountWithAssociationCriteriaShouldReturnCorrectNumber(): void
    {
        $mockCorp1 = $this->createTestCorp('test2');
        $mockCorp2 = $this->createTestCorp('test3');

        $userTag1 = $this->createTestUserTag('Tag 1');
        $userTag1->setCorp($mockCorp1);
        $userTag2 = $this->createTestUserTag('Tag 2');
        $userTag2->setCorp($mockCorp1);
        $userTag3 = $this->createTestUserTag('Tag 3');
        $userTag3->setCorp($mockCorp2);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['corp' => $mockCorp1]);

        $this->assertEquals(2, $count);
    }

    public function testFindByWithAssociationCriteriaShouldReturnCorrectEntities(): void
    {
        $mockCorp1 = $this->createTestCorp('test4');
        $mockCorp2 = $this->createTestCorp('test5');

        $userTag1 = $this->createTestUserTag('Tag 1');
        $userTag1->setCorp($mockCorp1);
        $userTag2 = $this->createTestUserTag('Tag 2');
        $userTag2->setCorp($mockCorp1);
        $userTag3 = $this->createTestUserTag('Tag 3');
        $userTag3->setCorp($mockCorp2);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['corp' => $mockCorp1]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $entity) {
            /** @var UserTag $entity */
            $this->assertEquals($mockCorp1, $entity->getCorp());
        }
    }

    public function testFindByWithNullCriteriaShouldReturnCorrectEntities(): void
    {
        $userTag1 = $this->createTestUserTag('Tag 1');
        $userTag1->setAgent(null);
        $userTag2 = $this->createTestUserTag('Tag 2');
        $userTag2->setAgent(null);
        $userTag3 = $this->createTestUserTag('Tag 3');
        // userTag3 has agent set by default

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['agent' => null]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $entity) {
            /** @var UserTag $entity */
            $this->assertNull($entity->getAgent());
        }
    }

    public function testFindOneByWithOrderByShouldReturnCorrectEntity(): void
    {
        // Clear existing data to avoid interference with sorting
        self::getEntityManager()->createQuery('DELETE FROM ' . UserTag::class)->execute();

        $userTag1 = $this->createTestUserTag('B Tag');
        $userTag1->setTagId(200);
        $userTag2 = $this->createTestUserTag('A Tag');
        $userTag2->setTagId(100);
        $userTag3 = $this->createTestUserTag('C Tag');
        $userTag3->setTagId(300);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy([], ['name' => 'ASC']);

        $this->assertInstanceOf(UserTag::class, $result);
        $this->assertEquals('A Tag', $result->getName());

        $resultDesc = $this->repository->findOneBy([], ['tagId' => 'DESC']);

        $this->assertInstanceOf(UserTag::class, $resultDesc);
        $this->assertEquals(300, $resultDesc->getTagId());
    }

    public function testFindOneByWithMultipleCriteriaShouldMatchAllConditions(): void
    {
        $userTag1 = $this->createTestUserTag('Test Tag');
        $userTag1->setTagId(123);
        $userTag2 = $this->createTestUserTag('Test Tag');
        $userTag2->setTagId(456);
        $userTag3 = $this->createTestUserTag('Other Tag');
        $userTag3->setTagId(123);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['name' => 'Test Tag', 'tagId' => 123]);

        $this->assertInstanceOf(UserTag::class, $result);
        $this->assertEquals('Test Tag', $result->getName());
        $this->assertEquals(123, $result->getTagId());
    }

    public function testFindByWithTagIdCriteriaShouldReturnCorrectEntities(): void
    {
        $userTag1 = $this->createTestUserTag('Tag 1');
        $userTag1->setTagId(100);
        $userTag2 = $this->createTestUserTag('Tag 2');
        $userTag2->setTagId(100);
        $userTag3 = $this->createTestUserTag('Tag 3');
        $userTag3->setTagId(200);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['tagId' => 100]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $entity) {
            /** @var UserTag $entity */
            $this->assertEquals(100, $entity->getTagId());
        }
    }

    public function testCountWithTagIdCriteriaShouldReturnCorrectNumber(): void
    {
        $userTag1 = $this->createTestUserTag('Tag 1');
        $userTag1->setTagId(100);
        $userTag2 = $this->createTestUserTag('Tag 2');
        $userTag2->setTagId(100);
        $userTag3 = $this->createTestUserTag('Tag 3');
        $userTag3->setTagId(200);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['tagId' => 100]);

        $this->assertEquals(2, $count);
    }

    public function testFindByWithComplexCriteriaShouldReturnCorrectEntities(): void
    {
        $mockCorp = $this->createTestCorp('test6');

        $userTag1 = $this->createTestUserTag('Test Tag');
        $userTag1->setTagId(100);
        $userTag1->setCorp($mockCorp);
        $userTag1->setAgent(null);

        $userTag2 = $this->createTestUserTag('Test Tag');
        $userTag2->setTagId(200);
        $userTag2->setCorp($mockCorp);
        $userTag2->setAgent(null);

        $userTag3 = $this->createTestUserTag('Other Tag');
        $userTag3->setTagId(300);  // Use different tagId to avoid unique constraint violation
        $userTag3->setCorp($mockCorp);
        $userTag3->setAgent(null);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy([
            'name' => 'Test Tag',
            'corp' => $mockCorp,
            'agent' => null,
        ]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $entity) {
            /** @var UserTag $entity */
            $this->assertEquals('Test Tag', $entity->getName());
            $this->assertEquals($mockCorp, $entity->getCorp());
            $this->assertNull($entity->getAgent());
        }
    }

    public function testFindOneByWithSortingLogicShouldReturnCorrectEntity(): void
    {
        // Clear existing data to avoid interference with sorting
        self::getEntityManager()->createQuery('DELETE FROM ' . UserTag::class)->execute();

        $userTag1 = $this->createTestUserTag('Z Tag');
        $userTag1->setTagId(300);
        $userTag2 = $this->createTestUserTag('A Tag');
        $userTag2->setTagId(100);
        $userTag3 = $this->createTestUserTag('M Tag');
        $userTag3->setTagId(200);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy([], ['name' => 'ASC']);

        $this->assertInstanceOf(UserTag::class, $result);
        $this->assertEquals('A Tag', $result->getName());

        $resultDesc = $this->repository->findOneBy([], ['tagId' => 'DESC']);

        $this->assertInstanceOf(UserTag::class, $resultDesc);
        $this->assertEquals(300, $resultDesc->getTagId());
    }

    public function testFindByWithCorpAssociationShouldReturnCorrectEntities(): void
    {
        $mockCorp1 = $this->createTestCorp('test7');
        $mockCorp2 = $this->createTestCorp('test8');

        $userTag1 = $this->createTestUserTag('Tag 1');
        $userTag1->setCorp($mockCorp1);
        $userTag2 = $this->createTestUserTag('Tag 2');
        $userTag2->setCorp($mockCorp1);
        $userTag3 = $this->createTestUserTag('Tag 3');
        $userTag3->setCorp($mockCorp2);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['corp' => $mockCorp1]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $entity) {
            /** @var UserTag $entity */
            $this->assertEquals($mockCorp1, $entity->getCorp());
        }
    }

    public function testCountWithCorpAssociationShouldReturnCorrectNumber(): void
    {
        $mockCorp1 = $this->createTestCorp('test9');
        $mockCorp2 = $this->createTestCorp('test10');

        $userTag1 = $this->createTestUserTag('Tag 1');
        $userTag1->setCorp($mockCorp1);
        $userTag2 = $this->createTestUserTag('Tag 2');
        $userTag2->setCorp($mockCorp1);
        $userTag3 = $this->createTestUserTag('Tag 3');
        $userTag3->setCorp($mockCorp2);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['corp' => $mockCorp1]);

        $this->assertEquals(2, $count);
    }

    public function testFindByWithNullAgentShouldReturnCorrectEntities(): void
    {
        $userTag1 = $this->createTestUserTag('Tag 1');
        $userTag1->setAgent(null);
        $userTag2 = $this->createTestUserTag('Tag 2');
        $userTag2->setAgent(null);
        $userTag3 = $this->createTestUserTag('Tag 3');
        // userTag3 has agent set by default

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['agent' => null]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $entity) {
            /** @var UserTag $entity */
            $this->assertNull($entity->getAgent());
        }
    }

    public function testCountWithNullAgentShouldReturnCorrectNumber(): void
    {
        $userTag1 = $this->createTestUserTag('Tag 1');
        $userTag1->setAgent(null);
        $userTag2 = $this->createTestUserTag('Tag 2');
        $userTag2->setAgent(null);
        $userTag3 = $this->createTestUserTag('Tag 3');
        // userTag3 has agent set by default

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->persist($userTag3);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['agent' => null]);

        $this->assertEquals(2, $count);
    }

    public function testCountByAssociationCorpShouldReturnCorrectNumber(): void
    {
        $mockCorp = $this->createTestCorp('test11');

        $userTag1 = $this->createTestUserTag('Tag 1');
        $userTag1->setCorp($mockCorp);
        $userTag2 = $this->createTestUserTag('Tag 2');
        $userTag2->setCorp($mockCorp);

        self::getEntityManager()->persist($userTag1);
        self::getEntityManager()->persist($userTag2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['corp' => $mockCorp]);

        $this->assertEquals(2, $count);
    }

    public function testFindOneByAssociationCorpShouldReturnMatchingEntity(): void
    {
        $mockCorp = $this->createTestCorp('test12');

        $userTag = $this->createTestUserTag('Tag 1');
        $userTag->setCorp($mockCorp);
        self::getEntityManager()->persist($userTag);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['corp' => $mockCorp]);

        $this->assertInstanceOf(UserTag::class, $result);
        $this->assertEquals($mockCorp, $result->getCorp());
    }

    private function createTestUserTag(string $name = 'Test Tag'): UserTag
    {
        $userTag = new UserTag();
        $userTag->setName($name);
        $userTag->setTagId(rand(1000, 8888)); // Use random tagId to avoid unique constraint violations

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

        $userTag->setCorp($testCorp);
        $userTag->setAgent($testAgent);

        return $userTag;
    }

    protected function createNewEntity(): object
    {
        return $this->createTestUserTag('Test UserTag ' . uniqid());
    }

    protected function getRepository(): UserTagRepository
    {
        return $this->repository;
    }

    private function createTestCorp(string $suffix = ''): Corp
    {
        $uniqueId = '' !== $suffix ? $suffix : uniqid();
        $corp = new Corp();
        $corp->setName('Test Corp ' . $uniqueId);
        $corp->setCorpId('test_corp_id_' . $uniqueId);
        $corp->setCorpSecret('test_corp_secret_' . $uniqueId);
        self::getEntityManager()->persist($corp);
        self::getEntityManager()->flush();

        return $corp;
    }
}
