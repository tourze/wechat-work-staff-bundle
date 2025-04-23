<?php

namespace WechatWorkStaffBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkStaffBundle\Entity\AgentUser;

/**
 * @method AgentUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgentUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgentUser[]    findAll()
 * @method AgentUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgentUser::class);
    }
}
