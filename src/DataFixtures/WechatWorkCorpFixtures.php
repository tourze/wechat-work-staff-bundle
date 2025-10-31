<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatWorkBundle\Entity\Corp;

class WechatWorkCorpFixtures extends Fixture
{
    public const WECHAT_WORK_CORP_REFERENCE = 'wechat-work-corp';

    public function load(ObjectManager $manager): void
    {
        $corp = new Corp();
        $corp->setName('Test Corporation');
        $corp->setCorpId('test-corp');
        $corp->setCorpSecret('test-secret-key-123456');

        $manager->persist($corp);
        $manager->flush();

        $this->addReference(self::WECHAT_WORK_CORP_REFERENCE, $corp);
    }
}
