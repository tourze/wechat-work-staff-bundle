<?php

namespace WechatWorkStaffBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserTagRepository;
use WechatWorkStaffBundle\Request\User\GetTagUsersRequest;

#[AsCronTask('*/20 * * * *')]
#[AsCommand(name: self::NAME, description: '同步获取标签成员')]
class SyncTagUsersCommand extends Command
{
    public const NAME = 'wechat-work:sync-tag-users';

    public function __construct(
        private readonly AgentRepository $agentRepository,
        private readonly UserTagRepository $userTagRepository,
        private readonly UserLoaderInterface $userLoader,
        private readonly WorkService $workService,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->agentRepository->findAll() as $agent) {
            foreach ($this->userTagRepository->findBy(['corp' => $agent->getCorp()]) as $tag) {
                $request = new GetTagUsersRequest();
                $request->setAgent($agent);
                $request->setTagId((string) $tag->getTagId());
                $response = $this->workService->request($request);

                if (isset($response['tagname']) && $response['tagname'] !== $tag->getName()) {
                    $tag->setName($response['tagname']);
                    $this->entityManager->persist($tag);
                    $this->entityManager->flush();
                }

                if (isset($response['userlist'])) {
                    $users = new ArrayCollection();
                    foreach ($response['userlist'] as $item) {
                        $user = $this->userLoader->loadUserByUserIdAndCorp($item['userid'], $agent->getCorp());
                        if (!$user instanceof User) {
                            $user = new User();
                            $user->setCorp($agent->getCorp());
                            $user->setUserId($item['userid']);
                        }
                        if (isset($item['name']) && $item['name'] !== $user->getName()) {
                            $user->setName($item['name']);
                            $this->entityManager->persist($user);
                            $this->entityManager->flush();
                        }
                        $users->add($user);
                    }
                    $tag->replaceUsers($users);
                    $this->entityManager->persist($tag);
                    $this->entityManager->flush();
                }
            }
        }

        return Command::SUCCESS;
    }
}
