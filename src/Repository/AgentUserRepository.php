<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatWorkStaffBundle\Entity\AgentUser;

/**
 * @extends ServiceEntityRepository<AgentUser>
 */
#[AsRepository(entityClass: AgentUser::class)]
class AgentUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgentUser::class);
    }

    public function createNewEntity(): AgentUser
    {
        $entity = new AgentUser();
        $entity->setUserId('test-user-' . uniqid());

        return $entity;
    }

    public function save(AgentUser $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AgentUser $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
