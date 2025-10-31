<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkStaffBundle\Entity\UserTag;
use WechatWorkStaffBundle\Repository\UserTagRepository;
use WechatWorkStaffBundle\Request\Tag\GetTagListRequest;

#[Autoconfigure(public: true)]
#[AsCronTask(expression: '30 */8 * * *')]
#[AsCommand(name: self::NAME, description: '同步获取成员标签')]
class SyncUserTagsCommand extends Command
{
    public const NAME = 'wechat-work:sync-user-tags';

    public function __construct(
        private readonly ?AgentRepository $agentRepository = null,
        private readonly ?WorkServiceInterface $workService = null,
        private readonly ?EntityManagerInterface $entityManager = null,
        private readonly ?UserTagRepository $userTagRepository = null,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null === $this->agentRepository || null === $this->workService || null === $this->entityManager || null === $this->userTagRepository) {
            $output->writeln('<error>Required services not available</error>');

            return Command::FAILURE;
        }

        foreach ($this->agentRepository->findAll() as $agent) {
            $request = new GetTagListRequest();
            $request->setAgent($agent);
            $response = $this->workService->request($request);
            assert(is_array($response));

            if (!isset($response['taglist'])) {
                continue;
            }

            $taglist = $response['taglist'];
            assert(is_array($taglist));

            foreach ($taglist as $item) {
                assert(is_array($item));
                assert(isset($item['tagid']));
                assert(isset($item['tagname']));

                $tagId = $item['tagid'];
                assert(is_int($tagId) || is_string($tagId));
                $tagIdInt = is_int($tagId) ? $tagId : (int) $tagId;

                $tagName = $item['tagname'];
                assert(is_string($tagName));

                $corp = $agent->getCorp();

                // Check if tag already exists for this corp and tagId
                $existingTag = $this->userTagRepository->findOneBy([
                    'corp' => $corp,
                    'tagId' => $tagIdInt,
                ]);

                if (null !== $existingTag) {
                    // Update existing tag name if changed
                    $existingTag->setName($tagName);
                    $this->entityManager->persist($existingTag);
                } else {
                    // Create new tag
                    $tag = new UserTag();
                    $tag->setAgent($agent);
                    $tag->setCorp($corp);
                    $tag->setTagId($tagIdInt);
                    $tag->setName($tagName);
                    $this->entityManager->persist($tag);
                }
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
