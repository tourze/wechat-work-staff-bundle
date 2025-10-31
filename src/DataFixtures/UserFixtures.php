<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkStaffBundle\Entity\User;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUserId('z_fixture_user_001');
        $user->setName('测试用户');

        // 需要创建关联的 Corp 和 Agent 实体
        $corp = new Corp();
        $corp->setCorpId('fixture_corp_001');
        $corp->setName('测试企业');
        $corp->setCorpSecret('fixture_corp_secret_001');
        $manager->persist($corp);

        $agent = new Agent();
        $agent->setAgentId('fixture_agent_001');
        $agent->setName('测试应用');
        $agent->setSecret('fixture_agent_secret_001');
        $agent->setCorp($corp);
        $manager->persist($agent);

        $user->setCorp($corp);
        $user->setAgent($agent);

        $manager->persist($user);
        $manager->flush();
    }
}
