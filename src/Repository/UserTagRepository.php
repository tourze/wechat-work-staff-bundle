<?php

namespace WechatWorkStaffBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkStaffBundle\Entity\UserTag;

/**
 * @method UserTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTag[]    findAll()
 * @method UserTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTag::class);
    }
}
