<?php

namespace WechatWorkStaffBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkStaffBundle\Entity\User;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
#[AsAlias(UserLoaderInterface::class)]
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function loadUserByUserIdAndCorp(string $userId, CorpInterface $corp): ?UserInterface
    {
        return $this->findOneBy(['userId' => $userId, 'corp' => $corp]);
    }
}
