<?php

namespace WechatWorkStaffBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Entity\UserTag;
use WechatWorkStaffBundle\Request\Tag\GetTagListRequest;

#[AsCronTask('30 */8 * * *')]
#[AsCommand(name: self::NAME, description: '同步获取成员标签')]
class SyncUserTagsCommand extends Command
{
    public const NAME = 'wechat-work:sync-user-tags';

    public function __construct(
        private readonly AgentRepository $agentRepository,
        private readonly WorkService $workService,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->agentRepository->findAll() as $agent) {
            $request = new GetTagListRequest();
            $request->setAgent($agent);
            $response = $this->workService->request($request);
            if (!isset($response['taglist'])) {
                continue;
            }
            foreach ($response['taglist'] as $item) {
                $tag = new UserTag();
                $tag->setAgent($agent);
                $tag->setCorp($agent->getCorp());
                $tag->setTagId($item['tagid']);
                $tag->setName($item['tagname']);
                $this->entityManager->persist($tag);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
