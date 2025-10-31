<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkStaffBundle\Entity\UserTag;

class UserTagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $userTag = new UserTag();
        $userTag->setName('测试用户标签');
        $userTag->setTagId(9999);

        // 需要创建关联的 Corp 和 Agent 实体
        $corp = new Corp();
        $corp->setCorpId('fixture_corp_tag_001');
        $corp->setName('测试企业标签');
        $corp->setCorpSecret('fixture_corp_secret_tag_001');
        $manager->persist($corp);

        $agent = new Agent();
        $agent->setAgentId('fixture_agent_tag_001');
        $agent->setName('测试应用标签');
        $agent->setSecret('fixture_agent_secret_tag_001');
        $agent->setCorp($corp);
        $manager->persist($agent);

        $userTag->setCorp($corp);
        $userTag->setAgent($agent);

        $manager->persist($userTag);
        $manager->flush();
    }
}
