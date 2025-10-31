<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatWorkStaffBundle\Entity\AgentUser;

class AgentUserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 3; ++$i) {
            $agentUser = new AgentUser();
            $agentUser->setUserId('user-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT));
            $agentUser->setOpenId('openid-' . uniqid());
            $agentUser->setCreatedFromIp('127.0.0.1');
            $agentUser->setUpdatedFromIp('127.0.0.1');
            $agentUser->setCreateTime(new \DateTimeImmutable());
            $agentUser->setUpdateTime(new \DateTimeImmutable());

            $manager->persist($agentUser);
        }

        $manager->flush();
    }
}
