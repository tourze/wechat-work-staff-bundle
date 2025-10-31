<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use WechatWorkBundle\DataFixtures\AgentFixtures;
use WechatWorkBundle\Entity\Agent;
use WechatWorkStaffBundle\Entity\Department;

class DepartmentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $department = new Department();
        $department->setName('测试部门');

        // 关联到代理应用
        if ($this->hasReference(AgentFixtures::AGENT_1_REFERENCE, Agent::class)) {
            $agent = $this->getReference(AgentFixtures::AGENT_1_REFERENCE, Agent::class);
            $department->setAgent($agent);
        }

        $manager->persist($department);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AgentFixtures::class,
        ];
    }
}
