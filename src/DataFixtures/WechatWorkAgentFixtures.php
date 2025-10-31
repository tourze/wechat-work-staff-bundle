<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

class WechatWorkAgentFixtures extends Fixture implements DependentFixtureInterface
{
    public const WECHAT_WORK_AGENT_REFERENCE = 'wechat-work-agent';

    public function load(ObjectManager $manager): void
    {
        $agent = new Agent();
        $agent->setName('Test Agent');
        $agent->setAgentId('test-agent');
        $agent->setSecret('test-agent-secret-123456');
        $agent->setWelcomeText('Welcome to Test Agent');

        // 关联到测试企业
        if ($this->hasReference(WechatWorkCorpFixtures::WECHAT_WORK_CORP_REFERENCE, Corp::class)) {
            $corp = $this->getReference(WechatWorkCorpFixtures::WECHAT_WORK_CORP_REFERENCE, Corp::class);
            $agent->setCorp($corp);
        }

        $manager->persist($agent);
        $manager->flush();

        $this->addReference(self::WECHAT_WORK_AGENT_REFERENCE, $agent);
    }

    public function getDependencies(): array
    {
        return [
            WechatWorkCorpFixtures::class,
        ];
    }
}
