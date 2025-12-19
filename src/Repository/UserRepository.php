<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkStaffBundle\Entity\User;

/**
 * @extends ServiceEntityRepository<User>
 */
#[AsAlias(id: UserLoaderInterface::class)]
#[AsRepository(entityClass: User::class)]
final class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function loadUserByUserIdAndCorp(string $userId, CorpInterface $corp): ?UserInterface
    {
        $user = $this->findOneBy(['userId' => $userId, 'corp' => $corp]);

        return $user instanceof UserInterface ? $user : null;
    }

    public function createUser(CorpInterface $corp, AgentInterface $agent, string $userId, string $name): UserInterface
    {
        $user = new User();
        $user->setCorp($corp);
        $user->setAgent($agent);
        $user->setUserId($userId);
        $user->setName($name);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    public function save(User $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
